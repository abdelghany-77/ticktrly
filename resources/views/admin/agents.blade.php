@extends('layouts.app')

@section('title', 'Manage Agents')
@section('page-title', 'Manage Agents')

@section('content')
  {{-- ── Search & Filter Bar ── --}}
  <div class="filter-bar">
    <form method="GET" action="{{ route('admin.agents.index') }}">
      <div class="search-field-wrapper">
        <div class="search-icon">
          <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
        </div>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name..."
          class="search-input">
      </div>

      <div class="filter-divider"></div>

      <select name="role" class="filter-select">
        <option value="">All Roles</option>
        <option value="user" @selected(request('role') === 'user')>Users</option>
        <option value="agent" @selected(request('role') === 'agent')>Agents</option>
        <option value="admin" @selected(request('role') === 'admin')>Admins</option>
      </select>

      <button type="submit" class="btn-primary">
        Filter
      </button>

      @if (request('search') || request('role'))
        <a href="{{ route('admin.agents.index') }}" class="btn-clear">
          Clear
        </a>
      @endif
    </form>
  </div>

  {{-- ── Users Table ── --}}
  <div class="data-table-wrapper">
    <div class="data-table-scroll">
      <table class="data-table">
        <thead>
          <tr>
            <th>User</th>
            <th>Email</th>
            <th>Role</th>
            <th>Category</th>
            <th class="text-right">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($users as $user)
            <tr>
              {{-- User --}}
              <td>
                <div class="cell-with-avatar">
                  <div
                    class="avatar-circle {{ $user->role === 'admin' ? 'avatar-admin' : ($user->role === 'agent' ? 'avatar-agent' : 'avatar-user') }}">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                  </div>
                  <span class="cell-name">{{ $user->name }}</span>
                </div>
              </td>

              {{-- Email --}}
              <td class="cell-muted">{{ $user->email }}</td>

              {{-- Role --}}
              <td>
                @php
                  $roleColor = match ($user->role) {
                      'admin' => 'badge-amber',
                      'agent' => 'badge-violet',
                      default => 'badge-blue',
                  };
                @endphp
                <span class="badge {{ $roleColor }}">
                  {{ $user->role }}
                </span>
              </td>

              {{-- Category --}}
              <td>
                @if ($user->role === 'agent')
                  <form method="POST" action="{{ route('admin.agents.update-category', $user) }}" class="inline-form">
                    @csrf
                    @method('PATCH')
                    <select name="category_id" onchange="this.form.submit()" class="inline-select">
                      <option value="">No Category</option>
                      @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected($user->category_id == $category->id)>
                          {{ $category->name }}
                        </option>
                      @endforeach
                    </select>
                  </form>
                @elseif($user->role === 'admin')
                  <span class="text-muted-sm">—</span>
                @else
                  <span class="cell-muted" style="font-size:0.75rem">Not an agent</span>
                @endif
              </td>

              {{-- Actions --}}
              <td class="text-right">
                @if ($user->role === 'user')
                  <form method="POST" action="{{ route('admin.agents.update-role', $user) }}" class="inline-form">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="role" value="agent">
                    <button type="submit" class="btn-promote">
                      <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                      </svg>
                      Promote to Agent
                    </button>
                  </form>
                @elseif($user->role === 'agent')
                  <form method="POST" action="{{ route('admin.agents.update-role', $user) }}" class="inline-form">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="role" value="user">
                    <button type="submit" class="btn-demote">
                      <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                      </svg>
                      Demote to User
                    </button>
                  </form>
                @else
                  <span class="text-muted-sm">—</span>
                @endif
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="empty-state">
                <div class="empty-state-inner">
                  <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                      d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                  </svg>
                  <span>No users found matching your criteria.</span>
                </div>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    @if ($users->hasPages())
      <div class="pagination-wrapper">
        {{ $users->links() }}
      </div>
    @endif
  </div>
@endsection
