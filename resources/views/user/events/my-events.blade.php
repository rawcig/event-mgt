@extends('backend.layout.app')
@section('Title', 'My Registered Events')
@section('content')
<div class="container-fluid">
    <div class="row page-titles mx-0">
        <div class="col-sm-6 p-md-0">
            <div class="breadcrumb-range-picker">
                <span><i class="mdi mdi-ticket"></i></span>
                <span class="ml-1">My Registered Events</span>
            </div>
        </div>
        <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item active"><a href="javascript:void(0)">My Events</a></li>
            </ol>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-responsive-cards">
                            <thead>
                                <tr>
                                    <th>Event</th>
                                    <th>Date</th>
                                    <th>Participation Type</th>
                                    <th data-hide="mobile">Tickets</th>
                                    <th>Status</th>
                                    <th data-hide="mobile">Checked In</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($guests as $guest)
                                    <tr>
                                        <td data-label="Event">
                                            <strong>{{ $guest->event->title }}</strong>
                                        </td>
                                        <td data-label="Date">{{ $guest->event->date->format('M d, Y') }}</td>
                                        <td data-label="Participation">
                                            <span class="badge badge-{{
                                                $guest->participation_type === 'vip' ? 'danger' :
                                                ($guest->participation_type === 'speaker' ? 'primary' :
                                                ($guest->participation_type === 'sponsor' ? 'success' :
                                                ($guest->participation_type === 'volunteer' ? 'info' : 'secondary')))
                                                }}">
                                                {{ ucfirst($guest->participation_type) }}
                                            </span>
                                        </td>
                                        <td data-label="Tickets" data-hide="mobile">{{ $guest->ticket_count }}</td>
                                        <td data-label="Status">
                                            <span class="badge badge-{{
                                                $guest->registration_status === 'confirmed' ? 'success' :
                                                ($guest->registration_status === 'cancelled' ? 'danger' :
                                                ($guest->registration_status === 'attended' ? 'info' : 'warning'))
                                                }}">
                                                {{ ucfirst($guest->registration_status) }}
                                            </span>
                                        </td>
                                        <td data-label="Checked In" data-hide="mobile">
                                            @if($guest->checked_in)
                                                <span class="badge badge-success"><i class="mdi mdi-check"></i> Yes</span>
                                            @else
                                                <span class="badge badge-secondary"><i class="mdi mdi-close"></i> No</span>
                                            @endif
                                        </td>
                                        <td data-label="Actions">
                                            <div class="d-flex flex-wrap" style="gap: 4px;">
                                                <button type="button" class="btn btn-sm btn-warning text-white"
                                                        data-toggle="modal" data-target="#qrModal{{ $guest->id }}">
                                                    <i class="mdi mdi-qrcode"></i> Ticket
                                                </button>
                                                <a href="{{ route('events.show.public', $guest->event) }}" class="btn btn-sm btn-primary text-white">
                                                    <i class="mdi mdi-eye"></i> View
                                                </a>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- QR Code Modal -->
                                    <div class="modal fade" id="qrModal{{ $guest->id }}" tabindex="-1">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header bg-primary text-white">
                                                    <h5 class="modal-title">{{ Str::limit($guest->event->title, 40) }}</h5>
                                                    <button type="button" class="close text-white" data-dismiss="modal">
                                                        <span>&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body text-center">
                                                    @if($guest->qr_code)
                                                        <img src="{{ $guest->qr_code }}" alt="QR Code" class="img-fluid mb-3 qr-modal-img" style="max-width: 250px;">
                                                        <h6 class="mb-2">{{ $guest->name }}</h6>
                                                        <p class="mb-1">
                                                            <strong>Tickets:</strong> {{ $guest->ticket_count }}<br>
                                                            <strong>Type:</strong> {{ ucfirst($guest->participation_type) }}<br>
                                                            <strong>Status:</strong>
                                                            <span class="badge badge-{{
                                                                $guest->registration_status === 'confirmed' ? 'success' : 'warning'
                                                            }}">
                                                                {{ ucfirst($guest->registration_status) }}
                                                            </span>
                                                        </p>
                                                        <div class="alert alert-info mt-3 mb-0">
                                                            <small>
                                                                <i class="mdi mdi-information"></i>
                                                                Show this QR code at the event for check-in
                                                            </small>
                                                        </div>
                                                    @else
                                                        <p class="text-muted">QR code not generated yet</p>
                                                    @endif
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            You haven't registered for any events yet.
                                            <a href="{{ route('events.public') }}" class="text-primary">Browse Events</a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="d-flex justify-content-center">
                        {{ $guests->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
