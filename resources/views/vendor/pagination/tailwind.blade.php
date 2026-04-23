@if ($paginator->hasPages())
  <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="pagination-nav">

    {{-- Mobile: Previous / Next only --}}
    <div class="pagination-mobile">
      @if ($paginator->onFirstPage())
        <span class="pagination-btn pagination-btn-disabled">
          <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
          </svg>
          {!! __('pagination.previous') !!}
        </span>
      @else
        <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="pagination-btn">
          <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
          </svg>
          {!! __('pagination.previous') !!}
        </a>
      @endif

      <span class="pagination-info-mobile">
        Page {{ $paginator->currentPage() }} of {{ $paginator->lastPage() }}
      </span>

      @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="pagination-btn">
          {!! __('pagination.next') !!}
          <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
          </svg>
        </a>
      @else
        <span class="pagination-btn pagination-btn-disabled">
          {!! __('pagination.next') !!}
          <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
          </svg>
        </span>
      @endif
    </div>

    {{-- Desktop: Full pagination --}}
    <div class="pagination-desktop">

      <p class="pagination-showing">
        {!! __('Showing') !!}
        @if ($paginator->firstItem())
          <span>{{ $paginator->firstItem() }}</span>
          {!! __('to') !!}
          <span>{{ $paginator->lastItem() }}</span>
        @else
          {{ $paginator->count() }}
        @endif
        {!! __('of') !!}
        <span>{{ $paginator->total() }}</span>
        {!! __('results') !!}
      </p>

      <div class="pagination-links">
        {{-- Previous --}}
        @if ($paginator->onFirstPage())
          <span class="pagination-link pagination-link-disabled pagination-link-arrow" aria-disabled="true">
            <svg fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd"
                d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                clip-rule="evenodd" />
            </svg>
          </span>
        @else
          <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="pagination-link pagination-link-arrow"
            aria-label="{{ __('pagination.previous') }}">
            <svg fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd"
                d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                clip-rule="evenodd" />
            </svg>
          </a>
        @endif

        {{-- Pages --}}
        @foreach ($elements as $element)
          @if (is_string($element))
            <span class="pagination-link pagination-link-dots" aria-disabled="true">{{ $element }}</span>
          @endif

          @if (is_array($element))
            @foreach ($element as $page => $url)
              @if ($page == $paginator->currentPage())
                <span class="pagination-link pagination-link-active" aria-current="page">{{ $page }}</span>
              @else
                <a href="{{ $url }}" class="pagination-link"
                  aria-label="{{ __('Go to page :page', ['page' => $page]) }}">{{ $page }}</a>
              @endif
            @endforeach
          @endif
        @endforeach

        {{-- Next --}}
        @if ($paginator->hasMorePages())
          <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="pagination-link pagination-link-arrow"
            aria-label="{{ __('pagination.next') }}">
            <svg fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd"
                d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                clip-rule="evenodd" />
            </svg>
          </a>
        @else
          <span class="pagination-link pagination-link-disabled pagination-link-arrow" aria-disabled="true">
            <svg fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd"
                d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                clip-rule="evenodd" />
            </svg>
          </span>
        @endif
      </div>
    </div>
  </nav>
@endif
