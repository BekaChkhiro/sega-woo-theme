{{--
  Template: Cart Empty
  Description: Displayed when the cart is empty
  @see https://woocommerce.github.io/code-reference/files/woocommerce-templates-cart-cart-empty.html
--}}

@extends('layouts.app')

@section('breadcrumbs')
  <x-breadcrumbs :items="[
    ['label' => __('Home', 'sega-woo-theme'), 'url' => home_url('/')],
    ['label' => __('Shop', 'sega-woo-theme'), 'url' => wc_get_page_permalink('shop')],
    ['label' => __('Cart', 'sega-woo-theme'), 'url' => null],
  ]" />
@endsection

@section('page-header')
  <div class="mb-8">
    <h1 class="text-2xl font-bold text-secondary-900 lg:text-3xl">
      {{ __('Shopping Cart', 'sega-woo-theme') }}
    </h1>
  </div>
@endsection

@section('content')
  @php
    do_action('woocommerce_cart_is_empty');

    if (wc_get_page_id('shop') > 0) {
      $shopUrl = wc_get_page_permalink('shop');
    } else {
      $shopUrl = home_url('/');
    }
  @endphp

  <div class="flex flex-col items-center justify-center py-16 text-center">
    {{-- Empty Cart Icon --}}
    <div class="mb-6 flex h-24 w-24 items-center justify-center rounded-full bg-secondary-100">
      <svg class="h-12 w-12 text-secondary-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" />
      </svg>
    </div>

    {{-- Empty Cart Message --}}
    <h2 class="mb-2 text-xl font-semibold text-secondary-900">
      {{ __('Your cart is currently empty', 'sega-woo-theme') }}
    </h2>

    <p class="mb-8 max-w-md text-secondary-600">
      {{ __('Looks like you haven\'t added anything to your cart yet. Browse our products and find something you\'ll love!', 'sega-woo-theme') }}
    </p>

    {{-- Return to Shop Button --}}
    <a
      href="{{ esc_url($shopUrl) }}"
      class="inline-flex items-center gap-2 rounded-xl bg-primary-600 px-8 py-4 text-base font-semibold text-white shadow-lg shadow-primary-600/25 transition-all hover:bg-primary-700 hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 active:scale-[0.98]"
    >
      <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
      </svg>
      {{ __('Return to Shop', 'sega-woo-theme') }}
    </a>

    {{-- Additional Help Links --}}
    <div class="mt-12 flex flex-col items-center gap-6 sm:flex-row">
      @if (is_user_logged_in())
        <a
          href="{{ wc_get_account_endpoint_url('orders') }}"
          class="inline-flex items-center gap-2 text-sm text-secondary-600 transition-colors hover:text-primary-600"
        >
          <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
          </svg>
          {{ __('View your orders', 'sega-woo-theme') }}
        </a>
      @endif

      <a
        href="{{ home_url('/') }}"
        class="inline-flex items-center gap-2 text-sm text-secondary-600 transition-colors hover:text-primary-600"
      >
        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
        </svg>
        {{ __('Go to homepage', 'sega-woo-theme') }}
      </a>
    </div>
  </div>

  {{-- Featured Products Section (Optional) --}}
  @php
    $featuredProducts = wc_get_products([
      'status' => 'publish',
      'limit' => 4,
      'featured' => true,
      'visibility' => 'visible',
    ]);

    // Fallback to recent products if no featured products
    if (empty($featuredProducts)) {
      $featuredProducts = wc_get_products([
        'status' => 'publish',
        'limit' => 4,
        'orderby' => 'date',
        'order' => 'DESC',
        'visibility' => 'visible',
      ]);
    }
  @endphp

  @if (!empty($featuredProducts))
    <div class="mt-16 border-t border-secondary-200 pt-12">
      <div class="mb-8 text-center">
        <h3 class="text-xl font-bold text-secondary-900">
          {{ __('Popular Products', 'sega-woo-theme') }}
        </h3>
        <p class="mt-2 text-secondary-600">
          {{ __('Check out some of our most popular items', 'sega-woo-theme') }}
        </p>
      </div>

      <ul class="products grid grid-cols-1 gap-4 xs:grid-cols-2 sm:gap-6 lg:grid-cols-4">
        @foreach ($featuredProducts as $product)
          <li class="flex">
            <x-product-card :product="$product" class="w-full" />
          </li>
        @endforeach
      </ul>

      <div class="mt-8 text-center">
        <a
          href="{{ esc_url($shopUrl) }}"
          class="inline-flex items-center gap-2 text-sm font-medium text-primary-600 transition-colors hover:text-primary-700"
        >
          {{ __('View all products', 'sega-woo-theme') }}
          <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
          </svg>
        </a>
      </div>
    </div>
  @endif
@endsection
