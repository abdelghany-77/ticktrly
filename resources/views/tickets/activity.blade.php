@extends('layouts.app')

@section('title', 'Activity Log')
@section('page-title', 'Ticket Activity Log')

@section('content')
  <div class="filter-bar">
    <form method="GET" action="{{ route('tickets.activity') }}">
      <div class="search-field-wrapper">
        <div class="search-icon">
          <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
        </div>
        <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Search by Ticket ID..."
          class="search-input">
      </div>

      <button type="submit" class="btn-primary">
        Search
      </button>

      @if (request()->filled('search'))
        <a href="{{ route('tickets.activity') }}" class="btn-clear">
          Clear
        </a>
      @endif
    </form>
  </div>

  <div class="activity-table-wrapper">
    <div class="data-table-scroll">
      <table class="activity-table">
        <thead>
          <tr>
            <th>Time</th>
            <th>User</th>
            <th>Ticket</th>
            <th>Action</th>
            <th>Changes</th>
          </tr>
        </thead>
        <tbody>
          @forelse($activities as $activity)
            <tr>
              <td class="td-time">
                {{ $activity->created_at->diffForHumans() }}
              </td>
              <td class="td-user">
                {{ $activity->user->name ?? 'Unknown' }}
              </td>
              <td class="td-ticket">
                @if ($activity->ticket)
                  <a href="{{ route('tickets.show', $activity->ticket->id) }}">
                    #{{ $activity->ticket->id }} - {{ Str::limit($activity->ticket->title, 30) }}
                  </a>
                @else
                  <span class="cell-muted"  style="font-size:0.875rem">Deleted (#{{ $activity->ticket_id }})</span>
                @endif
              </td>
              <td>
                <span class="badge-action">
                  {{ str_replace('_', ' ', Str::title($activity->action)) }}
                </span>
              </td>
              <td class="td-changes">
                @if ($activity->action === 'status_changed' || $activity->action === 'priority_changed')
                  Changed from <span class="changes-tag">{{ $activity->old_value }}</span> to <span
                    class="changes-tag">{{ $activity->new_value }}</span>
                @elseif($activity->action === 'assigned')
                  @if ($activity->old_value)
                    Reassigned from <span
                      class="changes-tag">{{ \App\Models\User::find($activity->old_value)?->name ?? 'Unknown' }}</span>
                  @else
                    Assigned
                  @endif
                  to <span
                    class="changes-tag">{{ \App\Models\User::find($activity->new_value)?->name ?? 'Unknown' }}</span>
                @elseif($activity->action === 'commented')
                  <span class="italic-muted">"{{ \Illuminate\Support\Str::limit($activity->new_value, 50) }}"</span>
                @elseif($activity->action === 'created')
                  Ticket created with status <span class="changes-tag">{{ $activity->new_value }}</span>
                @elseif($activity->action === 'deleted')
                  Ticket was deleted
                @endif
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="empty-state">
                <div class="empty-state-inner empty-state-sm">
                  <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  <p>No activity recorded yet.</p>
                </div>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    @if ($activities->hasPages())
      <div class="pagination-wrapper">
        {{ $activities->links() }}
      </div>
    @endif
  </div>
@endsection
