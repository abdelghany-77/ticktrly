@extends('layouts.app')

@section('title', 'Manage Categories')
@section('page-title', 'Manage Categories')

@section('content')
  <div class="section-header">
    <div>
      <p class="section-subtitle">Organize your agents and tickets with categories.</p>
    </div>
    <button id="open-modal-btn" type="button" class="btn-primary">
      <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
      </svg>
      Add New Category
    </button>
  </div>

  <div class="data-table-wrapper">
    <div class="data-table-scroll">
      <table class="data-table">
        <thead>
          <tr>
            <th>Category</th>
            <th>Description</th>
            <th>Agents</th>
            <th>Created</th>
            <th class="text-right">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($categories as $category)
            <tr>
              {{-- Name --}}
              <td>
                <div class="cell-with-avatar">
                  <div class="avatar-circle avatar-blue">
                    {{ strtoupper(substr($category->name, 0, 1)) }}
                  </div>
                  <span class="cell-name">{{ $category->name }}</span>
                </div>
              </td>

              {{-- Description --}}
              <td class="cell-muted">
                {{ Str::limit($category->description, 40) ?? '—' }}
              </td>

              {{-- Agent Count --}}
              <td>
                <span class="badge badge-violet">
                  {{ $category->users_count }} {{ $category->users_count === 1 ? 'agent' : 'agents' }}
                </span>
              </td>

              {{-- Created At --}}
              <td class="cell-muted">
                {{ $category->created_at->diffForHumans() }}
              </td>

              {{-- Actions --}}
              <td class="text-right">
                @if ($category->users_count === 0)
                  <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" class="inline-form"
                    onsubmit="return confirm('Are you sure you want to delete this category?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-danger-ghost show-on-hover">
                      <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                      </svg>
                      Delete
                    </button>
                  </form>
                @else
                  <span class="text-muted-sm">Has agents</span>
                @endif
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="empty-state">
                <div class="empty-state-inner">
                  <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                      d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                  </svg>
                  <span>No categories yet. Click "Add New Category" to create one.</span>
                </div>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  {{-- ── Add Category Modal ── --}}
  <div id="category-modal" style="display:none" class="modal-overlay">
    <div class="modal-box">
      {{-- Header --}}
      <div class="modal-header">
        <div class="modal-header-content">
          <div class="modal-icon modal-icon-blue">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round"
                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
            </svg>
          </div>
          <div>
            <h3 class="modal-title">New Category</h3>
            <p class="modal-subtitle">Create a new category to organize agents</p>
          </div>
        </div>
        <button id="close-modal-btn" type="button" class="modal-close">
          <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      {{-- Body --}}
      <form method="POST" action="{{ route('admin.categories.store') }}">
        @csrf
        <div class="modal-body">
          <div>
            <label for="name" class="form-label">Category Name</label>
            <input id="name" name="name" value="{{ old('name') }}" required placeholder="e.g. Technical Support"
              class="form-input">
            @error('name')
              <p class="form-error">{{ $message }}</p>
            @enderror
          </div>

          <div>
            <label for="description" class="form-label">Description
              <span class="form-label-optional">(optional)</span></label>
            <textarea id="description" name="description" rows="3" placeholder="Brief description of this category..."
              class="form-input">{{ old('description') }}</textarea>
          </div>
        </div>

        {{-- Footer --}}
        <div class="modal-footer">
          <button type="button" id="cancel-modal-btn" class="modal-btn-cancel">
            Cancel
          </button>
          <button type="submit" class="modal-btn-confirm">
            Create Category
          </button>
        </div>
      </form>
    </div>
  </div>

  {{-- Data attribute to signal errors for JS --}}
  @if ($errors->any())
    <div data-has-errors="true" style="display:none"></div>
  @endif

  <script src="{{ asset('js/categories.js') }}"></script>
@endsection
