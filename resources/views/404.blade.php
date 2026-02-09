@extends('layouts.app')

@section('content')
  <div class="flex flex-col items-center justify-center py-16 text-center">
    {{-- 404 Error Code --}}
    <div class="mb-6 text-9xl font-bold text-primary-500">
      404
    </div>

    {{-- Title --}}
    <h1 class="mb-4 text-3xl font-bold text-secondary-900 lg:text-4xl">
      {{ __('Not Found', 'sega-woo-theme') }}
    </h1>

    {{-- Description --}}
    <p class="mb-8 max-w-md text-lg text-secondary-600">
      {{ __('Sorry, but the page you are trying to view does not exist.', 'sega-woo-theme') }}
    </p>

    {{-- Search Form --}}
    <div class="mb-8 w-full max-w-md">
      <form role="search" method="get" class="flex gap-2" action="{{ home_url('/') }}">
        <input
          type="search"
          class="flex-1 rounded-lg border border-secondary-300 px-4 py-3 text-secondary-900 placeholder-secondary-400 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20"
          placeholder="{{ esc_attr__('Search products...', 'sega-woo-theme') }}"
          value="{{ get_search_query() }}"
          name="s"
        >
        <button type="submit" class="rounded-lg bg-primary-500 px-6 py-3 font-medium text-white transition-colors hover:bg-primary-600">
          {{ __('Search', 'sega-woo-theme') }}
        </button>
      </form>
    </div>

    {{-- Action Buttons --}}
    <div class="flex flex-wrap justify-center gap-4">
      <a href="{{ home_url('/') }}" class="inline-flex items-center gap-2 rounded-lg border border-secondary-300 bg-white px-6 py-3 font-medium text-secondary-700 transition-colors hover:bg-secondary-50">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
        </svg>
        {{ __('Go to homepage', 'sega-woo-theme') }}
      </a>

      @if (function_exists('wc_get_page_permalink'))
        <a href="{{ wc_get_page_permalink('shop') }}" class="inline-flex items-center gap-2 rounded-lg bg-primary-500 px-6 py-3 font-medium text-white transition-colors hover:bg-primary-600">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
          </svg>
          {{ __('Browse products', 'sega-woo-theme') }}
        </a>
      @endif
    </div>

    {{-- Popular Categories (if WooCommerce is active) --}}
    @if (function_exists('get_terms'))
      @php
        $categories = get_terms([
          'taxonomy' => 'product_cat',
          'hide_empty' => true,
          'parent' => 0,
          'number' => 6,
        ]);
      @endphp

      @if (!is_wp_error($categories) && !empty($categories))
        <div class="mt-12 w-full max-w-2xl">
          <h2 class="mb-4 text-lg font-semibold text-secondary-700">
            {{ __('Popular Categories', 'sega-woo-theme') }}
          </h2>
          <div class="flex flex-wrap justify-center gap-2">
            @foreach ($categories as $category)
              <a href="{{ get_term_link($category) }}" class="rounded-full border border-secondary-200 bg-secondary-50 px-4 py-2 text-sm text-secondary-600 transition-colors hover:border-primary-300 hover:bg-primary-50 hover:text-primary-600">
                {{ $category->name }}
              </a>
            @endforeach
          </div>
        </div>
      @endif
    @endif
  </div>
@endsection
