@extends('layouts.app')

@section('title', 'Tickets')
@section('page-title', 'Tickets View')
@section('body-class', 'tickets-index-page')

@section('content')
  @php
    $canSeePriority = auth()->user()->role === 'agent' || auth()->user()->role === 'admin';
    $emptyColumns = $canSeePriority ? 8 : 7;
  @endphp

  @if (auth()->user()->role === 'agent')
    <div class="tabs-bar">
      <a href="{{ route('tickets.index', array_merge(request()->except(['page', 'tab']), ['tab' => 'all'])) }}"
        class="tab-link {{ $activeTab === 'all' ? 'active' : '' }}">
        All Tickets
      </a>
      <a href="{{ route('tickets.index', array_merge(request()->except(['page', 'tab']), ['tab' => 'my-tickets'])) }}"
        class="tab-link {{ $activeTab === 'my-tickets' ? 'active' : '' }}">
        My Tickets
      </a>
      <a href="{{ route('tickets.index', array_merge(request()->except(['page', 'tab']), ['tab' => 'category-tickets'])) }}"
        class="tab-link {{ $activeTab === 'category-tickets' ? 'active' : '' }}">
        Team Tickets
      </a>
    </div>
  @endif

  <div class="filter-bar">
    <form method="GET" action="{{ route('tickets.index') }}">
      @if (auth()->user()->role === 'agent')
        <input type="hidden" name="tab" value="{{ $activeTab }}">
      @endif

      <div class="search-field-wrapper">
        <div class="search-icon">
          <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
        </div>
        <input type="text" name="search" value="{{ $search }}" placeholder="Search by ticket ID or title..."
          class="search-input">
      </div>

      <div class="filter-divider"></div>

      <select name="status" class="filter-select">
        <option value="">All Statuses</option>
        <option value="open" @selected($status === 'open')>Open</option>
        <option value="in_progress" @selected($status === 'in_progress')>In Progress</option>
        <option value="resolved" @selected($status === 'resolved')>Resolved</option>
        <option value="closed" @selected($status === 'closed')>Closed</option>
      </select>

      <button type="submit" class="btn-primary">
        Filter
      </button>
    </form>
  </div>

  <div class="data-table-wrapper">
    <div class="data-table-scroll">
      <table class="data-table">
        <thead>
          <tr>
            <th>ID / Details</th>
            <th>Category</th>
            <th>Status</th>
            @if ($canSeePriority)
              <th>Priority</th>
            @endif
            <th>Owner</th>
            <th>Created At</th>
            <th class="text-right">Action</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($tickets as $ticket)
            <tr>
              <td>
                <div class="cell-name">#{{ $ticket->id }} - {{ $ticket->title }}</div>
              </td>
              <td class="cell-light">
                {{ $ticket->category->name ?? 'None' }}
              </td>
              <td>
                @php
                  if ($ticket->status === 'open') {
                      $statusClasses = 'badge-amber';
                  } elseif ($ticket->status === 'in_progress') {
                      $statusClasses = 'badge-blue';
                  } elseif ($ticket->status === 'resolved' || $ticket->status === 'closed') {
                      $statusClasses = 'badge-emerald';
                  } else {
                      $statusClasses = 'badge-gray';
                  }
                @endphp
                <span class="badge {{ $statusClasses }}">
                  {{ str_replace('_', ' ', $ticket->status) }}
                </span>
              </td>
              @if ($canSeePriority)
                <td>
                  @php
                    if ($ticket->priority === 'high') {
                        $priorityClasses = 'badge-red';
                    } elseif ($ticket->priority === 'medium') {
                        $priorityClasses = 'badge-amber';
                    } else {
                        $priorityClasses = 'badge-blue';
                    }
                  @endphp
                  <span class="badge {{ $priorityClasses }}">
                    {{ $ticket->priority }}
                  </span>
                </td>
              @endif
              <td>
                <div class="cell-with-avatar">
                  <div class="avatar-circle-sm">
                    {{ strtoupper(substr($ticket->user->name ?? 'U', 0, 1)) }}
                  </div>
                  <span class="cell-light">{{ $ticket->user->name ?? 'N/A' }}</span>
                </div>
              </td>
              <td class="cell-muted">
                {{ $ticket->created_at->diffForHumans() }}
              </td>
              <td class="text-right">
                <a href="{{ route('tickets.show', $ticket) }}" class="btn-secondary show-on-hover">
                  View
                  <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                  </svg>
                </a>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="{{ $emptyColumns }}" class="empty-state">
                <div class="empty-state-inner">
                  <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                  </svg>
                  <span>No tickets found matching your criteria.</span>
                </div>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    @if ($tickets->hasPages())
      <div class="pagination-wrapper">
        {{ $tickets->links() }}
      </div>
    @endif
  </div>
@endsection
