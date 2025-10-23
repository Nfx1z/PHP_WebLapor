<?php

namespace App\Http\Controllers;

use App\Models\Camera;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $cameras = Camera::orderBy('camera_index')->get();
        
        $totalCameras = Camera::count();
        $activeCameras = Camera::where('is_active', true)->count();
        $onlineCameras = Camera::where('status', 'online')->count();
        
        $todayEvents = Event::whereDate('detected_at', today())->count();
        $totalEvents = Event::count();
        
        $recentEvents = Event::with('camera')
            ->orderBy('detected_at', 'desc')
            ->limit(10)
            ->get();
        
        $eventsByType = Event::select('event_type', DB::raw('count(*) as count'))
            ->whereDate('detected_at', '>=', now()->subDays(7))
            ->groupBy('event_type')
            ->get()
            ->pluck('count', 'event_type')
            ->toArray();

        return view('dashboard', compact(
            'cameras',
            'totalCameras',
            'activeCameras',
            'onlineCameras',
            'todayEvents',
            'totalEvents',
            'recentEvents',
            'eventsByType'
        ));
    }
}