{{--
  Template: Product Search Results
  Description: Displays product search results with pagination and filtering
--}}

@extends('layouts.app')

@section('breadcrumbs')
  <x-breadcrumbs :items="$breadcrumbs()" />
@endsection

@section('page-header')
  <div class="mb-8">
    {{-- Page Title - Matching Shop Page Style --}}
    <h1 class="text-2xl font-bold text-secondary-900 lg:text-3xl">
      {{ $shopTitle }}
    </h1>

    @if ($hasProducts)
      <p class="mt-3 text-secondary-600">
        {{ sprintf(_n('%d product found', '%d products found', $totalProducts, 'sega-woo-theme'), $totalProducts) }}
      </p>
    @endif

    {{-- Search Again Box --}}
    <div class="mt-6">
      <form action="{{ home_url('/') }}" method="get" class="flex max-w-xl gap-2">
        <input type="hidden" name="post_type" value="product">
        <div class="relative flex-1">
          <input
            type="search"
            name="s"
            value="{{ get_search_query() }}"
            placeholder="{{ __('Search products...', 'sega-woo-theme') }}"
            class="w-full rounded-lg border border-secondary-300 bg-white py-3 pl-11 pr-4 text-secondary-900 placeholder-secondary-400 transition-colors focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20"
          >
          <svg class="absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-secondary-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
        </div>
        <button
          type="submit"
          class="inline-flex items-center gap-2 rounded-lg bg-primary-600 px-5 py-3 text-sm font-medium text-white transition-colors hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
        >
          {{ __('Search', 'sega-woo-theme') }}
        </button>
      </form>
    </div>

    {{-- Active Filters Display --}}
    @php
      $filtersArray = is_callable($activeFilters) ? $activeFilters() : (is_array($activeFilters) ? $activeFilters : []);
    @endphp
    <div id="shop-active-filters" class="{{ empty($filtersArray) ? 'hidden' : '' }}">
      @if (!empty($filtersArray))
        <div class="mt-6 rounded-xl bg-gradient-to-r from-secondary-50 to-secondary-100/50 p-4">
          <div class="flex flex-wrap items-center gap-3">
            <div class="flex items-center gap-2 text-sm font-medium text-secondary-600">
              <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
              </svg>
              <span>{{ __('Filters:', 'sega-woo-theme') }}</span>
            </div>

            <div class="flex flex-wrap items-center gap-2">
              @foreach ($filtersArray as $filter)
                <a
                  href="{{ $filter['remove_url'] }}"
                  class="group inline-flex items-center gap-1.5 rounded-full bg-white px-3 py-1.5 text-sm font-medium text-secondary-700 shadow-sm ring-1 ring-secondary-200 transition-all hover:bg-red-50 hover:text-red-700 hover:ring-red-200"
                  title="{{ __('Remove filter', 'sega-woo-theme') }}"
                >
                  @if ($filter['type'] === 'category')
                    <svg class="h-3.5 w-3.5 text-primary-500 group-hover:text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                    </svg>
                  @elseif ($filter['type'] === 'price')
                    <svg class="h-3.5 w-3.5 text-green-500 group-hover:text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                  @elseif ($filter['type'] === 'search')
                    <svg class="h-3.5 w-3.5 text-blue-500 group-hover:text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                  @else
                    <svg class="h-3.5 w-3.5 text-secondary-400 group-hover:text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                  @endif
                  <span>{!! $filter['label'] !!}</span>
                  <svg class="h-3.5 w-3.5 text-secondary-400 transition-colors group-hover:text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </a>
              @endforeach
            </div>

            @if (count($filtersArray) > 1)
              <a
                href="{{ $shopUrl }}"
                class="ml-auto inline-flex items-center gap-1.5 rounded-full bg-secondary-200/50 px-3 py-1.5 text-sm font-medium text-secondary-600 transition-all hover:bg-secondary-200 hover:text-secondary-800"
              >
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                {{ __('Clear all', 'sega-woo-theme') }}
              </a>
            @endif
          </div>
        </div>
      @endif
    </div>
  </div>
@endsection

@section('sidebar')
  @include('partials.sidebar-shop')
@endsection

