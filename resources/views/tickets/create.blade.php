@extends('layouts.app')

@section('title', 'Create Ticket')
@section('page-title', 'Create Ticket')

@section('content')
  <div class="create-ticket-card">
    <h2 class="create-ticket-title">Create Ticket</h2>
    <p class="create-ticket-subtitle">Add clear details so an agent can resolve your issue faster.</p>

    <form id="ticket-form" method="POST" action="{{ route('tickets.store') }}" enctype="multipart/form-data"
      class="form-space-y"
      data-ai-search-url="{{ route('tickets.ai-search') }}"
      data-store-url="{{ route('tickets.store') }}">
      @csrf

      <div>
        <label for="title" class="form-label">Title</label>
        <input id="title" name="title" value="{{ old('title') }}" maxlength="255" required
          class="form-input">
      </div>

      <div>
        <label for="description" class="form-label">Description</label>
        <textarea id="description" name="description" rows="5" required
          class="form-input">{{ old('description') }}</textarea>
      </div>

      <div>
        <label for="category_id" class="form-label">Category</label>
        <select id="category_id" name="category_id" required class="form-select">
          <option value="">Select category</option>
          @foreach ($categories as $category)
            <option value="{{ $category->id }}" @selected((int) old('category_id') === $category->id)>{{ $category->name }}</option>
          @endforeach
        </select>
      </div>

      <div>
        <label for="file" class="form-label">Attachment (optional)</label>
        <input id="file" type="file" name="file" class="form-input-file">
        <p class="form-hint">Max file size: 2MB</p>
      </div>

      <button type="submit" id="submit-btn" class="btn-submit">Save
        Ticket</button>
    </form>
  </div>

  {{-- AI Assistant Modal --}}
  <div id="ai-modal" style="display:none" class="modal-overlay">
    <div class="modal-box">
      {{-- Header --}}
      <div class="modal-header" style="justify-content:flex-start">
        <div class="modal-header-content">
          <div class="modal-icon modal-icon-ai">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round"
                d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
            </svg>
          </div>
          <div>
            <h3 class="modal-title">AI Assistant</h3>
            <p class="modal-subtitle">Found a possible solution for your issue</p>
          </div>
        </div>
      </div>

      {{-- Body --}}
      <div class="modal-body">
        <div>
          <span class="ai-match-badge">
            <svg fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd"
                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                clip-rule="evenodd" />
            </svg>
            Match found
          </span>
        </div>
        <p id="ai-question" class="ai-question"></p>
        <div id="ai-answer" class="ai-answer-box"></div>
      </div>

      {{-- Footer --}}
      <div class="modal-footer">
        <button id="btn-create-normal" type="button" class="modal-btn-cancel">
          Create Normal Ticket
        </button>
        <button id="btn-use-answer" type="button" class="modal-btn-ai">
          Use This Answer
        </button>
      </div>
    </div>
  </div>

  <div id="ai-resolved" style="display:none" class="ai-resolved-card">
    <div class="ai-resolved-inner">
      <div class="ai-resolved-header">
        <div class="ai-resolved-icon">
          <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </div>
        <div>
          <h3 class="ai-resolved-title">Issue Resolved by AI</h3>
          <p class="ai-resolved-subtitle">No ticket was created — the answer below should help you</p>
        </div>
      </div>
      <p id="resolved-question" class="ai-question"></p>
      <div id="resolved-answer" class="ai-resolved-answer"></div>
      <div class="ai-resolved-actions">
        <a href="{{ route('tickets.create') }}" class="btn-outline">
          Create a New Ticket Anyway
        </a>
        <a href="{{ route('tickets.index') }}" class="btn-full-primary" style="width:auto;text-align:center">
          Back to Tickets
        </a>
      </div>
    </div>
  </div>

  <script src="{{ asset('js/create-ticket.js') }}"></script>
@endsection
