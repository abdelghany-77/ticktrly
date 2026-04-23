<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Ticktly')</title>

  @auth
    <meta name="user-id" content="{{ auth()->user()->id }}">
    <meta name="user-role" content="{{ auth()->user()->role }}">
    <meta name="user-category-id" content="{{ auth()->user()->category_id }}">
  @endauth

  {{-- Main CSS --}}
  <link rel="stylesheet" href="{{ asset('css/app.css') }}">

  @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
    @vite(['resources/js/app.js'])
  @endif

  @yield('head')
</head>

<body class="@yield('body-class')">
  @auth
    <div class="app-wrapper">
      <div id="sidebar-overlay" class="sidebar-overlay"></div>
      <!-- Sidebar -->
      <aside id="main-sidebar" class="sidebar">
        <a href="{{ route('dashboard') }}" class="sidebar-logo">
          <div class="sidebar-logo-icon">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
            </svg>
          </div>
          Ticketly
        </a>

        <button id="desktop-sidebar-toggle" class="sidebar-toggle-btn" type="button" aria-label="Toggle sidebar"
          aria-expanded="false">
          <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" width="18" height="18">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
          </svg>
        </button>

        <nav class="sidebar-nav">
          <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round"
                d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
            </svg>
            Dashboard
          </a>

          <a href="{{ route('tickets.index') }}"
            class="{{ request()->routeIs('tickets.index') || request()->routeIs('tickets.show') || request()->routeIs('tickets.edit') ? 'active' : '' }}">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round"
                d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
            </svg>
            Tickets
          </a>

          @if (auth()->user()->role === 'admin')
            <a href="{{ route('tickets.activity') }}"
              class="{{ request()->routeIs('tickets.activity') ? 'active' : '' }}">
              <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              Activity Log
            </a>

            <a href="{{ route('admin.agents.index') }}"
              class="{{ request()->routeIs('admin.agents.*') ? 'active' : '' }}">
              <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                  d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
              </svg>
              Manage Agents
            </a>

            <a href="{{ route('admin.categories.index') }}"
              class="{{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
              <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                  d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
              </svg>
              Categories
            </a>
          @endif

          @if (auth()->user()->role === 'user')
            <div class="sidebar-cta-wrapper">
              <a href="{{ route('tickets.create') }}" class="sidebar-cta">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                New Ticket
              </a>
            </div>
          @endif
        </nav>

        <!-- User short profile in sidebar bottom -->
        <div class="sidebar-user">
          <div class="sidebar-user-avatar">
            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
          </div>
          <div class="sidebar-user-info">
            <p class="sidebar-user-name">{{ auth()->user()->name }}</p>
            <p class="sidebar-user-role">{{ ucfirst(auth()->user()->role) }}</p>
          </div>
        </div>
      </aside>

      <!-- Main Content -->
      <div class="main-area">
        <header class="top-header">
          <div class="top-header-left">
            <button id="mobile-menu-btn" class="mobile-menu-btn">
              <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" width="24"
                height="24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
              </svg>
            </button>
            <h1 class="page-title">@yield('page-title', 'Overview')</h1>
          </div>
          <div class="top-header-right">
            {{-- Notification Bell (all roles) --}}
            <div class="bell-wrapper">
              <button id="notification-bell" type="button" class="bell-btn">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round"
                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                <span id="notification-badge" class="bell-badge">
                  0
                </span>
              </button>

              {{-- Notification Dropdown --}}
              <div id="notification-dropdown" class="notification-dropdown hidden">
                <div class="notification-dropdown-header">Notifications</div>
                <div id="notification-list">
                  <div class="notification-empty">No notifications yet</div>
                </div>
              </div>
            </div>

            <form method="POST" action="{{ route('logout') }}" class="logout-form">
              @csrf
              <button type="submit" class="logout-btn">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round"
                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                Logout
              </button>
            </form>
          </div>
        </header>

        <main class="main-content">
          <div class="content-wrapper">
            @if (session('success'))
              <div class="flash-success">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round"
                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ session('success') }}
              </div>
            @endif

            @if (session('error'))
              <div class="flash-error">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round"
                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ session('error') }}
              </div>
            @endif

            @if ($errors->any())
              <div class="flash-errors">
                <div class="flash-errors-header">
                  <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                      d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  Please fix the following errors:
                </div>
                <ul>
                  @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                  @endforeach
                </ul>
              </div>
            @endif

            @yield('content')
          </div>
        </main>
      </div>
    </div>
  @else
    <main class="guest-wrapper">
      <div>
        @if (session('success'))
          <div class="guest-flash-success">
            {{ session('success') }}
          </div>
        @endif

        @if (session('error'))
          <div class="guest-flash-error">
            {{ session('error') }}
          </div>
        @endif

        @if ($errors->any())
          <div class="guest-flash-errors">
            <ul>
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        @yield('content')
      </div>
    </main>
  @endauth

  {{-- Toast notification container --}}
  <div id="toast-container"></div>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const appWrapper = document.querySelector('.app-wrapper');
      const mobileMenuBtn = document.getElementById('mobile-menu-btn');
      const desktopSidebarToggle = document.getElementById('desktop-sidebar-toggle');
      const sidebar = document.getElementById('main-sidebar');
      const overlay = document.getElementById('sidebar-overlay');
      const desktopMedia = window.matchMedia('(min-width: 768px)');

      const setDesktopSidebarState = (collapsed) => {
        if (!appWrapper) {
          return;
        }

        appWrapper.classList.toggle('sidebar-collapsed', collapsed);

        if (desktopSidebarToggle) {
          desktopSidebarToggle.setAttribute('aria-expanded', collapsed ? 'false' : 'true');
          desktopSidebarToggle.classList.toggle('collapsed', collapsed);
        }
      };

      if (appWrapper && desktopMedia.matches) {
        setDesktopSidebarState(true);
      }

      if (desktopSidebarToggle) {
        desktopSidebarToggle.addEventListener('click', () => {
          if (!appWrapper) {
            return;
          }

          const isCollapsed = appWrapper.classList.toggle('sidebar-collapsed');

          desktopSidebarToggle.setAttribute('aria-expanded', isCollapsed ? 'false' : 'true');
          desktopSidebarToggle.classList.toggle('collapsed', isCollapsed);
        });
      }

      desktopMedia.addEventListener('change', (event) => {
        if (!appWrapper) {
          return;
        }

        if (event.matches) {
          setDesktopSidebarState(true);
        } else {
          appWrapper.classList.remove('sidebar-collapsed');
        }
      });

      if (mobileMenuBtn && sidebar && overlay) {
        mobileMenuBtn.addEventListener('click', () => {
          sidebar.classList.add('sidebar-open');
          overlay.classList.add('active');
        });

        overlay.addEventListener('click', () => {
          sidebar.classList.remove('sidebar-open');
          overlay.classList.remove('active');
        });
      }
    });
  </script>
</body>

</html>
