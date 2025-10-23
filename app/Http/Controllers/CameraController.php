<?php

namespace App\Http\Controllers;

use App\Models\Camera;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Setting;

class CameraController extends Controller
{
    public function index()
    {
        $cameras = Camera::orderBy('camera_index')->get();
        return view('cameras.index', compact('cameras'));
    }

    public function create()
    {
        $usedIndexes = Camera::pluck('camera_index')->toArray();
        $availableIndex = 0;
        while (in_array($availableIndex, $usedIndexes)) {
            $availableIndex++;
        }
        
        return view('cameras.form', compact('availableIndex'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'rtsp_url' => 'required|string',
            'camera_index' => 'required|integer|unique:cameras,camera_index',
            'is_active' => 'boolean',
        ]);

        $camera = Camera::create($validated);

        // Start camera in Flask API if active
        if ($camera->is_active) {
            $this->startCameraInFlask($camera);
        }

        return redirect()->route('cameras.index')
            ->with('success', 'Camera added successfully');
    }

    public function edit(Camera $camera)
    {
        return view('cameras.form', compact('camera'));
    }

    public function update(Request $request, Camera $camera)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'rtsp_url' => 'required|string',
            'camera_index' => 'required|integer|unique:cameras,camera_index,' . $camera->id,
            'is_active' => 'boolean',
        ]);

        $wasActive = $camera->is_active;
        $camera->update($validated);

        // Handle Flask API start/stop
        if ($camera->is_active && !$wasActive) {
            $this->startCameraInFlask($camera);
        } elseif (!$camera->is_active && $wasActive) {
            $this->stopCameraInFlask($camera);
        } elseif ($camera->is_active) {
            // Restart if config changed
            $this->stopCameraInFlask($camera);
            $this->startCameraInFlask($camera);
        }

        return redirect()->route('cameras.index')
            ->with('success', 'Camera updated successfully');
    }

    public function destroy(Camera $camera)
    {
        if ($camera->is_active) {
            $this->stopCameraInFlask($camera);
        }

        $camera->delete();

        return redirect()->route('cameras.index')
            ->with('success', 'Camera deleted successfully');
    }

    public function toggle(Camera $camera)
    {
        $camera->is_active = !$camera->is_active;
        $camera->save();

        if ($camera->is_active) {
            $this->startCameraInFlask($camera);
        } else {
            $this->stopCameraInFlask($camera);
        }

        return response()->json([
            'success' => true,
            'is_active' => $camera->is_active,
            'status' => $camera->status,
        ]);
    }

    private function startCameraInFlask(Camera $camera)
    {
        try {
            $flaskUrl = Setting::get('flask_api_url', 'http://localhost:5000');
            $detectionSettings = Setting::getGroup('detection');

            $response = Http::timeout(5)->post("{$flaskUrl}/api/start_camera", [
                'camera_index' => $camera->camera_index,
                'rtsp_url' => $camera->rtsp_url,
                'config' => $detectionSettings,
            ]);

            if ($response->successful()) {
                $camera->update(['status' => 'online', 'last_seen' => now()]);
                return true;
            }
        } catch (\Exception $e) {
            \Log::error("Failed to start camera {$camera->id}: " . $e->getMessage());
            $camera->update(['status' => 'error']);
        }
        return false;
    }

    private function stopCameraInFlask(Camera $camera)
    {
        try {
            $flaskUrl = Setting::get('flask_api_url', 'http://localhost:5000');
            
            Http::timeout(5)->post("{$flaskUrl}/api/stop_camera", [
                'camera_index' => $camera->camera_index,
            ]);

            $camera->update(['status' => 'offline']);
        } catch (\Exception $e) {
            \Log::error("Failed to stop camera {$camera->id}: " . $e->getMessage());
        }
    }

    public function videoFeed(Request $request)
    {
        $cameraIndex = $request->query('cam', 0);
        $camera = Camera::where('camera_index', $cameraIndex)->first();

        if (!$camera) {
            return response('Camera not found', 404);
        }

        try {
            $flaskUrl = Setting::get('flask_api_url', 'http://localhost:5000');
            $url = "{$flaskUrl}/video_feed?cam={$cameraIndex}";

            return response()->stream(function () use ($url) {
                $context = stream_context_create([
                    'http' => [
                        'timeout' => 30,
                        'ignore_errors' => true,
                    ]
                ]);

                $stream = @fopen($url, 'r', false, $context);
                
                if ($stream) {
                    while (!feof($stream)) {
                        echo fread($stream, 8192);
                        flush();
                    }
                    fclose($stream);
                }
            }, 200, [
                'Content-Type' => 'multipart/x-mixed-replace; boundary=frame',
                'Cache-Control' => 'no-cache',
                'Connection' => 'keep-alive',
            ]);
        } catch (\Exception $e) {
            return response('Stream error', 503);
        }
    }

    public function snapshot(Request $request)
    {
        $cameraIndex = $request->query('cam', 0);
        
        try {
            $flaskUrl = Setting::get('flask_api_url', 'http://localhost:5000');
            $response = Http::timeout(5)->get("{$flaskUrl}/snapshot", [
                'cam' => $cameraIndex,
            ]);

            if ($response->successful()) {
                return response($response->body(), 200)
                    ->header('Content-Type', 'image/jpeg');
            }
        } catch (\Exception $e) {
            \Log::error("Snapshot error: " . $e->getMessage());
        }

        return response('Snapshot not available', 503);
    }
}