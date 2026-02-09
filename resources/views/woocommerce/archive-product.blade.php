@extends('layouts.app')

@section('breadcrumbs')
  <x-breadcrumbs :items="$breadcrumbs()" />
@endsection

@section('page-header')
  <div class="mb-8">
    <h1 class="text-2xl font-bold text-secondary-900 lg:text-3xl">
      {{ $shopTitle }}
    </h1>

    @if ($shopDescription)
      <div class="mt-3 max-w-3xl text-secondary-600">
        {!! $shopDescription !!}
      </div>
    @endif

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
              <span>{{ __('Filters:', 'sage') }}</span>
            </div>

            <div class="flex flex-wrap items-center gap-2">
              @foreach ($filtersArray as $filter)
                <a
                  href="{{ $filter['remove_url'] }}"
                  class="group inline-flex items-center gap-1.5 rounded-full bg-white px-3 py-1.5 text-sm font-medium text-secondary-700 shadow-sm ring-1 ring-secondary-200 transition-all hover:bg-red-50 hover:text-red-700 hover:ring-red-200"
                  title="{{ __('Remove filter', 'sage') }}"
                >
                  @if ($filter['type'] === 'category')
                    <svg class="h-3.5 w-3.5 text-primary-500 group-hover:text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                    </svg>
                  @elseif ($filter['type'] === 'price')
                    <svg class="h-3.5 w-3.5 text-green-500 group-hover:text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                  @else
                    <svg class="h-3.5 w-3.5 text-secondary-400 group-hover:text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
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
                {{ __('Clear all', 'sage') }}
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
  @php
    // Ensure WooCommerce loop properties are initialized for pagination
    if (function_exists('wc_setup_loop')) {
      global $wp_query;
      wc_setup_loop([
        'name'         => 'product',
        'is_paginated' => true,
        'total'        => $wp_query->found_posts,
        'total_pages'  => $wp_query->max_num_pages,
        'per_page'     => $productsPerPage ?? wc_get_loop_prop('per_page'),
        'current_page' => max(1, get_query_var('paged', 1)),
      ]);
    }
  @endphp

  {{-- AJAX Loading Overlay --}}
  <div id="shop-loading-overlay" class="pointer-events-none fixed inset-0 z-50 flex hidden items-center justify-center bg-white/70">
    <div class="flex flex-col items-center gap-3">
      <svg class="h-10 w-10 animate-spin text-primary-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
      </svg>
      <span class="text-sm font-medium text-secondary-600">{{ __('Loading...', 'sage') }}</span>
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

    <div id="shop-pagination">
      @include('woocommerce.loop.pagination', [
        'hasProducts' => $hasProducts,
        'totalPages' => $totalPages,
        'currentPage' => $currentPage,
      ])
    </div>

    {{-- Global pagination handler for AJAX-returned pagination --}}
    <script>
      document.addEventListener('click', function(e) {
        const link = e.target.closest('#shop-pagination a.page-numbers');
        if (!link || !window.sageShopAjax) return;

        e.preventDefault();

        const href = link.getAttribute('href');
        if (!href) return;

        const url = new URL(href, window.location.origin);
        const page = parseInt(url.searchParams.get('paged') || '1', 10);

        // Show loading
        const overlay = document.getElementById('shop-loading-overlay');
        if (overlay) overlay.classList.remove('hidden');

        // Build form data
        const currentParams = new URLSearchParams(window.location.search);
        const formData = new FormData();
        formData.append('action', 'filter_products');
        formData.append('nonce', window.sageShopAjax.nonce);

        if (currentParams.has('cat_ids')) {
          // Categories are IDs - parse and deduplicate
          const ids = currentParams.get('cat_ids').split(',')
            .map(c => parseInt(c.trim(), 10))
            .filter(id => id > 0);
          const uniqueIds = [...new Set(ids)];
          uniqueIds.forEach(id => {
            formData.append('categories[]', id);
          });
        }
        if (currentParams.has('min_price')) formData.append('min_price', currentParams.get('min_price'));
        if (currentParams.has('max_price')) formData.append('max_price', currentParams.get('max_price'));
        if (currentParams.has('on_sale')) formData.append('on_sale', currentParams.get('on_sale'));
        if (currentParams.has('in_stock')) formData.append('in_stock', currentParams.get('in_stock'));
        if (currentParams.has('orderby')) formData.append('orderby', currentParams.get('orderby'));
        if (currentParams.has('per_page')) formData.append('per_page', currentParams.get('per_page'));
        formData.append('paged', page);

        fetch(window.sageShopAjax.ajaxUrl, { method: 'POST', body: formData })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              const productsContainer = document.getElementById('shop-products-grid');
              if (productsContainer && data.data.products) productsContainer.innerHTML = data.data.products;

              const resultCount = document.getElementById('shop-result-count');
              if (resultCount && data.data.result_count) {
                resultCount.innerHTML = '<p class="text-sm text-secondary-600">' + data.data.result_count + '</p>';
              }

              const pagination = document.getElementById('shop-pagination');
              if (pagination) pagination.innerHTML = data.data.pagination || '';

              // Update URL with deduplicated category IDs
              const newUrl = new URL(window.sageShopAjax.shopUrl);
              currentParams.forEach((val, key) => {
                if (key === 'paged') return;
                if (key === 'cat_ids') {
                  // Category IDs - parse, deduplicate
                  const ids = val.split(',').map(c => parseInt(c.trim(), 10)).filter(id => id > 0);
                  const uniqueIds = [...new Set(ids)];
                  if (uniqueIds.length > 0) {
                    newUrl.searchParams.set(key, uniqueIds.join(','));
                  }
                } else {
                  newUrl.searchParams.set(key, val);
                }
              });
              if (page > 1) newUrl.searchParams.set('paged', page);
              window.history.pushState({}, '', newUrl.toString());

              // Scroll to products
              const grid = document.getElementById('shop-products-grid');
              if (grid) {
                const top = grid.getBoundingClientRect().top + window.pageYOffset - 100;
                window.scrollTo({ top, behavior: 'smooth' });
              }
            }
          })
          .finally(() => {
            if (overlay) overlay.classList.add('hidden');
          });
      });
    </script>

  @else
    <div class="flex flex-col items-center justify-center py-16 text-center">
      <svg class="mb-4 h-16 w-16 text-secondary-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
        <path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
      </svg>

      <h2 class="mb-2 text-xl font-semibold text-secondary-900">
        {{ __('No products found', 'sage') }}
      </h2>

      <p class="mb-6 max-w-sm text-secondary-600">
        @if ($isSearch)
          {{ __('No products matched your search. Try using different keywords or browse our categories.', 'sage') }}
        @elseif ($isFiltered)
          {{ __('No products match your selected filters. Try adjusting your filter criteria.', 'sage') }}
        @else
          {{ __('There are no products available at the moment. Please check back later.', 'sage') }}
        @endif
      </p>

      <div class="flex flex-wrap justify-center gap-3">
        @if ($isSearch || $isFiltered)
          <a
            href="{{ $shopUrl }}"
            class="inline-flex items-center gap-2 rounded-lg bg-primary-600 px-5 py-2.5 text-sm font-medium text-white transition-colors hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
          >
            {{ __('View all products', 'sage') }}
          </a>
        @endif

        <a
          href="{{ home_url('/') }}"
          class="inline-flex items-center gap-2 rounded-lg border border-secondary-300 bg-white px-5 py-2.5 text-sm font-medium text-secondary-700 transition-colors hover:bg-secondary-50 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
        >
          {{ __('Return to homepage', 'sage') }}
        </a>
      </div>
    </div>
  @endif
@endsection
