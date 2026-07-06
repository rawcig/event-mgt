@extends('backend.layout.app')
@section('Title', 'Admin Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row page-titles mx-0">
        <div class="col-sm-6 p-md-0">
            <div class="breadcrumb-range-picker">
                <span><i class="mdi mdi-speedometer"></i></span>
                <span class="ml-1">Dashboard</span>
            </div>
        </div>
        <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item active"><a href="javascript:void(0)">Dashboard</a></li>
            </ol>
        </div>
    </div>

    <!-- Welcome Message -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card welcome-card bg-primary-gradient text-white">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h2 class="mb-2 font-weight-bold">
                                <i class="mdi mdi-hand-wave mr-2"></i> 
                                Welcome back, {{ auth()->user()->name }}!
                            </h2>
                            <p class="mb-0 opacity-8">
                                <span class="badge badge-light text-primary px-3 py-2">
                                    <i class="mdi mdi-shield-check mr-1"></i>
                                    {{ ucfirst(auth()->user()->role) }}
                                </span>
                                <span class="ml-2 font-weight-medium">Here's a summary of what's happening with your event ecosystem today.</span>
                            </p>
                        </div>
                        <div class="col-md-4 text-right d-none d-md-block">
                            <i class="mdi mdi-rocket-launch opacity-5" style="font-size: 80px;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <!-- Events Stats -->
        <div class="col-xl-3 col-lg-6 col-sm-6">
            <div class="card stat-widget-one border-0 shadow-sm">
                <div class="card-body">
                    <div class="media align-items-center">
                        <div class="icon-box-soft-info mr-3">
                            <i class="mdi mdi-calendar"></i>
                        </div>
                        <div class="media-body">
                            <p class="mb-1 text-muted">Total Events</p>
                            <h3 class="mb-0 font-weight-bold">{{ $stats['total_events'] }}</h3>
                        </div>
                    </div>
                    <div class="mt-3 pt-3 border-top d-flex justify-content-between">
                        <small class="text-success"><i class="mdi mdi-check-circle"></i> {{ $stats['published_events'] }} Published</small>
                        <small class="text-info"><i class="mdi mdi-clock-outline"></i> {{ $stats['upcoming_events'] }} Upcoming</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Organizers Stats -->
        <div class="col-xl-3 col-lg-6 col-sm-6">
            <div class="card stat-widget-one border-0 shadow-sm">
                <div class="card-body">
                    <div class="media align-items-center">
                        <div class="icon-box-soft-success mr-3">
                            <i class="mdi mdi-account-group"></i>
                        </div>
                        <div class="media-body">
                            <p class="mb-1 text-muted">Organizers</p>
                            <h3 class="mb-0 font-weight-bold">{{ $stats['total_organizers'] }}</h3>
                        </div>
                    </div>
                    <div class="mt-3 pt-3 border-top">
                        <small class="text-muted"><i class="mdi mdi-domain mr-1"></i> Managing all event operations</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Guests Stats -->
        <div class="col-xl-3 col-lg-6 col-sm-6">
            <div class="card stat-widget-one border-0 shadow-sm">
                <div class="card-body">
                    <div class="media align-items-center">
                        <div class="icon-box-soft-warning mr-3">
                            <i class="mdi mdi-ticket-confirmation"></i>
                        </div>
                        <div class="media-body">
                            <p class="mb-1 text-muted">Total Guests</p>
                            <h3 class="mb-0 font-weight-bold">{{ $stats['total_guests'] }}</h3>
                        </div>
                    </div>
                    <div class="mt-3 pt-3 border-top">
                        <small class="text-warning font-weight-bold">
                            <i class="mdi mdi-account-check mr-1"></i> {{ $stats['checked_in_guests'] }} Checked In
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Users Stats -->
        <div class="col-xl-3 col-lg-6 col-sm-6">
            <div class="card stat-widget-one border-0 shadow-sm">
                <div class="card-body">
                    <div class="media align-items-center">
                        <div class="icon-box-soft-danger mr-3">
                            <i class="mdi mdi-account-key"></i>
                        </div>
                        <div class="media-body">
                            <p class="mb-1 text-muted">System Users</p>
                            <h3 class="mb-0 font-weight-bold">{{ $stats['total_users'] }}</h3>
                        </div>
                    </div>
                    <div class="mt-3 pt-3 border-top d-flex justify-content-between">
                        <small class="text-danger"><i class="mdi mdi-shield-crown"></i> {{ $stats['admin_users'] }} Admins</small>
                        <small class="text-primary"><i class="mdi mdi-briefcase-account"></i> {{ $stats['organizer_users'] }} Org</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 font-weight-bold"><i class="mdi mdi-lightning-bolt text-warning mr-2"></i> Quick Actions</h5>
                </div>
                <div class="card-body pt-0">
                    <div class="row quick-actions-row">
                        <div class="col-lg-2 col-md-4 col-sm-6 mb-2">
                            <a href="{{ route('create-event') }}" class="btn btn-primary btn-block py-2">
                                <i class="mdi mdi-plus-circle mr-1"></i> Create Event
                            </a>
                        </div>
                        <div class="col-lg-2 col-md-4 col-sm-6 mb-2">
                            <a href="{{ route('organizer.create') }}" class="btn btn-success btn-block py-2">
                                <i class="mdi mdi-account-plus mr-1"></i> Add Organizer
                            </a>
                        </div>
                        <div class="col-lg-2 col-md-4 col-sm-6 mb-2">
                            <a href="{{ route('guests.create') }}" class="btn btn-info btn-block py-2">
                                <i class="mdi mdi-account-badge mr-1"></i> Register Guest
                            </a>
                        </div>
                        <div class="col-lg-3 col-md-6 col-sm-6 mb-2">
                            <a href="{{ route('reports.index') }}" class="btn btn-warning btn-block py-2 text-dark font-weight-bold">
                                <i class="mdi mdi-chart-box mr-1"></i> Analytics Dashboard
                            </a>
                        </div>
                        @if(auth()->user()->isAdmin())
                        <div class="col-lg-3 col-md-6 col-sm-12 mb-2">
                            <a href="{{ route('users.index') }}" class="btn btn-danger btn-block py-2">
                                <i class="mdi mdi-account-cog mr-1"></i> Manage Users
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Recent Events -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3 border-0">
                    <h5 class="mb-0 font-weight-bold text-primary"><i class="mdi mdi-calendar-clock mr-2"></i> Recent Events</h5>
                    <a href="{{ route('events.index') }}" class="btn btn-sm btn-outline-primary rounded-pill">View All</a>
                </div>
                <div class="card-body p-0">
                    @if($recentEvents->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($recentEvents as $event)
                                <div class="list-group-item list-group-item-action border-0 px-4 py-3">
                                    <div class="d-flex w-100 justify-content-between align-items-center">
                                        <h6 class="mb-1 font-weight-bold text-dark">{{ $event->title }}</h6>
                                        <small class="text-muted"><i class="mdi mdi-clock-outline mr-1"></i>{{ $event->created_at->diffForHumans() }}</small>
                                    </div>
                                    <p class="mb-2 text-muted small">
                                        <span class="mr-3"><i class="mdi mdi-calendar-text mr-1 text-primary"></i> {{ $event->date->format('M d, Y') }}</span>
                                        @if($event->organizer)
                                            <span><i class="mdi mdi-account-tie mr-1 text-info"></i> {{ $event->organizer->name }}</span>
                                        @endif
                                    </p>
                                    <div class="d-flex align-items-center">
                                        <span class="badge badge-pill badge-{{
                                            $event->status === 'published' ? 'success' :
                                            ($event->status === 'cancelled' ? 'danger' :
                                            ($event->status === 'completed' ? 'info' : 'warning'))
                                        }} px-3">
                                            {{ ucfirst($event->status) }}
                                        </span>
                                        <span class="ml-2 small text-muted">
                                            <i class="mdi mdi-account-group-outline mr-1"></i> {{ $event->guests->count() }} registered
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="mdi mdi-calendar-blank text-light" style="font-size: 60px;"></i>
                            <p class="text-muted mt-2">No events found.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Top Events by Registration -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3 border-0">
                    <h5 class="mb-0 font-weight-bold text-warning"><i class="mdi mdi-trophy-variant mr-2"></i> Popular Events</h5>
                    <a href="{{ route('reports.events') }}" class="btn btn-sm btn-outline-warning rounded-pill">Full Report</a>
                </div>
                <div class="card-body p-0">
                    @if($topEvents->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($topEvents as $index => $event)
                                <div class="list-group-item list-group-item-action border-0 px-4 py-3">
                                    <div class="d-flex w-100 justify-content-between align-items-center">
                                        <h6 class="mb-1 font-weight-bold">
                                            <span class="badge badge-circle badge-{{
                                                $index === 0 ? 'warning' :
                                                ($index === 1 ? 'secondary' :
                                                ($index === 2 ? 'info' : 'light'))
                                            }} mr-2">{{ $index + 1 }}</span>
                                            {{ $event->title }}
                                        </h6>
                                        <span class="badge badge-primary badge-pill px-3">
                                            <i class="mdi mdi-account-multiple mr-1"></i> {{ $event->guests_count }}
                                        </span>
                                    </div>
                                    <p class="mb-0 text-muted small mt-2">
                                        <i class="mdi mdi-calendar-check mr-1"></i> {{ $event->date->format('M d, Y') }}
                                        @if($event->location)
                                            <span class="ml-3"><i class="mdi mdi-map-marker-radius mr-1"></i> {{ Str::limit($event->location, 40) }}</span>
                                        @endif
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="mdi mdi-chart-line-variant text-light" style="font-size: 60px;"></i>
                            <p class="text-muted mt-2">No data available yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Registrations -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3 border-0">
                    <h5 class="mb-0 font-weight-bold text-info"><i class="mdi mdi-account-search mr-2"></i> Recent Registrations</h5>
                    <a href="{{ route('guests.index') }}" class="btn btn-sm btn-info text-white rounded-pill">Manage All Guests</a>
                </div>
                <div class="card-body pt-0">
                    @if($recentGuests->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle table-responsive-cards">
                                <thead class="text-muted small text-uppercase">
                                    <tr>
                                        <th class="border-0">Guest</th>
                                        <th class="border-0">Event</th>
                                        <th class="border-0">Details</th>
                                        <th class="border-0">Status</th>
                                        <th class="border-0">Time</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentGuests as $guest)
                                        <tr>
                                            <td class="py-3" data-label="Guest">
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm bg-light rounded-circle mr-3 d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                                        <i class="mdi mdi-account text-muted"></i>
                                                    </div>
                                                    <div class="text-left-mobile">
                                                        <span class="d-block font-weight-bold text-dark">{{ $guest->name }}</span>
                                                        <small class="text-muted">{{ $guest->email }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="py-3" data-label="Event">
                                                <a href="{{ route('events.show', $guest->event) }}" class="text-primary font-weight-medium">
                                                    {{ Str::limit($guest->event->title, 40) }}
                                                </a>
                                            </td>
                                            <td class="py-3" data-label="Details">
                                                <span class="badge badge-outline-{{
                                                    $guest->participation_type === 'vip' ? 'danger' :
                                                    ($guest->participation_type === 'speaker' ? 'primary' :
                                                    ($guest->participation_type === 'sponsor' ? 'success' :
                                                    ($guest->participation_type === 'volunteer' ? 'info' : 'secondary')))
                                                }} mr-2">
                                                    {{ ucfirst($guest->participation_type) }}
                                                </span>
                                                <small class="text-muted"><i class="mdi mdi-ticket-outline"></i> {{ $guest->ticket_count }}</small>
                                            </td>
                                            <td class="py-3" data-label="Status">
                                                <span class="badge badge-{{
                                                    $guest->registration_status === 'confirmed' ? 'success' :
                                                    ($guest->registration_status === 'cancelled' ? 'danger' :
                                                    ($guest->registration_status === 'attended' ? 'info' : 'warning'))
                                                }}">
                                                    {{ ucfirst($guest->registration_status) }}
                                                </span>
                                            </td>
                                            <td class="py-3" data-label="Time">
                                                <small class="d-block text-dark">{{ $guest->created_at->diffForHumans() }}</small>
                                                <small class="text-muted">{{ $guest->created_at->format('M d, h:i A') }}</small>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="mdi mdi-account-off-outline text-light" style="font-size: 60px;"></i>
                            <p class="text-muted mt-2">No registrations yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Modern Gradient for Welcome Card */
