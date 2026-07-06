@extends('backend.layout.app')
@section('Title', $event->title)
@section('content')
<div class="container-fluid">
    <div class="row page-titles mx-0">
        <div class="col-sm-6 p-md-0">
            <div class="breadcrumb-range-picker">
                <span><i class="mdi mdi-calendar"></i></span>
                <span class="ml-1">Event Details</span>
            </div>
        </div>
        <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('events.public') }}">Events</a></li>
                <li class="breadcrumb-item active"><a href="javascript:void(0)">{{ Str::limit($event->title, 30) }}</a></li>
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

    @if(session('info'))
        <div class="alert alert-info">
            {{ session('info') }}
        </div>
    @endif

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Event Description -->
            <div class="card mb-4">
                <div class="event-image-container" data-image="{{ $event->cover_image ? asset('storage/' . $event->cover_image) : asset('images/placeholder-event.svg') }}">
                    <img class="card-img-top img-fluid" src="{{ $event->cover_image ? asset('storage/' . $event->cover_image) : asset('images/placeholder-event.svg') }}" 
                         alt="{{ $event->title }}" style="max-height: 400px; object-fit: contain; width: 100%; position: relative; z-index: 1;">
                </div>
                <div class="card-header text-white">
                    <h4 class="mb-0">{{ $event->title }}</h4>
                </div>
                <div class="card-body">
                    <h5 class="mb-3">About This Event</h5>
                    <p>{{ $event->description }}</p>
                    
                    <!-- Event Detail Image -->
                    @if($event->detail_image)
                        <div class="text-center mb-4">
                            <img src="{{ asset('storage/' . $event->detail_image) }}" 
                                 class="img-fluid rounded" alt="Event Detail" 
                                 style="max-height: 500px; width: auto;">
                        </div>
                    @endif
                    
                    <hr>
                    
                    <!-- Event Details -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center">
                                <i class="mdi mdi-calendar text-primary" style="font-size: 24px;"></i>
                                <div class="ml-3">
                                    <strong>Date & Time</strong>
                                    <p class="mb-0">{{ $event->date->format('l, F d, Y') }}</p>
                                    <small class="text-muted">{{ $event->date->format('h:i A') }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center">
                                <i class="mdi mdi-map-marker text-danger" style="font-size: 24px;"></i>
                                <div class="ml-3">
                                    <strong>Location</strong>
                                    <p class="mb-0">{{ $event->location ?? 'TBA' }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center">
                                <i class="mdi mdi-account text-success" style="font-size: 24px;"></i>
                                <div class="ml-3">
                                    <strong>Organizer</strong>
                                    <p class="mb-0">
                                        @if($event->organizer)
                                            {{ $event->organizer->name }}
                                        @else
                                            TBA
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center">
                                <i class="mdi mdi-information text-info" style="font-size: 24px;"></i>
                                <div class="ml-3">
                                    <strong>Status</strong>
                                    <p class="mb-0">
                                        <span class="badge badge-{{
                                            $event->status === 'published' ? 'success' :
                                            ($event->status === 'cancelled' ? 'danger' :
                                            ($event->status === 'completed' ? 'info' : 'warning'))
                                        }}">
                                            {{ ucfirst($event->status) }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Related Events -->
            @if(isset($relatedEvents) && $relatedEvents->count() > 0)
                <div class="card">
                    <div class="card-header text-white">
                        <h4 class="mb-0">Related Events</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($relatedEvents as $relatedEvent)
                                <div class="col-12 col-md-6 mb-3">
                                    <div class="card h-100">
                                        <div class="event-image-container-small" data-image="{{ $relatedEvent->cover_image ? asset('storage/' . $relatedEvent->cover_image) : asset('images/placeholder-event.svg') }}">
                                    <img class="card-img-top img-fluid" src="{{ $relatedEvent->cover_image ? asset('storage/' . $relatedEvent->cover_image) : asset('images/placeholder-event.svg') }}"
                                         alt="{{ $relatedEvent->title }}" style="height: 150px; object-fit: contain; width: 100%; position: relative; z-index: 1;">
                                </div>
                                        <div class="card-body p-3">
                                            <h6 class="card-title">
                                                <a href="{{ route('events.show.public', $relatedEvent) }}">
                                                    {{ Str::limit($relatedEvent->title, 50) }}
                                                </a>
                                            </h6>
                                            <p class="text-muted small mb-2">
                                                <i class="mdi mdi-calendar"></i> 
                                                {{ $relatedEvent->date->format('M d, Y') }}
                                            </p>
                                            <p class="text-muted small mb-2">
                                                <i class="mdi mdi-map-marker"></i> 
                                                {{ $relatedEvent->location ?? 'TBA' }}
                                            </p>
                                            @php
                                                $relatedRegistration = auth()->check() ? $relatedEvent->getUserRegistration() : null;
                                            @endphp
                                            @if($relatedRegistration)
                                                <span class="badge badge-success">
                                                    <i class="mdi mdi-check-circle"></i> Registered
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4 event-detail-sidebar">
            <!-- Registration Card -->
            <div class="card mb-4">
                <div class="card-header text-white">
                    <h5 class="mb-0">Event Registration</h5>
                </div>
                <div class="card-body">
                    <!-- Seat Availability -->
                    <div class="mb-4">
                        @if(is_null($event->max_attendees))
                            <div class="alert alert-info mb-2">
                                <i class="mdi mdi-infinity"></i> <strong>Unlimited Seats Available</strong>
                            </div>
                        @elseif($event->is_full)
                            <div class="alert alert-danger mb-2">
                                <i class="mdi mdi-close-circle"></i> <strong>Sold Out!</strong>
                            </div>
                        @else
                            <div class="alert alert-success mb-2">
                                <i class="mdi mdi-check-circle"></i> 
                                <strong>{{ $event->available_seats }} seats available</strong>
                            </div>
                        @endif
                        
                        @if($event->max_attendees)
                            <div class="mb-2">
                                <div class="d-flex justify-content-between mb-1">
                                    <small>Occupancy</small>
                                    <small>{{ number_format(($event->ticket_count / $event->max_attendees) * 100, 0) }}% full</small>
                                </div>
                                <div class="progress" style="height: 10px;">
                                    @php
                                        $percentage = min(100, ($event->ticket_count / $event->max_attendees) * 100);
                                    @endphp
                                    <div class="progress-bar bg-{{ $event->is_full ? 'danger' : ($percentage > 80 ? 'warning' : 'success') }}" 
                                         role="progressbar" 
                                         style="width: {{ $percentage }}%">
                                    </div>
                                </div>
                                <small class="text-muted">
                                    {{ $event->ticket_count }} of {{ $event->max_attendees }} tickets taken
                                </small>
                            </div>
                        @endif
                    </div>

                    <!-- Attendance Statistics -->
                    <div class="mb-4">
                        <h6 class="mb-3">Attendance Statistics</h6>
                        <div class="row">
                            <div class="col-6 mb-2">
                                <div class="text-center p-2 bg-light rounded">
                                    <i class="mdi mdi-account-multiple text-primary" style="font-size: 24px;"></i>
                                    <h4 class="mb-0 mt-1">{{ $event->registered_count }}</h4>
                                    <small class="text-muted">Registered</small>
                                </div>
                            </div>
                            <div class="col-6 mb-2">
                                <div class="text-center p-2 bg-light rounded">
                                    <i class="mdi mdi-ticket text-success" style="font-size: 24px;"></i>
                                    <h4 class="mb-0 mt-1">{{ $event->ticket_count }}</h4>
                                    <small class="text-muted">Tickets</small>
                                </div>
                            </div>
                            <div class="col-6 mb-2">
                                <div class="text-center p-2 bg-light rounded">
                                    <i class="mdi mdi-check-circle text-info" style="font-size: 24px;"></i>
                                    <h4 class="mb-0 mt-1">{{ $event->confirmed_count }}</h4>
                                    <small class="text-muted">Confirmed</small>
                                </div>
                            </div>
                            <div class="col-6 mb-2">
                                <div class="text-center p-2 bg-light rounded">
                                    <i class="mdi mdi-clipboard-check text-warning" style="font-size: 24px;"></i>
                                    <h4 class="mb-0 mt-1">{{ $event->checked_in_count }}</h4>
                                    <small class="text-muted">Checked In</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Registration Button -->
                    @php
                        $userRegistration = auth()->check() ? $event->getUserRegistration() : null;
                    @endphp
                    
                    @if($userRegistration)
                        <a href="{{ route('my-events') }}" class="btn btn-success btn-block btn-lg text-white">
                            <i class="mdi mdi-ticket"></i> View My Ticket
                        </a>
                        <p class="text-center text-muted mt-2">
                            <small>You're registered for this event!</small>
                        </p>
                    @elseif(auth()->check() && !$event->is_full)
                        <a href="{{ route('events.register', $event) }}" class="btn btn-primary btn-block btn-lg text-white">
                            <i class="mdi mdi-account-plus"></i> Register Now
                        </a>
                    @elseif($event->is_full)
                        <button class="btn btn-secondary btn-block btn-lg" disabled>
                            <i class="mdi mdi-close-circle"></i> Event Full
                        </button>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-info btn-block btn-lg text-white">
                            <i class="mdi mdi-login"></i> Login to Register
                        </a>
                    @endif
                </div>
            </div>

            <!-- Organizer Info -->
            @if($event->organizer)
                <div class="card">
                    <div class="event-image-container-org" data-image="{{ $event->organizer->logo ? asset('storage/' . $event->organizer->logo) : asset('images/placeholder-organizer.svg') }}">
                        <img class="card-img-top img-fluid" src="{{ $event->organizer->logo ? asset('storage/' . $event->organizer->logo) : asset('images/placeholder-organizer.svg') }}" 
                             alt="{{ $event->organizer->name }}" style="height: 150px; object-fit: contain; width: 100%; position: relative; z-index: 1;">
                    </div>
                    <div class="card-header text-white">
                        <h5 class="mb-0">Organizer</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" 
                                 style="width: 50px; height: 50px; font-size: 20px;">
                                {{ strtoupper(substr($event->organizer->name, 0, 1)) }}
                            </div>
                            <div class="ml-3">
                                <h6 class="mb-0">{{ $event->organizer->name }}</h6>
                                @if($event->organizer->email)
                                    <small class="text-muted">{{ $event->organizer->email }}</small>
                                @endif
                            </div>
                        </div>
                        @if($event->organizer->phone || $event->organizer->website)
                            <hr>
                            @if($event->organizer->phone)
                                <p class="mb-2">
                                    <i class="mdi mdi-phone"></i> 
                                    {{ $event->organizer->phone }}
                                </p>
                            @endif
                            @if($event->organizer->website)
                                <p class="mb-0">
                                    <i class="mdi mdi-web"></i> 
                                    <a href="{{ $event->organizer->website }}" target="_blank">Visit Website</a>
                                </p>
                            @endif
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.card {
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    border: none;
    border-radius: 10px;
    overflow: hidden;
}

.card:hover {
    box-shadow: 0 5px 20px rgba(0,0,0,0.15);
    transition: box-shadow 0.3s ease;
}

.card-img-top {
    border-top-left-radius: 10px;
    border-top-right-radius: 10px;
}

.card-header {
    border-top-left-radius: 10px;
    border-top-right-radius: 10px;
}

.event-image-container,
.event-image-container-small,
.event-image-container-org {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    background-color: #e4e4e4;
    background-image: var(--bg-image);
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
}

.event-image-container {
    height: 400px;
}

.event-image-container-small {
    height: 150px;
}

.event-image-container-org {
    height: 150px;
}

.event-image-container::before,
.event-image-container-small::before,
.event-image-container-org::before {
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

.event-image-container > img,
.event-image-container-small > img,
.event-image-container-org > img {
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
