{{--
  WooCommerce Pagination Template

  Uses the global shopFiltersAPI for unified state management.
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
    x-data="{ currentPage: {{ $paginationCurrentPage }}, totalPages: {{ $paginationTotalPages }}, isLoading: false }"
    @shop-filters-updated.window="currentPage = $event.detail.currentPage; totalPages = $event.detail.totalPages"
  >
    <ul class="flex items-center gap-1">
      {{-- Previous Button --}}
      <li>
        <button
          type="button"
          class="flex h-10 w-10 items-center justify-center rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
          :class="currentPage > 1 ? 'text-secondary-600 hover:bg-secondary-100 hover:text-secondary-900' : 'text-secondary-300 cursor-not-allowed'"
          :disabled="currentPage <= 1 || isLoading"
          @click="if (window.shopFiltersAPI) window.shopFiltersAPI.goToPage(currentPage - 1)"
          aria-label="{{ __('Previous page', 'sage') }}"
        >
          <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
          </svg>
        </button>
      </li>

      {{-- Page Numbers --}}
      @php
        $range = 2;
        $showDots = false;
      @endphp

      @for ($i = 1; $i <= $paginationTotalPages; $i++)
        @if ($i == 1 || $i == $paginationTotalPages || ($i >= $paginationCurrentPage - $range && $i <= $paginationCurrentPage + $range))
          @php $showDots = true; @endphp
          <li>
            <button
              type="button"
              class="flex h-10 w-10 items-center justify-center rounded-lg text-sm font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
              :class="currentPage === {{ $i }} ? 'bg-primary-600 text-white' : 'text-secondary-700 hover:bg-secondary-100'"
              @click="if (window.shopFiltersAPI) window.shopFiltersAPI.goToPage({{ $i }})"
              :disabled="isLoading"
              :aria-current="currentPage === {{ $i }} ? 'page' : null"
            >
              {{ $i }}
            </button>
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
      <li>
        <button
          type="button"
          class="flex h-10 w-10 items-center justify-center rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
          :class="currentPage < totalPages ? 'text-secondary-600 hover:bg-secondary-100 hover:text-secondary-900' : 'text-secondary-300 cursor-not-allowed'"
          :disabled="currentPage >= totalPages || isLoading"
          @click="if (window.shopFiltersAPI) window.shopFiltersAPI.goToPage(currentPage + 1)"
          aria-label="{{ __('Next page', 'sage') }}"
        >
          <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
          </svg>
        </button>
      </li>
    </ul>
  </nav>
@endif
