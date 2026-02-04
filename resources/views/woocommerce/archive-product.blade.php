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

    @if ($isFiltered && !empty($activeFilters))
      <div class="mt-4 flex flex-wrap items-center gap-2">
        <span class="text-sm text-secondary-600">{{ __('Active filters:', 'sage') }}</span>
        @foreach ($activeFilters as $filter)
          <a
            href="{{ $filter['remove_url'] }}"
            class="inline-flex items-center gap-1 rounded-full bg-secondary-100 px-3 py-1 text-sm text-secondary-700 transition-colors hover:bg-secondary-200"
          >
            {!! $filter['label'] !!}
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </a>
        @endforeach

        <a href="{{ $shopUrl }}" class="text-sm text-primary-600 hover:text-primary-700">
          {{ __('Clear all', 'sage') }}
        </a>
      </div>
    @endif
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

  @if ($hasProducts)
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
      @include('woocommerce.loop.result-count')

      @include('woocommerce.loop.orderby')
    </div>

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

    @include('woocommerce.loop.pagination', [
      'hasProducts' => $hasProducts,
      'totalPages' => $totalPages,
      'currentPage' => $currentPage,
    ])

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
