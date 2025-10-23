<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Camera;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $query = Event::with('camera')->orderBy('detected_at', 'desc');

        // Filters
        if ($request->filled('camera_id')) {
            $query->where('camera_id', $request->camera_id);
        }

        if ($request->filled('event_type')) {
            $query->where('event_type', $request->event_type);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('detected_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('detected_at', '<=', $request->date_to);
        }

        $events = $query->paginate(20);
        $cameras = Camera::orderBy('name')->get();

        return view('events.index', compact('events', 'cameras'));
    }

    public function show(Event $event)
    {
        $event->load('camera');
        return view('events.show', compact('event'));
    }

    public function destroy(Event $event)
    {
        // Delete image file if exists
        if ($event->image_path) {
            Storage::disk('public')->delete($event->image_path);
        }

        $event->delete();

        return redirect()->route('events.index')
            ->with('success', 'Event deleted successfully');
    }

    public function stream()
    {
        return response()->stream(function () {
            while (true) {
                // Check for new events in the last 2 seconds
                $events = Event::with('camera')
                    ->where('created_at', '>=', now()->subSeconds(2))
                    ->orderBy('created_at', 'desc')
                    ->get();

                foreach ($events as $event) {
                    $data = [
                        'id' => $event->event_id,
                        'camera' => $event->camera->camera_index,
                        'camera_name' => $event->camera->name,
                        'location' => $event->camera->location,
                        'event' => $event->event_type,
                        'ts' => $event->detected_at->format('Y-m-d H:i:s'),
                        'n_persons' => $event->persons_count,
                        'details' => json_decode($event->details, true),
                        'image_url' => $event->image_url,
                    ];

                    echo "data: " . json_encode($data) . "\n\n";
                    ob_flush();
                    flush();
                }

                // Send keepalive
                echo ": keepalive\n\n";
                ob_flush();
                flush();

                sleep(1);
            }
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
            'X-Accel-Buffering' => 'no',
        ]);
    }
}