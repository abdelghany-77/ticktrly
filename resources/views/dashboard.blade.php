@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
  @php
    $canSeePriority = auth()->user()->role === 'agent' || auth()->user()->role === 'admin';
  @endphp

  {{-- ── Stat Cards ── --}}
  <div class="stats-grid">
    <!-- Stat Card 1 -->
    <div class="stat-card">
      <div class="stat-card-inner">
        <div>
          <p class="stat-label">My Tickets</p>
          <p class="stat-value">{{ $stats['my_tickets'] }}</p>
        </div>
        <div class="stat-icon stat-icon-blue">
          <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round"
              d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
          </svg>
        </div>
      </div>
    </div>

    <!-- Stat Card 2 -->
    <div class="stat-card">
      <div class="stat-card-inner">
        <div>
          <p class="stat-label stat-label-blue">Open Tickets</p>
          <p class="stat-value">{{ $stats['open_tickets'] }}</p>
        </div>
        <div class="stat-icon stat-icon-blue">
          <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </div>
      </div>
    </div>

    <!-- Stat Card 3 -->
    <div class="stat-card">
      <div class="stat-card-inner">
        <div>
          <p class="stat-label stat-label-green">Resolved</p>
          <p class="stat-value">{{ $stats['resolved_tickets'] }}</p>
        </div>
        <div class="stat-icon stat-icon-green">
          <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
          </svg>
        </div>
      </div>
    </div>
  </div>

  @if (auth()->user()->role === 'admin')
    {{-- ── Charts Section (Admin Only) ── --}}
    <div class="charts-grid">

      {{-- 1. Tickets by Category (Bar Chart) --}}
      <div class="chart-card">
        <h3 class="chart-title">
          <svg style="color:#60a5fa" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round"
              d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
          </svg>
          Tickets by Category
        </h3>
        <div class="chart-container">
          <canvas id="categoryChart"></canvas>
        </div>
      </div>

      {{-- 2. Tickets by Status (Pie Chart) --}}
      <div class="chart-card">
        <h3 class="chart-title">
          <svg style="color:#fbbf24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
          </svg>
          Tickets by Status
        </h3>
        <div class="chart-container-center">
          <canvas id="statusChart"></canvas>
        </div>
      </div>

      {{-- 3. AI Resolution Rate (Donut Chart) --}}
      <div class="chart-card">
        <h3 class="chart-title">
          <svg style="color:#a78bfa" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round"
              d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
          </svg>
          AI Resolution Rate
        </h3>
        <div class="chart-container-center">
          <canvas id="aiChart"></canvas>
        </div>
      </div>

      {{-- 4. Tickets by Priority (Horizontal Bar Chart) --}}
      <div class="chart-card">
        <h3 class="chart-title">
          <svg style="color:#fb7185" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12" />
          </svg>
          Tickets by Priority
        </h3>
        <div class="chart-container">
          <canvas id="priorityChart"></canvas>
        </div>
      </div>

    </div>
  @endif

  {{-- ── Recent Activity Table ── --}}
  <div class="activity-card">
    <div class="activity-card-header">
      <h3 class="activity-card-title">Recent Activity</h3>
      <a href="{{ route('tickets.index') }}" class="activity-card-link">View all</a>
    </div>

    <!-- headers -->
    <div class="activity-table-header">
      <div style="width:50%">Details</div>
      <div style="display:flex;width:50%;justify-content:flex-end;gap:2.5rem">
        @if ($canSeePriority)
          <div class="badge-col">Priority</div>
        @endif
        <div class="badge-col-wide">Status</div>
      </div>
    </div>

    <div class="activity-rows">
      @forelse ($recentTickets as $ticket)
        <div class="activity-row">
          <div class="activity-row-details">
            <a href="{{ route('tickets.show', $ticket) }}" class="activity-row-title">
              {{ $ticket->title }}
            </a>
            <p class="activity-row-subtitle">
              Created {{ $ticket->created_at->diffForHumans() }} by {{ $ticket->user->name ?? 'User' }}
            </p>
          </div>
          <div class="activity-row-badges">
            @if ($canSeePriority)
              <div class="badge-col">
                @php
                  if ($ticket->priority === 'high') {
                      $priorityColor = 'badge-red';
                  } elseif ($ticket->priority === 'medium') {
                      $priorityColor = 'badge-amber';
                  } else {
                      $priorityColor = 'badge-blue';
                  }
                @endphp
                <span class="badge-sm {{ $priorityColor }}">
                  {{ $ticket->priority }}
                </span>
              </div>
            @endif
            <div class="badge-col-wide">
              @php
                $statusColor = match ($ticket->status) {
                    'open' => 'badge-amber',
                    'in_progress' => 'badge-blue',
                    'resolved', 'closed' => 'badge-emerald',
                    default => 'badge-gray',
                };
              @endphp
              <span class="badge-sm {{ $statusColor }}">
                {{ str_replace('_', ' ', $ticket->status) }}
              </span>
            </div>
          </div>
        </div>
      @empty
        <div style="padding:1.5rem 2rem;text-align:center">
          <p class="cell-muted">No recent tickets available.</p>
        </div>
      @endforelse
    </div>
  </div>

  @if (auth()->user()->role === 'admin')
    {{-- ── Chart.js CDN ── --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0/dist/chartjs-plugin-datalabels.min.js">
    </script>

    <script>
      window.chartData = @json($chartData);
    </script>
    <script src="{{ asset('js/dashboard-charts.js') }}"></script>
  @endif
@endsection
