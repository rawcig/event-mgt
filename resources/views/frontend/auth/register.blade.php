@extends('backend.layout.auth')
@section('title', 'Register')
@section('welcome-title', 'Create Account')

@section('auth-form')
<div class="auth-form">
    <h4 class="text-center mb-4">Create your account</h4>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('register') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="name" class="font-weight-bold">Name</label>
            <input type="text" id="name" class="form-control @error('name') is-invalid @enderror"
                   name="name" value="{{ old('name') }}" placeholder="Enter your name" required autofocus>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label for="reg-email" class="font-weight-bold">Email</label>
            <input type="email" id="reg-email" class="form-control @error('email') is-invalid @enderror"
                   name="email" value="{{ old('email') }}" placeholder="Enter your email" required>
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label for="phone" class="font-weight-bold">Phone</label>
            <input type="text" id="phone" class="form-control @error('phone') is-invalid @enderror"
                   name="phone" value="{{ old('phone') }}" placeholder="Enter your phone number">
            @error('phone')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label for="reg-password" class="font-weight-bold">Password</label>
            <input type="password" id="reg-password" class="form-control @error('password') is-invalid @enderror"
                   name="password" placeholder="Create a password (min 8 characters)" required>
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label for="password-confirm" class="font-weight-bold">Confirm Password</label>
            <input type="password" id="password-confirm" class="form-control"
                   name="password_confirmation" placeholder="Confirm your password" required>
        </div>
        <div class="text-center">
            <button type="submit" class="btn btn-primary btn-block">Sign up</button>
        </div>
    </form>
    <div class="new-account mt-3">
        <p>Already have an account? <a class="text-primary" href="{{ route('login') }}">Sign in</a></p>
    </div>
</div>
@endsection
