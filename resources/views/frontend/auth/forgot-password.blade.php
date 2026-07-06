@extends('backend.layout.auth')
@section('title', 'Forgot Password')
@section('welcome-title', 'Reset Password')

@section('welcome-content')
<p>Enter your email and we'll send you a reset link.</p>
@endsection

@section('auth-form')
<div class="auth-form">
    <h4 class="text-center mb-4">Reset Password</h4>

    @if(session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    @if($errors->has('email'))
        <div class="alert alert-danger">
            {{ $errors->first('email') }}
        </div>
    @endif

    <form action="{{ route('password.email') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="forgot-email" class="font-weight-bold">Email</label>
            <input type="email" id="forgot-email" class="form-control @error('email') is-invalid @enderror"
                   name="email" value="{{ old('email') }}" placeholder="Enter your email" required>
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="text-center">
            <button type="submit" class="btn btn-primary btn-block">Send Reset Link</button>
        </div>
    </form>
    <div class="new-account mt-3 text-center">
        <a class="text-primary" href="{{ route('login') }}">Back to Login</a>
    </div>
</div>
@endsection
