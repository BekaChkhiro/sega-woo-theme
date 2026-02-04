{{--
  WooCommerce Pagination Template
--}}

@php
  // Get pagination values
  $paginationTotalPages = $totalPages ?? 1;
  $paginationCurrentPage = $currentPage ?? 1;

  // Fallback to global query
  if ($paginationTotalPages <= 1) {
    global $wp_query;
    $paginationTotalPages = (int) ($wp_query->max_num_pages ?? 1);
    $paginationCurrentPage = max(1, get_query_var('paged', 1));
  }
@endphp

@if (($hasProducts ?? true) && $paginationTotalPages > 1)
  <nav
    class="mt-10 flex items-center justify-center"
    aria-label="{{ __('Product pagination', 'sage') }}"
    role="navigation"
  >
    <ul class="flex items-center gap-1">
      {{-- Previous Button --}}
      @if ($paginationCurrentPage > 1)
        <li>
          <a
            href="{{ get_pagenum_link($paginationCurrentPage - 1) }}"
            class="flex h-10 w-10 items-center justify-center rounded-lg text-secondary-600 transition-colors hover:bg-secondary-100 hover:text-secondary-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
            aria-label="{{ __('Previous page', 'sage') }}"
          >
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
          </a>
        </li>
      @else
        <li>
          <span class="flex h-10 w-10 items-center justify-center rounded-lg text-secondary-300 cursor-not-allowed">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
          </span>
        </li>
      @endif

      {{-- Page Numbers --}}
      @php
        $range = 2;
        $showDots = false;
      @endphp

      @for ($i = 1; $i <= $paginationTotalPages; $i++)
        @if ($i == 1 || $i == $paginationTotalPages || ($i >= $paginationCurrentPage - $range && $i <= $paginationCurrentPage + $range))
          @php $showDots = true; @endphp
          <li>
            @if ($i == $paginationCurrentPage)
              <span
                class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary-600 text-sm font-medium text-white"
                aria-current="page"
              >
                {{ $i }}
              </span>
            @else
              <a
                href="{{ get_pagenum_link($i) }}"
                class="flex h-10 w-10 items-center justify-center rounded-lg text-sm font-medium text-secondary-700 transition-colors hover:bg-secondary-100 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
              >
                {{ $i }}
              </a>
            @endif
          </li>
        @elseif ($showDots)
          @php $showDots = false; @endphp
          <li>
            <span class="flex h-10 w-10 items-center justify-center text-secondary-400">
              &hellip;
            </span>
          </li>
        @endif
      @endfor

      {{-- Next Button --}}
      @if ($paginationCurrentPage < $paginationTotalPages)
        <li>
          <a
            href="{{ get_pagenum_link($paginationCurrentPage + 1) }}"
            class="flex h-10 w-10 items-center justify-center rounded-lg text-secondary-600 transition-colors hover:bg-secondary-100 hover:text-secondary-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
            aria-label="{{ __('Next page', 'sage') }}"
          >
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
            </svg>
          </a>
        </li>
      @else
        <li>
          <span class="flex h-10 w-10 items-center justify-center rounded-lg text-secondary-300 cursor-not-allowed">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
            </svg>
          </span>
        </li>
      @endif
    </ul>
  </nav>
@endif
