@extends('backend.layout.auth')
@section('title', 'Login')
@section('welcome-title', 'Welcome Back')

@section('auth-form')
<div class="auth-form">
    <h4 class="text-center mb-4">Sign in your account</h4>
    
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

    <form action="{{ route('login') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="email" class="font-weight-bold">Email</label>
            <input type="email" id="email" class="form-control @error('email') is-invalid @enderror"
                   name="email" value="{{ old('email') }}" placeholder="Enter your email" required autofocus>
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label for="password" class="font-weight-bold">Password</label>
            <input type="password" id="password" class="form-control @error('password') is-invalid @enderror"
                   name="password" placeholder="Enter your password" required>
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="auth-options d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mt-4 mb-3">
            <div class="form-check mb-2 mb-sm-0">
                <input class="form-check-input" type="checkbox" id="remember" name="remember">
                <label class="form-check-label" for="remember">Remember me</label>
            </div>
            <div>
                <a href="{{ route('password.request') }}">Forgot Password?</a>
            </div>
        </div>
        <div class="text-center">
            <button type="submit" class="btn btn-primary btn-block">Sign me in</button>
        </div>
    </form>
    <div class="new-account mt-3">
        <p>Don't have an account? <a class="text-primary" href="{{ route('register') }}">Sign up</a></p>
    </div>
</div>
@endsection
