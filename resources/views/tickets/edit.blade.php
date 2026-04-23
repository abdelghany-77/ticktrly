@extends('layouts.app')

@section('title', 'Edit Ticket')
@section('page-title', 'Edit Ticket')

@section('content')
  <div class="edit-ticket-card">
    <h1 class="edit-title">Update Ticket Status</h1>
    <p class="edit-subtitle">Use the workflow to move this ticket forward.</p>
    <p class="edit-status-info">
      Current status: {{ ucwords(str_replace('_', ' ', $ticket->status)) }}
    </p>

    <form method="POST" action="{{ route('tickets.update', $ticket) }}" class="form-space-y">
      @csrf
      @method('PUT')

      <div>
        <label for="status" class="form-label">Next status</label>
        <select id="status" name="status" required class="form-select">
          <option value="" disabled selected>Select next status</option>
          @foreach ($allowedTransitions as $nextStatus)
            <option value="{{ $nextStatus }}" @selected(old('status') === $nextStatus)>
              {{ ucwords(str_replace('_', ' ', $nextStatus)) }}
            </option>
          @endforeach
        </select>
      </div>

      @if (count($allowedTransitions) === 0)
        <p class="edit-warning">
          No available next steps from this status.
        </p>
      @endif

      <div class="edit-actions">
        <button type="submit" class="btn-full-primary" style="width:auto" @disabled(count($allowedTransitions) === 0)>Update
          Status</button>
        <a href="{{ route('tickets.show', $ticket) }}" class="btn-back">Back</a>
      </div>
    </form>
  </div>
@endsection
