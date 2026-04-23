@extends('layouts.app')

@section('title', 'Register')

@section('content')
  <div class="auth-card">
    <h1 class="auth-title">Register</h1>
    <p class="auth-subtitle">Create a user account to submit support tickets.</p>

    <form method="POST" action="{{ route('register.store') }}" class="form-space-y">
      @csrf

      <div>
        <label for="name" class="form-label">Name</label>
        <input id="name" type="text" name="name" value="{{ old('name') }}" required
          class="form-input">
      </div>

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

      <div>
        <label for="password_confirmation" class="form-label">Confirm Password</label>
        <input id="password_confirmation" type="password" name="password_confirmation" required
          class="form-input">
      </div>

      <button type="submit" class="btn-full-primary">
        Register
      </button>
    </form>

    <p class="auth-footer">
      Already have an account?
      <a href="{{ route('login') }}">Login</a>
    </p>
  </div>
@endsection
