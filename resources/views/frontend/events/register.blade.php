@extends('backend.layout.app')
@section('Title', 'Register - ' . $event->title)
@section('content')
<div class="container-fluid">
    <div class="row page-titles mx-0">
        <div class="col-sm-6 p-md-0">
            <div class="breadcrumb-range-picker">
                <span><i class="mdi mdi-account-plus"></i></span>
                <span class="ml-1">Event Registration</span>
            </div>
        </div>
        <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('events.public') }}">Events</a></li>
                <li class="breadcrumb-item active"><a href="javascript:void(0)">Register</a></li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Register for: {{ $event->title }}</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('events.register.store', $event) }}" method="POST">
                        @csrf
                        
                        <h5 class="mb-3">Your Information</h5>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>Full Name *</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                       name="name" value="{{ old('name', auth()->user()->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group col-md-6">
                                <label>Email Address *</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                       name="email" value="{{ old('email', auth()->user()->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>Phone Number</label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                       name="phone" value="{{ old('phone', auth()->user()->phone) }}">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group col-md-6">
                                <label>Company/Organization</label>
                                <input type="text" class="form-control @error('company') is-invalid @enderror"
                                       name="company" value="{{ old('company') }}">
                                @error('company')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>Position/Title</label>
                                <input type="text" class="form-control @error('position') is-invalid @enderror"
                                       name="position" value="{{ old('position') }}">
                                @error('position')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group col-md-6">
                                <label>Participation Type *</label>
                                <select name="participation_type" class="form-control @error('participation_type') is-invalid @enderror" required>
                                    <option value="">Select type</option>
                                    <option value="attendee" {{ old('participation_type') == 'attendee' ? 'selected' : '' }}>Attendee</option>
                                    <option value="speaker" {{ old('participation_type') == 'speaker' ? 'selected' : '' }}>Speaker</option>
                                    <option value="sponsor" {{ old('participation_type') == 'sponsor' ? 'selected' : '' }}>Sponsor</option>
                                    <option value="volunteer" {{ old('participation_type') == 'volunteer' ? 'selected' : '' }}>Volunteer</option>
                                    <option value="vip" {{ old('participation_type') == 'vip' ? 'selected' : '' }}>VIP Guest</option>
                                </select>
                                @error('participation_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>Number of Tickets *</label>
                                <input type="number" class="form-control @error('ticket_count') is-invalid @enderror"
                                       name="ticket_count" value="{{ old('ticket_count', 1) }}" min="1" max="10" required>
                                @error('ticket_count')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Dietary Requirements</label>
                            <textarea class="form-control @error('dietary_requirements') is-invalid @enderror"
                                      name="dietary_requirements" rows="2" placeholder="Any dietary restrictions...">{{ old('dietary_requirements') }}</textarea>
                            @error('dietary_requirements')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Additional Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror"
                                      name="notes" rows="3" placeholder="Any additional information...">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mt-4 d-flex flex-column flex-sm-row">
                            <button type="submit" class="btn btn-secondary text-white mb-2 mb-sm-0 mr-sm-2">
                                <i class="mdi mdi-check"></i> Complete Registration
                            </button>
                            <a href="{{ route('events.show.public', $event) }}" class="btn btn-danger text-white">
                                <i class="mdi mdi-close"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4 event-detail-sidebar">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Event Information</h5>
                </div>
                <div class="card-body">
                    <p><strong>Date:</strong> {{ $event->date->format('M d, Y h:i A') }}</p>
                    <p><strong>Location:</strong> {{ $event->location ?? 'TBA' }}</p>
                    <p><strong>Organizer:</strong> {{ $event->organizer ? $event->organizer->name : 'TBA' }}</p>
                    <hr>
                    <div class="alert alert-info">
                        <small>
                            <strong>Registration is free!</strong><br>
                            You'll receive a confirmation email after registration.
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