.bg-primary-gradient {
    background: linear-gradient(135deg, #7366ff 0%, #a066ff 100%);
}

.opacity-8 {
    opacity: 0.85;
}

.opacity-5 {
    opacity: 0.5;
}

/* Soft Icon Boxes */
.icon-box-soft-info, .icon-box-soft-success, .icon-box-soft-warning, .icon-box-soft-danger {
    width: 55px;
    height: 55px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 26px;
    transition: all 0.3s ease;
}

.icon-box-soft-info { background: rgba(23, 162, 184, 0.12); color: #17a2b8; }
.icon-box-soft-success { background: rgba(40, 167, 69, 0.12); color: #28a745; }
.icon-box-soft-warning { background: rgba(255, 193, 7, 0.12); color: #ffc107; }
.icon-box-soft-danger { background: rgba(220, 53, 69, 0.12); color: #dc3545; }

.stat-widget-one:hover .icon-box-soft-info { background: #17a2b8; color: #fff; transform: scale(1.1); }
.stat-widget-one:hover .icon-box-soft-success { background: #28a745; color: #fff; transform: scale(1.1); }
.stat-widget-one:hover .icon-box-soft-warning { background: #ffc107; color: #fff; transform: scale(1.1); }
.stat-widget-one:hover .icon-box-soft-danger { background: #dc3545; color: #fff; transform: scale(1.1); }

/* List Group Enhancements */
.list-group-item-action {
    transition: all 0.2s ease;
    border-left: 4px solid transparent !important;
}

.list-group-item-action:hover {
    background-color: #f8f9fa;
    border-left-color: #7366ff !important;
    padding-left: 1.75rem !important;
}

/* Card Improvements */
.card {
    border-radius: 15px !important;
    overflow: hidden;
}

.card-header {
    border-bottom: 1px solid rgba(0,0,0,0.03) !important;
}

.shadow-sm {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important;
}

/* Table Hover Effect */
.table-hover tbody tr:hover {
    background-color: rgba(115, 102, 255, 0.02);
}

/* Badge Circle for Rankings */
.badge-circle {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0;
    font-size: 12px;
}
</style>
@endsection
