@extends('layouts.app')

@section('title', 'Login')

@section('content')
  <div class="auth-card">
    <h1 class="auth-title">Login</h1>
    <p class="auth-subtitle">Sign in to your account.</p>

    <form method="POST" action="{{ route('login.store') }}" class="form-space-y">
      @csrf

      <div>
        <label for="email" class="form-label">Email</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}" required
          class="form-input">
      </div>

      <div>
        <label for="password" class="form-label">Password</label>
        <input id="password" type="password" name="password" required
          class="form-input">
      </div>

      <button type="submit" class="btn-full-primary">
        Login
      </button>
    </form>

    <p class="auth-footer">
      No account?
      <a href="{{ route('register') }}">Register</a>
    </p>
  </div>
@endsection
