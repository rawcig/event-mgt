<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GuestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Guest::with('event');
        
        // Filter by search
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }
        
        // Filter by event
        if ($request->has('event_id') && $request->event_id) {
            $query->where('event_id', $request->event_id);
        }
        
        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        // Filter by participation type
        if ($request->has('participation_type') && $request->participation_type) {
            $query->where('participation_type', $request->participation_type);
        }
        
        // Filter by checked in status
        if ($request->has('checked_in') && $request->checked_in !== '') {
            $query->where('checked_in', $request->checked_in);
        }
        
        // Sorting
        $sortBy = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        
        switch ($sortBy) {
            case 'name':
                $query->orderBy('name', $sortDirection);
                break;
            case 'email':
                $query->orderBy('email', $sortDirection);
                break;
            case 'status':
                $query->orderBy('status', $sortDirection);
                break;
            case 'registration_status':
                $query->orderBy('registration_status', $sortDirection);
                break;
            case 'created_at':
            default:
                $query->orderBy('created_at', $sortDirection);
                break;
        }
        
        // Get statistics in a single query (fix N+1 problem)
        $statsQuery = (clone $query)->selectRaw("
            COUNT(*) as total,
            SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) as confirmed,
            SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
            SUM(CASE WHEN checked_in = true THEN 1 ELSE 0 END) as attended
        ")->first();
        
        $guests = $query->paginate(20);
        $events = Event::where('status', 'published')->get();
        
        // Statistics
        $totalGuests = $statsQuery->total ?? 0;
        $confirmedGuests = $statsQuery->confirmed ?? 0;
        $pendingGuests = $statsQuery->pending ?? 0;
        $attendedGuests = $statsQuery->attended ?? 0;
        
        return view('backend.pages.guests.index', compact('guests', 'events', 'totalGuests', 'confirmedGuests', 'pendingGuests', 'attendedGuests'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $events = Event::where('status', 'published')->where('date', '>', now())->get();
        $selectedEventId = $request->query('event_id');
        return view('backend.pages.guests.create', compact('events', 'selectedEventId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'event_id' => 'required|exists:events,id',
            'participation_type' => 'required|in:attendee,speaker,sponsor,volunteer,vip',
            'ticket_count' => 'required|integer|min:1|max:10',
            'company' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'dietary_requirements' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['status'] = 'pending';
        $validated['registration_status'] = 'confirmed';

        Guest::create($validated);

        return redirect()->route('guests.index')->with('success', 'Guest registered successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Guest $guest)
    {
        $guest->load('event');
        return view('backend.pages.guests.show', compact('guest'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Guest $guest)
    {
        $this->authorize('update', $guest);
        $events = Event::all();
        return view('backend.pages.guests.edit', compact('guest', 'events'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Guest $guest)
    {
        $this->authorize('update', $guest);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'event_id' => 'required|exists:events,id',
            'status' => 'required|in:pending,confirmed,declined,attended',
            'ticket_count' => 'required|integer|min:1|max:10',
            'notes' => 'nullable|string',
        ]);

        // Map status to registration_status (convert 'declined' to 'cancelled')
        $status_mapping = [
            'pending' => 'pending',
            'confirmed' => 'confirmed',
            'declined' => 'cancelled',
            'attended' => 'attended',
        ];
        
        $validated['registration_status'] = $status_mapping[$validated['status']];
        $guest->update($validated);

        return redirect()->route('guests.index')->with('success', 'Guest updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Guest $guest)
    {
        $this->authorize('delete', $guest);
        $guest->delete();

        return redirect()->route('guests.index')->with('success', 'Ticket removed successfully!');
    }

    /**
     * Bulk update guest status
     */
    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'guest_ids' => 'required|array',
            'status' => 'required|in:pending,confirmed,declined,attended',
        ]);

        // Map status to registration_status
        $status_mapping = [
            'pending' => 'pending',
            'confirmed' => 'confirmed',
            'declined' => 'cancelled',
            'attended' => 'attended',
        ];
        
        $registration_status = $status_mapping[$request->status];
        Guest::whereIn('id', $request->guest_ids)->update([
            'status' => $request->status,
            'registration_status' => $registration_status
        ]);

        return redirect()->route('guests.index')->with('success', 'Guest status updated successfully!');
    }

    /**
     * Show event guest list for check-in
     */
    public function eventGuests(Event $event)
    {
        $this->authorize('viewGuests', $event);
        $guests = $event->guests()->with('user')->latest()->get();
        
        // Statistics
        $stats = [
            'total' => $guests->count(),
            'confirmed' => $guests->where('status', 'confirmed')->count(),
            'checked_in' => $guests->where('checked_in', true)->count(),
            'not_checked_in' => $guests->where('checked_in', false)->count(),
        ];
        
        return view('backend.pages.guests.event-guests', compact('event', 'guests', 'stats'));
    }

    /**
     * Check in a guest
     */
    public function checkIn(Guest $guest)
    {
        $this->authorize('checkIn', $guest);
        $guest->update([
            'checked_in' => true,
            'checked_in_at' => now(),
        ]);

        return back()->with('success', 'Guest checked in successfully!');
    }

    /**
     * Confirm a guest registration
     */
    public function confirm(Guest $guest)
    {
        $this->authorize('update', $guest);
        $guest->update([
            'status' => 'confirmed',
            'confirmed_at' => now(),
        ]);

        return back()->with('success', 'Guest registration confirmed successfully!');
    }

    /**
     * Check out a guest
     */
    public function checkOut(Guest $guest)
    {
        $this->authorize('checkOut', $guest);
        
        $guest->update([
            'checked_in' => false,
            'checked_in_at' => null,
        ]);

        return back()->with('success', 'Guest checked out successfully!');
    }

    /**
     * Export guest list to CSV
     */
    public function exportGuests(Request $request)
    {
        // Only admins and organizers can access
        if (!auth()->user()->isAdmin() && !auth()->user()->isOrganizer()) {
            abort(403);
        }

        $filename = 'guest-list-' . date('Y-m-d-H-i-s') . '.csv';
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // CSV Header
        fputcsv($output, [
            'ID',
            'Guest Name',
            'Email',
            'Phone',
            'Event',
            'Participation Type',
            'Tickets',
            'Status',
            'Checked In',
            'Check-in Time',
            'Company',
            'Position',
            'Registered At'
        ]);
        
        // Get guests with filters
        $query = Guest::with(['event', 'user']);
        
        if ($request->has('event_id') && $request->event_id) {
            $query->where('event_id', $request->event_id);
        }
        
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        if ($request->has('participation_type') && $request->participation_type) {
            $query->where('participation_type', $request->participation_type);
        }
        
        if ($request->has('checked_in') && $request->checked_in !== '') {
            $query->where('checked_in', $request->checked_in);
        }
        
        $guests = $query->latest()->get();
        
        // CSV Data
        foreach ($guests as $guest) {
            fputcsv($output, [
                $guest->id,
                $guest->name,
                $guest->email,
                $guest->phone ?? 'N/A',
                $guest->event ? $guest->event->title : 'N/A',
                ucfirst($guest->participation_type),
                $guest->ticket_count,
                ucfirst($guest->status),
                $guest->checked_in ? 'Yes' : 'No',
                $guest->checked_in_at ? $guest->checked_in_at->format('Y-m-d H:i:s') : 'N/A',
                $guest->company ?? 'N/A',
                $guest->position ?? 'N/A',
                $guest->created_at->format('Y-m-d H:i:s')
            ]);
        }
        
        fclose($output);
        exit;
    }

    /**
     * Show public registration form
     */
    public function publicRegister(Event $event)
    {
        if ($event->status !== 'published') {
            abort(404);
        }
        
        // Check if event is full
        if ($event->is_full) {
            return redirect()->route('events.public')
                ->with('error', 'This event is sold out!');
        }
        
        // if not logged in, redirect to login with return url
        if (!auth()->check()) {
            return redirect()->route('login')->with('message', 'Please login to register for this event');
        }
        
        // Check if already registered
        $alreadyRegistered = $event->guests()
            ->where('user_id', auth()->id())
            ->exists();
        
        if ($alreadyRegistered) {
            return redirect()->route('my-events')
                ->with('info', 'You are already registered for this event');
        }
        
        return view('frontend.events.register', compact('event'));
    }

    /**
     * Store public registration
     */
    public function publicRegisterStore(Request $request, Event $event)
    {
        // ensure user is logged in
        if (!auth()->check()) {
            return redirect()->route('login')->with('message', 'Please login to complete registration');
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'participation_type' => 'required|in:attendee,speaker,sponsor,volunteer,vip',
            'ticket_count' => 'required|integer|min:1|max:10',
            'company' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'dietary_requirements' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $validated['event_id'] = $event->id;
        $validated['user_id'] = Auth::id();
        $validated['status'] = 'pending';
        $validated['registration_status'] = 'pending';

        // Create guest
        $guest = Guest::create($validated);
        
        // Generate QR code data (unique for each registration)
        $qrData = 'EVENT-' . $event->id . '-GUEST-' . $guest->id . '-' . time();
        
        // Generate QR code URL using QR Server API (free)
        $qrCodeUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' . urlencode($qrData);
        
        // Store QR code URL in database
        $guest->update(['qr_code' => $qrCodeUrl]);

        return redirect()->route('my-events')->with('success', 'Successfully registered for ' . $event->title . '!');
    }

    /**
     * Display user's registered events
     */
    public function myEvents()
    {
        $guests = Guest::where('user_id', Auth::id())
            ->with('event')
            ->latest()
            ->paginate(20);
        
        return view('user.events.my-events', compact('guests'));
    }
}
