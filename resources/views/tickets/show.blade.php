@extends('layouts.app')

@section('title', 'Ticket Details')
@section('page-title', 'Ticket Details')

@section('content')
  @php
    $isAgentOrAdmin = auth()->user()->role === 'agent' || auth()->user()->role === 'admin';
    $isAdmin = auth()->user()->role === 'admin';
    $isUser = auth()->user()->role === 'user';
    $userCanComment = $isUser && ($ticket->status !== 'resolved' && $ticket->status !== 'closed');
    $canComment = $isAgentOrAdmin || $userCanComment;
  @endphp

  <div class="ticket-header">
    <div>
      <h2 class="ticket-title">{{ $ticket->title }}</h2>
      <p class="ticket-status-text">
        Status: {{ ucwords(str_replace('_', ' ', $ticket->status)) }}
      </p>

      @if ($isAgentOrAdmin)
        @php
          if ($ticket->priority === 'high') {
              $priorityClasses = 'badge-red';
          } elseif ($ticket->priority === 'medium') {
              $priorityClasses = 'badge-amber';
          } else {
              $priorityClasses = 'badge-blue';
          }
        @endphp
        <span class="badge-pill {{ $priorityClasses }}" style="margin-top:0.5rem">
          Priority: {{ ucfirst($ticket->priority) }}
        </span>
      @endif
    </div>

    @if ($isAgentOrAdmin)
      <a href="{{ route('tickets.edit', $ticket) }}" class="btn-update-status">Update
        Status</a>
    @endif
  </div>

  <div class="detail-grid">
    <div class="detail-main">
      <h3 class="section-title">Description</h3>
      <p class="detail-description">{{ $ticket->description }}</p>

      @if ($ticket->file_path)
        <div class="attachment-link">
          <a href="{{ asset('storage/' . $ticket->file_path) }}" target="_blank">
            View Attachment
          </a>
        </div>
      @endif
    </div>

    <div class="detail-sidebar">
      <h3 class="section-title" style="margin-bottom:0.75rem">Details</h3>
      <div class="detail-info-list">
        <p class="detail-label">Owner: <span class="detail-value">{{ $ticket->user->name ?? 'N/A' }}</span></p>
        <p class="detail-label">Agent: <span class="detail-value">{{ $ticket->agent->name ?? 'Unassigned' }}</span></p>
        <p class="detail-label">Category: <span
            class="detail-value">{{ $ticket->category->name ?? 'Uncategorized' }}</span></p>
        <p class="detail-label">Created: <span class="detail-value">{{ $ticket->created_at->diffForHumans() }}</span></p>
        <p class="detail-label">Updated: <span class="detail-value">{{ $ticket->updated_at->diffForHumans() }}</span></p>
      </div>

      @if ($isAgentOrAdmin)
        <form class="detail-divider-form" method="POST" action="{{ route('tickets.update-priority', $ticket) }}">
          @csrf
          @method('PATCH')
          <label for="priority" class="form-label">Update Priority</label>
          <select id="priority" name="priority" class="form-select">
            <option value="low" @selected($ticket->priority === 'low')>Low</option>
            <option value="medium" @selected($ticket->priority === 'medium')>Medium</option>
            <option value="high" @selected($ticket->priority === 'high')>High</option>
          </select>
          <button type="submit" class="btn-full-outline">Save
            Priority</button>
        </form>
      @endif

      @if ($isAdmin)
        <form class="assign-form" method="POST" action="{{ route('tickets.assign-agent', $ticket) }}">
          @csrf
          @method('PATCH')
          <label for="agent_id" class="form-label">Assign Agent</label>
          <select id="agent_id" name="agent_id" class="form-select">
            <option value="">Unassigned</option>
            @foreach ($agents as $agent)
              <option value="{{ $agent->id }}" @selected($ticket->agent_id === $agent->id)>{{ $agent->name }}</option>
            @endforeach
          </select>
          <button type="submit" class="btn-full-outline">Save
            Assignment</button>
        </form>
      @endif

      @if ($isAdmin)
        <form class="delete-form" method="POST" action="{{ route('tickets.destroy', $ticket) }}"
          onsubmit="return confirm('Delete this ticket?')">
          @csrf
          @method('DELETE')
          <button type="submit" class="btn-danger" style="width:100%">Delete
            Ticket</button>
        </form>
      @endif
    </div>
  </div>

  <div class="comments-section">
    <h3 class="section-title-lg">Comments</h3>

    @if ($canComment)
      <form method="POST" action="{{ route('comments.store', $ticket) }}" class="comment-form">
        @csrf
        <textarea name="comment" rows="3" required placeholder="Write a comment..." class="form-input">{{ old('comment') }}</textarea>
        <button type="submit" class="btn-full-primary" style="width:auto">Add
          Comment</button>
      </form>
    @elseif ($isUser)
      <p class="comment-disabled-msg">
        You cannot comment when a ticket is resolved or closed.
      </p>
    @endif

    <div class="comments-list">
      @forelse ($ticket->comments as $comment)
        <div class="comment-card">
          <div class="comment-header">
            <p class="comment-author">{{ $comment->user->name ?? 'Unknown' }}</p>
            <p class="comment-time">{{ $comment->created_at->diffForHumans() }}</p>
          </div>
          <p class="comment-body">{{ $comment->comment }}</p>
        </div>
      @empty
        <p class="no-comments">No comments yet.</p>
      @endforelse
    </div>
  </div>
@endsection
