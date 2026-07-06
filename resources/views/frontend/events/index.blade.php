@extends('backend.layout.app')
@section('Title', 'Browse Events')
@section('content')
<div class="container-fluid">
    <div class="row page-titles mx-0">
        <div class="col-sm-6 p-md-0">
            <div class="breadcrumb-range-picker">
                <span><i class="mdi mdi-calendar"></i></span>
                <span class="ml-1">Browse Events</span>
            </div>
        </div>
        <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item active"><a href="javascript:void(0)">Events</a></li>
            </ol>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    @if(session('message'))
        <div class="alert alert-info">
            {{ session('message') }}
        </div>
    @endif

    @if(auth()->check())
        @php
            $myEventIds = auth()->user()->guests()->pluck('event_id');
        @endphp

        @if($myEventIds->count() > 0)
            <div class="row mb-4">
                <div class="col-12">
                    <div class="alert alert-info">
                        <h5 style="color:#0c5460 !important;"><i class="mdi mdi-ticket" style="color: #0c5460"></i> Your Registered Events</h5>
                        <p class="mb-0">
                            You are registered for {{ $myEventIds->count() }} event(s). 
                            <a href="{{ route('my-events') }}">View all my events →</a>
                        </p>
                    </div>
                </div>
            </div>
        @endif
    @endif

    <!-- Filters -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('events.public') }}" method="GET" class="row align-items-end">
                        <div class="col-12 col-md-4 mb-2 mb-md-0">
                            <label class="form-label">Search Events</label>
                            <input type="text" name="search" class="form-control"
                                   placeholder="Search by title..." value="{{ request('search') }}">
                        </div>
                        <div class="col-12 col-md-3 mb-2 mb-md-0">
                            <label class="form-label">Location</label>
                            <input type="text" name="location" class="form-control"
                                   placeholder="Filter by location..." value="{{ request('location') }}">
                        </div>
                        <div class="col-6 col-md-2 mb-2 mb-md-0">
                            <label class="form-label">Sort By</label>
                            <select name="sort" class="form-control">
                                <option value="date" {{ request('sort') == 'date' ? 'selected' : '' }}>Date</option>
                                <option value="title" {{ request('sort') == 'title' ? 'selected' : '' }}>Title</option>
                            </select>
                        </div>
                        <div class="col-6 col-md-2 mb-2 mb-md-0">
                            <label class="form-label">Order</label>
                            <select name="direction" class="form-control">
                                <option value="asc" {{ request('direction') == 'asc' ? 'selected' : '' }}>Ascending</option>
                                <option value="desc" {{ request('direction') == 'desc' ? 'selected' : '' }}>Descending</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-1 mb-2 mb-md-0">
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="mdi mdi-filter"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        @forelse($events as $event)
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card event-card h-80">
                    <div class="event-image-container-list" data-image="{{ $event->cover_image ? asset('storage/' . $event->cover_image) : asset('images/placeholder-event.svg') }}">
                        <img class="card-img-top img-fluid" src="{{ $event->cover_image ? asset('storage/' . $event->cover_image) : asset('images/placeholder-event.svg') }}" 
                             alt="{{ $event->title }}" style="height: 200px; object-fit: contain; width: 100%; position: relative; z-index: 1;">
                    </div>
                    <div class="card-header text-white">
                        <h5 class="card-title mb-0">{{ Str::limit($event->title, 40) }}</h5>
                    </div>
                    <div class="card-body">
                        <!-- Registration Status Badge -->
                        @php
                            $userRegistration = auth()->check() ? $event->getUserRegistration() : null;
                        @endphp
                        
                        @if($userRegistration)
                            <span class="badge badge-success mb-2">
                                <i class="mdi mdi-check-circle"></i> Registered
                            </span>
                        @elseif(auth()->check())
                            <span class="badge badge-primary mb-2">
                                <i class="mdi mdi-plus-circle"></i> Available to Register
                            </span>
                        @else
                            <span class="badge badge-info mb-2">
                                <i class="mdi mdi-login"></i> Login to Register
                            </span>
                        @endif
                        
                        <!-- Seat Availability -->
                        <div class="mb-2">
                            @if(is_null($event->max_attendees))
                                <span class="badge badge-info">
                                    <i class="mdi mdi-infinity"></i> Unlimited Seats
                                </span>
                            @elseif($event->is_full)
                                <span class="badge badge-danger">
                                    <i class="mdi mdi-close-circle"></i> Sold Out
                                </span>
                            @else
                                <span class="badge badge-success">
                                    <i class="mdi mdi-check-circle"></i> 
                                    {{ $event->available_seats }} seats left
                                </span>
                            @endif
                        </div>
                        
                        <p class="card-text">{{ Str::limit($event->description, 100) }}</p>
                        
                        <!-- Attendance Stats -->
                        <div class="row mt-3 mb-2">
                            <div class="col-6">
                                <small class="text-muted">
                                    <i class="mdi mdi-account-multiple"></i> 
                                    {{ $event->registered_count }} registered
                                </small>
                            </div>
                            <div class="col-6 text-right">
                                <small class="text-muted">
                                    <i class="mdi mdi-ticket"></i> 
                                    {{ $event->ticket_count }} tickets
                                </small>
                            </div>
                        </div>
                        
                        @if($event->max_attendees)
                            <div class="progress mb-3" style="height: 8px;">
                                @php
                                    $percentage = min(100, ($event->ticket_count / $event->max_attendees) * 100);
                                @endphp
                                <div class="progress-bar bg-{{ $event->is_full ? 'danger' : ($percentage > 80 ? 'warning' : 'success') }}" 
                                     role="progressbar" 
                                     style="width: {{ $percentage }}%">
                                </div>
                            </div>
                        @endif
                        
                        <!-- Action Buttons -->
                        <div class="mt-3 group-row col-12 row">
                            <div class="col-6">
                                <a href="{{ route('events.show.public', $event) }}" 
                                    class="btn btn-primary btn-block text-white">
                                    <i class="mdi mdi-eye"></i> View Details
                                </a>
                            </div>

                            <div class="col-6">
                            @if($userRegistration)
                                <a href="{{ route('my-events') }}" 
                                   class="btn btn-warning btn-block text-white">
                                    <i class="mdi mdi-ticket"></i> View My Ticket
                                </a>
                            @elseif(auth()->check() && !$event->is_full)
                                <a href="{{ route('events.register', $event) }}" 
                                   class="btn btn-success btn-block text-white">
                                    <i class="mdi mdi-account-plus"></i> Register Now
                                </a>
                            @elseif($event->is_full)
                                <button class="btn btn-secondary btn-block" disabled>
                                    <i class="mdi mdi-close-circle"></i> Event Full
                                </button>
                            @else
                                <a href="{{ route('login') }}" 
                                   class="btn btn-info btn-block text-white">
                                    <i class="mdi mdi-login"></i> Login to Register
                                </a>
                            @endif
                            </div>
                        </div>
                    {{-- </div>
                    <div class="card-footer"> --}}
                        <div class="row mt-3">
                            <div class="col-6">
                                <small class="text-muted">
                                    <i class="mdi mdi-calendar"></i> 
                                    {{ $event->date->format('M d, Y') }}
                                </small>
                            </div>
                            <div class="col-6 text-right">
                                <small class="text-muted">
                                    <i class="mdi mdi-map-marker"></i> 
                                    {{ $event->location ?? 'TBA' }}
                                </small>
                            </div>
                        </div>
                        <div class="mt-1">
                            <small class="text-muted">
                                <i class="mdi mdi-account"></i> 
                                {{ $event->organizer ? $event->organizer->name : 'TBA' }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">
                    <i class="mdi mdi-information"></i> No upcoming events available at the moment.
                </div>
            </div>
        @endforelse
    </div>

    <div class="d-flex justify-content-center">
        {{ $events->links() }}
    </div>
</div>

<style>
.event-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border: none;
    border-radius: 10px;
    overflow: hidden;
}

.event-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.15);
}

.event-card img.card-img-top {
    border-top-left-radius: 10px;
    border-top-right-radius: 10px;
}

.event-card .card-header {
    border-top-left-radius: 10px;
    border-top-right-radius: 10px;
}

.event-card .card-footer {
    border-bottom-left-radius: 10px;
    border-bottom-right-radius: 10px;
}

.event-image-container-list {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    height: 200px;
    overflow: hidden;
    background-color: #e4e4e4;
    background-image: var(--bg-image);
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
}

.event-image-container-list::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-image: var(--bg-image);
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    filter: blur(18px);
    transform: scale(1.08);
    z-index: 0;
}

.event-image-container-list > img {
    position: relative;
    z-index: 1;
}

</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('[data-image]').forEach(container => {
        const imageUrl = container.getAttribute('data-image');
        container.style.setProperty('--bg-image', `url('${imageUrl}')`);
        container.style.backgroundImage = `var(--bg-image)`;
    });
});
</script>
@endsection