@section('content')
  {{-- WooCommerce loop is set up by SearchResults View Composer via $searchProducts --}}

  {{-- AJAX Loading Overlay --}}
  <div id="shop-loading-overlay" class="pointer-events-none fixed inset-0 z-50 flex hidden items-center justify-center bg-white/70">
    <div class="flex flex-col items-center gap-3">
      <svg class="h-10 w-10 animate-spin text-primary-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
      </svg>
      <span class="text-sm font-medium text-secondary-600">{{ __('Loading...', 'sega-woo-theme') }}</span>
    </div>
  </div>

  @if ($hasProducts)
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
      <div id="shop-result-count">
        @include('woocommerce.loop.result-count')
      </div>

      <div class="flex items-center gap-3">
        @include('woocommerce.loop.per-page')
        @include('woocommerce.loop.orderby')
      </div>
    </div>

    <div id="shop-products-grid">
      <ul class="products grid gap-3 xs:gap-4 sm:gap-5 lg:gap-6 xl:gap-8 {{ $gridClasses }}">
        @while (have_posts())
          @php
            the_post();
            $product = wc_get_product(get_the_ID());
          @endphp
          <li class="flex">
            <x-product-card :product="$product" class="w-full" />
          </li>
        @endwhile
      </ul>
    </div>

    {{-- Search Results Pagination --}}
    @if ($totalPages > 1)
      <nav
        class="mt-10 flex items-center justify-center"
        aria-label="{{ __('Search results pagination', 'sega-woo-theme') }}"
        role="navigation"
      >
        @php
          $searchQuery = get_search_query();
          $baseUrl = add_query_arg([
            's' => $searchQuery,
            'post_type' => 'product',
          ], home_url('/'));

          // Preserve other query params
          $preserveParams = ['orderby', 'per_page', 'min_price', 'max_price'];
          foreach ($preserveParams as $param) {
            if (isset($_GET[$param]) && $_GET[$param] !== '') {
              $baseUrl = add_query_arg($param, sanitize_text_field($_GET[$param]), $baseUrl);
            }
          }
        @endphp

        <ul class="flex items-center gap-1">
          {{-- Previous Button --}}
          <li>
            @if ($currentPage > 1)
              <a
                href="{{ add_query_arg('paged', $currentPage - 1, $baseUrl) }}"
                class="flex h-10 w-10 items-center justify-center rounded-lg text-secondary-600 transition-colors hover:bg-secondary-100 hover:text-secondary-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
                aria-label="{{ __('Previous page', 'sega-woo-theme') }}"
              >
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
              </a>
            @else
              <span class="flex h-10 w-10 cursor-not-allowed items-center justify-center rounded-lg text-secondary-300">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
              </span>
            @endif
          </li>

          {{-- Page Numbers --}}
          @php
            $range = 2;
            $showDots = false;
          @endphp

          @for ($i = 1; $i <= $totalPages; $i++)
            @if ($i == 1 || $i == $totalPages || ($i >= $currentPage - $range && $i <= $currentPage + $range))
              @php $showDots = true; @endphp
              <li>
                @if ($i == $currentPage)
                  <span
                    class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary-600 text-sm font-medium text-white"
                    aria-current="page"
                  >
                    {{ $i }}
                  </span>
                @else
                  <a
                    href="{{ add_query_arg('paged', $i, $baseUrl) }}"
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
          <li>
            @if ($currentPage < $totalPages)
              <a
                href="{{ add_query_arg('paged', $currentPage + 1, $baseUrl) }}"
                class="flex h-10 w-10 items-center justify-center rounded-lg text-secondary-600 transition-colors hover:bg-secondary-100 hover:text-secondary-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
                aria-label="{{ __('Next page', 'sega-woo-theme') }}"
              >
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
              </a>
            @else
              <span class="flex h-10 w-10 cursor-not-allowed items-center justify-center rounded-lg text-secondary-300">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
              </span>
            @endif
          </li>
        </ul>
      </nav>
    @endif

  @else
    {{-- No Products Found - Matching Shop Page Style --}}
    <div class="flex flex-col items-center justify-center py-16 text-center">
      <svg class="mb-4 h-16 w-16 text-secondary-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
      </svg>

      <h2 class="mb-2 text-xl font-semibold text-secondary-900">
        {{ __('No products found', 'sega-woo-theme') }}
      </h2>

      <p class="mb-6 max-w-sm text-secondary-600">
        {{ sprintf(__('No products matched your search for "%s". Try using different keywords or browse our categories.', 'sega-woo-theme'), get_search_query()) }}
      </p>

      <div class="flex flex-wrap justify-center gap-3">
        <a
          href="{{ $shopUrl }}"
          class="inline-flex items-center gap-2 rounded-lg bg-primary-600 px-5 py-2.5 text-sm font-medium text-white transition-colors hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
        >
          {{ __('View all products', 'sega-woo-theme') }}
        </a>

        <a
          href="{{ home_url('/') }}"
          class="inline-flex items-center gap-2 rounded-lg border border-secondary-300 bg-white px-5 py-2.5 text-sm font-medium text-secondary-700 transition-colors hover:bg-secondary-50 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
        >
          {{ __('Return to homepage', 'sega-woo-theme') }}
        </a>
      </div>
    </div>
  @endif
@endsection
