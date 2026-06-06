<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Organizer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Display the reports dashboard
     */
    public function index()
    {
        // Overall Statistics
        $totalEvents = Event::count();
        $totalOrganizers = Organizer::count();
        $totalUsers = User::count();
        
        // Events by Status
        $eventsByStatus = Event::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status')
            ->toArray();
        
        // Events by Month (Current Year)
        $eventsByMonth = Event::selectRaw("EXTRACT(MONTH FROM date)::int as month, COUNT(*) as count")
            ->whereYear('date', date('Y'))
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('count', 'month');
        
        // Monthly event data for chart (all 12 months)
        $monthlyEvents = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthlyEvents[] = $eventsByMonth->get($i, 0);
        }
        
        // Top Organizers by Event Count
        $topOrganizers = DB::table('events')
            ->join('organizers', 'events.organizer_id', '=', 'organizers.id')
            ->select('organizers.name', 'organizers.id', DB::raw('COUNT(events.id) as event_count'))
            ->groupBy('organizers.id', 'organizers.name')
            ->orderByDesc('event_count')
            ->limit(5)
            ->get();
        
        // Recent Events
        $recentEvents = Event::with('organizer')
            ->latest()
            ->take(10)
            ->get();
        
        // Upcoming Events
        $upcomingEvents = Event::with('organizer')
            ->where('date', '>', now())
            ->where('status', 'published')
            ->orderBy('date')
            ->take(5)
            ->get();
        
        // Events Location Distribution
        $eventsByLocation = Event::selectRaw('location, COUNT(*) as count')
            ->whereNotNull('location')
            ->groupBy('location')
            ->orderByDesc('count')
            ->limit(5)
            ->get();
        
        return view('backend.pages.reports.index', compact(
            'totalEvents',
            'totalOrganizers',
            'totalUsers',
            'eventsByStatus',
            'monthlyEvents',
            'topOrganizers',
            'recentEvents',
            'upcomingEvents',
            'eventsByLocation'
        ));
    }
    
    /**
     * Event-specific reports
     */
    public function events(Request $request)
    {
        $query = Event::with('organizer');
        
        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Filter by date range
        if ($request->filled('start_date')) {
            $query->where('date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->where('date', '<=', $request->end_date);
        }
        
        // Filter by organizer
        if ($request->filled('organizer_id')) {
            $query->where('organizer_id', $request->organizer_id);
        }
        
        $events = $query->latest()->paginate(20);
        $organizers = Organizer::all();
        
        return view('backend.pages.reports.events', compact('events', 'organizers'));
    }
    
    /**
     * Organizer-specific reports
     */
    public function organizers()
    {
        $organizers = Organizer::withCount('events')
            ->latest()
            ->paginate(20);
        
        return view('backend.pages.reports.organizers', compact('organizers'));
    }
}
