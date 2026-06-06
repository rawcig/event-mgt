<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Organizer;
use App\Http\Requests\CreateEventRequest;

class EventController extends Controller
{
    /**
     * list all events
     */
    public function index(Request $request)
    {
        $query = Event::with('organizer');
        
        // Filter by search
        if ($request->has('search') && $request->search) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }
        
        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        // Sorting
        $sortBy = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        
        switch ($sortBy) {
            case 'title':
                $query->orderBy('title', $sortDirection);
                break;
            case 'date':
                $query->orderBy('date', $sortDirection);
                break;
            case 'status':
                $query->orderBy('status', $sortDirection);
                break;
            case 'created_at':
            default:
                $query->orderBy('created_at', $sortDirection);
                break;
        }
        
        $events = $query->paginate(10);
        return view('backend.pages.events.index', compact('events'));
    }

    /**
     * show create form
     */
    public function create()
    {
        $this->authorize('create', Event::class);
        $organizers = Organizer::all();
        return view('backend.pages.events.create', compact('organizers'));
    }

    /**
     * store new event
     */
    public function store(CreateEventRequest $request)
    {
        $validatedData = $request->validated();

        // remove organizer name from data
        unset($validatedData['organizer']);

        // get organizer id from name
        if ($request->has('organizer') && !empty($request->organizer)) {
            $organizer = Organizer::where('name', $request->organizer)->first();
            if ($organizer) {
                $validatedData['organizer_id'] = $organizer->id;
            }
        }

        // handle cover image upload
        if ($request->hasFile('cover_image')) {
            $image = $request->file('cover_image');
            $imageName = uniqid('cover_') . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('storage/events'), $imageName);
            $validatedData['cover_image'] = 'events/' . $imageName;
        }

        // handle detail image upload
        if ($request->hasFile('detail_image')) {
            $image = $request->file('detail_image');
            $imageName = uniqid('detail_') . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('storage/events'), $imageName);
            $validatedData['detail_image'] = 'events/' . $imageName;
        }

        Event::create($validatedData);

        return redirect()->route('events.index')->with('success', 'event created!');
    }

    /**
     * show event details
     */
    public function show(Event $event)
    {
        $event->load('organizer', 'guests');
        
        // Load admin statistics
        $statistics = [
            'total_registered' => $event->registered_count,
            'confirmed' => $event->confirmed_count,
            'pending' => $event->pending_count,
            'cancelled' => $event->cancelled_count,
            'checked_in' => $event->checked_in_count,
            'not_checked_in' => $event->not_checked_in_count,
            'attendance_rate' => $event->attendance_rate,
            'occupancy_percentage' => $event->occupancy_percentage,
            'participation_breakdown' => $event->participation_breakdown,
            'registration_trend' => $event->registration_trend,
        ];
        
        // Load guests with pagination for different views
        $allGuests = $event->guests()->with('user')->paginate(10, ['*'], 'all_page');
        $checkedInGuests = $event->guests()->where('checked_in', true)->with('user')->paginate(10, ['*'], 'checked_page');
        $notCheckedInGuests = $event->guests()->where('checked_in', false)->with('user')->paginate(10, ['*'], 'not_checked_page');
        $cancelledGuests = $event->guests()->where('status', 'cancelled')->with('user')->paginate(10, ['*'], 'cancelled_page');
        
        // Get related events (same organizer)
        $relatedEvents = Event::where('organizer_id', $event->organizer_id)
            ->where('id', '!=', $event->id)
            ->where('status', 'published')
            ->limit(4)
            ->get();
        
        return view('backend.pages.events.show', compact(
            'event', 
            'statistics', 
            'relatedEvents',
            'allGuests',
            'checkedInGuests', 
            'notCheckedInGuests',
            'cancelledGuests'
        ));
    }

    /**
     * show edit form
     */
    public function edit(Event $event)
    {
        $this->authorize('update', $event);
        $organizers = Organizer::all();
        return view('backend.pages.events.edit', compact('event', 'organizers'));
    }

    /**
     * update event
     */
    public function update(CreateEventRequest $request, Event $event)
    {
        $this->authorize('update', $event);
        $validatedData = $request->validated();
        unset($validatedData['organizer']);

        // handle organizer assignment
        if ($request->has('organizer') && !empty($request->organizer)) {
            $organizer = Organizer::where('name', $request->organizer)->first();
            if ($organizer) {
                $validatedData['organizer_id'] = $organizer->id;
            }
        }

        // handle cover image upload
        if ($request->hasFile('cover_image')) {
            // delete old image
            if ($event->cover_image) {
                $oldImagePath = public_path('storage/' . $event->cover_image);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }
            
            // upload new image
            $image = $request->file('cover_image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('storage/events'), $imageName);
            $validatedData['cover_image'] = 'events/' . $imageName;
        }

        // handle detail image upload
        if ($request->hasFile('detail_image')) {
            // delete old image
            if ($event->detail_image) {
                $oldImagePath = public_path('storage/' . $event->detail_image);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }
            
            // upload new image
            $image = $request->file('detail_image');
            $imageName = time() . '_detail.' . $image->getClientOriginalExtension();
            $image->move(public_path('storage/events'), $imageName);
            $validatedData['detail_image'] = 'events/' . $imageName;
        }

        $event->update($validatedData);

        return redirect()->route('events.index')->with('success', 'event updated!');
    }

    /**
     * delete event
     */
    public function destroy(Event $event)
    {
        $this->authorize('delete', $event);
        // delete cover image if exists
        if ($event->cover_image) {
            $imagePath = public_path('storage/' . $event->cover_image);
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        // delete detail image if exists
        if ($event->detail_image) {
            $imagePath = public_path('storage/' . $event->detail_image);
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }
        
        $event->delete();
        return redirect()->route('events.index')->with('success', 'event deleted!');
    }

    /**
     * bulk delete events
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'event_ids' => 'required|string',
        ]);

        $eventIds = explode(',', $request->event_ids);
        
        foreach ($eventIds as $eventId) {
            $event = Event::find($eventId);
            if ($event) {
                // delete cover image if exists
                if ($event->cover_image) {
                    $imagePath = public_path('storage/' . $event->cover_image);
                    if (file_exists($imagePath)) {
                        unlink($imagePath);
                    }
                }

                // delete detail image if exists
                if ($event->detail_image) {
                    $imagePath = public_path('storage/' . $event->detail_image);
                    if (file_exists($imagePath)) {
                        unlink($imagePath);
                    }
                }

                $event->delete();
            }
        }

        return redirect()->route('events.index')->with('success', count($eventIds) . ' event(s) deleted!');
    }

    /**
     * show public event list
     */
    public function publicIndex(Request $request)
    {
        $query = Event::where('status', 'published')
            ->where('date', '>=', now())
            ->with('organizer');
        
        // Filter by search
        if ($request->has('search') && $request->search) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }
        
        // Filter by location
        if ($request->has('location') && $request->location) {
            $query->where('location', 'like', '%' . $request->location . '%');
        }
        
        // Sorting
        $sortBy = $request->get('sort', 'date');
        $sortDirection = $request->get('direction', 'asc');
        
        switch ($sortBy) {
            case 'title':
                $query->orderBy('title', $sortDirection);
                break;
            case 'date':
            default:
                $query->orderBy('date', $sortDirection);
                break;
        }
        
        $events = $query->paginate(12);
        
        return view('frontend.events.index', compact('events'));
    }

    /**
     * show public event details
     */
    public function publicShow(Event $event)
    {
        if ($event->status !== 'published') {
            abort(404);
        }

        $event->load(['organizer', 'guests']);
        
        // Get related events (same organizer, excluding current event)
        $relatedEvents = Event::where('status', 'published')
            ->where('id', '!=', $event->id)
            ->where('date', '>', now())
            ->when($event->organizer_id, function($query) use ($event) {
                $query->where('organizer_id', $event->organizer_id);
            })
            ->limit(4)
            ->get();
        
        return view('frontend.events.show', compact('event', 'relatedEvents'));
    }
}
