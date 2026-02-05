@extends('layouts.app')

@section('container-class', '')

@section('hero')
  {{-- Hero Section: Mega Menu (20%) + Slider (80%) --}}
  <section class="hero-section section-bg-secondary py-4 lg:py-6">
    <div class="shop-container grid gap-4 lg:grid-cols-5 lg:gap-6">
      {{-- Mega Menu (20% width = 1 col out of 5 on desktop) --}}
      <div class="mega-menu-wrapper hidden h-[300px] sm:h-[370px] lg:col-span-1 lg:block lg:h-[440px]">
        <x-mega-menu
          mode="menu"
          menu-location="mega_menu"
          :limit="0"
          :show-product-count="false"
          :show-thumbnails="true"
          :show-view-all="false"
          :title="__('Categories', 'sage')"
          class="h-full"
        />
      </div>

      {{-- Hero Slider (80% width = 4 cols out of 5 on desktop) --}}
      <div class="hero-slider-wrapper h-[300px] sm:h-[370px] lg:col-span-4 lg:h-[440px]">
        <x-hero-slider
          :autoplay="true"
          :delay="5000"
          :show-navigation="true"
          :show-pagination="true"
        />
      </div>
    </div>
  </section>
@endsection

@section('content')
  {{-- Featured Categories Section --}}
  @if ($hasFeaturedCategories)
    <section class="homepage-section section-bg-primary section-padding shop-container">
      <div class="carousel-section-header">
        <div class="carousel-title-group">
          <span class="section-title-badge bg-primary-100">
            <svg class="h-5 w-5 text-primary-600 lg:h-6 lg:w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
            </svg>
          </span>
          <h2 class="text-xl font-bold text-secondary-900 lg:text-2xl">
            {{ __('Shop by Category', 'sage') }}
          </h2>
        </div>
        <a href="{{ $shopUrl }}" class="view-all-link">
          {{ __('View All', 'sage') }}
          <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
          </svg>
        </a>
      </div>

      <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 sm:gap-4 lg:grid-cols-6 lg:gap-6">
        @foreach ($featuredCategories as $category)
          <a
            href="{{ $category['url'] }}"
            class="category-card group flex flex-col items-center rounded-xl border border-secondary-200 bg-white p-4 text-center lg:p-6"
          >
            <div class="category-icon mb-3 flex h-16 w-16 items-center justify-center overflow-hidden rounded-full bg-secondary-100 transition-colors group-hover:bg-primary-50 lg:h-20 lg:w-20">
              @if ($category['thumbnail'])
                <img
                  src="{{ $category['thumbnail'] }}"
                  alt="{{ $category['name'] }}"
                  class="h-full w-full object-cover"
                  loading="lazy"
                >
              @else
                <svg class="h-8 w-8 text-secondary-400 lg:h-10 lg:w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
              @endif
            </div>
            <h3 class="text-sm font-medium text-secondary-900 transition-colors group-hover:text-primary-600 lg:text-base">
              {{ $category['name'] }}
            </h3>
            <span class="mt-1 text-xs text-secondary-500">
              {{ sprintf(_n('%d product', '%d products', $category['count'], 'sage'), $category['count']) }}
            </span>
          </a>
        @endforeach
      </div>
    </section>
  @endif

  {{-- New Products Carousel Section --}}
  @if ($hasNewProducts)
    <section class="homepage-section section-bg-secondary section-padding">
      <div class="shop-container">
        {{-- Custom header with sparkle badge --}}
        <div class="carousel-section-header">
          <div class="carousel-title-group">
            <span class="section-title-badge bg-emerald-100">
              <svg class="h-5 w-5 text-emerald-600 lg:h-6 lg:w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
              </svg>
            </span>
            <div>
              <h2 class="text-xl font-bold text-secondary-900 lg:text-2xl">
                {{ __('New Arrivals', 'sage') }}
              </h2>
              <p class="mt-0.5 hidden text-sm text-secondary-600 sm:block">
                {{ __('Fresh products just added to our store', 'sage') }}
              </p>
            </div>
          </div>
          <a href="{{ $newArrivalsUrl }}" class="view-all-link">
            {{ __('View All', 'sage') }}
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
            </svg>
          </a>
        </div>

        {{-- Product Carousel --}}
        @include('partials.carousel', [
          'products' => $newProducts,
          'title' => '',
          'id' => 'carousel-new-products',
          'slidesPerView' => 4,
          'spaceBetween' => 24,
          'autoplay' => false,
          'loop' => true,
          'navigation' => true,
          'pagination' => false,
          'showHeader' => false,
        ])
      </div>
    </section>
  @endif

  {{-- Promotional Banners --}}
  <section class="homepage-section section-bg-primary section-padding shop-container">
    <div class="grid gap-4 md:grid-cols-2 lg:gap-6">
      {{-- Promo Card 1: Free Shipping --}}
      <div class="promo-card bg-gradient-to-br from-amber-400 to-orange-500 p-6 lg:p-8">
        <div class="relative z-10 max-w-xs">
          <span class="promo-badge mb-2 text-white">
            {{ __('Limited Time', 'sage') }}
          </span>
          <h3 class="mb-2 text-xl font-bold text-white lg:text-2xl">
            {{ __('Free Shipping', 'sage') }}
          </h3>
          <p class="mb-4 text-sm text-white/90">
            {{ __('On all orders over $50. Shop now and save on delivery!', 'sage') }}
          </p>
          <a href="{{ $shopUrl }}" class="promo-cta bg-white text-orange-600 hover:bg-orange-50">
            {{ __('Shop Now', 'sage') }}
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
            </svg>
          </a>
        </div>
        {{-- Decorative circles --}}
        <div class="promo-circle -bottom-8 -right-8 h-40 w-40 lg:h-48 lg:w-48"></div>
        <div class="promo-circle -bottom-4 right-16 h-20 w-20 lg:h-24 lg:w-24"></div>
      </div>

      {{-- Promo Card 2: Exclusive Deals --}}
      <div class="promo-card bg-gradient-to-br from-indigo-500 to-purple-600 p-6 lg:p-8">
        <div class="relative z-10 max-w-xs">
          <span class="promo-badge mb-2 text-white">
            {{ __('Members Only', 'sage') }}
          </span>
          <h3 class="mb-2 text-xl font-bold text-white lg:text-2xl">
            {{ __('Exclusive Deals', 'sage') }}
          </h3>
          <p class="mb-4 text-sm text-white/90">
            {{ __('Sign up for our newsletter and get 10% off your first order.', 'sage') }}
          </p>
          <a href="{{ $myAccountUrl }}" class="promo-cta bg-white text-indigo-600 hover:bg-indigo-50">
            {{ __('Join Now', 'sage') }}
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
            </svg>
          </a>
        </div>
        {{-- Decorative circles --}}
        <div class="promo-circle -bottom-8 -right-8 h-40 w-40 lg:h-48 lg:w-48"></div>
        <div class="promo-circle -bottom-4 right-16 h-20 w-20 lg:h-24 lg:w-24"></div>
      </div>
    </div>
  </section>

  {{-- On Sale Products Carousel Section --}}
  @if ($hasSaleProducts)
    <section class="homepage-section section-bg-primary section-padding">
      <div class="shop-container">
        {{-- Custom header with sale badge --}}
        <div class="carousel-section-header">
          <div class="carousel-title-group">
            <span class="section-title-badge bg-red-100">
              <svg class="h-5 w-5 text-red-600 lg:h-6 lg:w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </span>
            <div>
              <h2 class="text-xl font-bold text-secondary-900 lg:text-2xl">
                {{ __('On Sale', 'sage') }}
              </h2>
              <p class="mt-0.5 hidden text-sm text-secondary-600 sm:block">
                {{ __("Don't miss these amazing deals", 'sage') }}
              </p>
            </div>
            {{-- Sale pulse indicator --}}
            <span class="sale-countdown ml-2 hidden sm:inline-flex">
              <span class="sale-countdown-pulse"></span>
              {{ __('Hot Deals', 'sage') }}
            </span>
          </div>
          <a href="{{ $onSaleUrl }}" class="view-all-link">
            {{ __('View All', 'sage') }}
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
            </svg>
          </a>
        </div>

        {{-- Product Carousel --}}
        @include('partials.carousel', [
          'products' => $saleProducts,
          'title' => '',
          'id' => 'carousel-on-sale',
          'slidesPerView' => 4,
          'spaceBetween' => 24,
          'autoplay' => false,
          'loop' => true,
          'navigation' => true,
          'pagination' => false,
          'showHeader' => false,
        ])
      </div>
    </section>
  @endif

  {{-- Bestsellers Carousel Section --}}
  @if ($hasBestsellers)
    <section class="homepage-section section-bg-secondary section-padding">
      <div class="shop-container">
        {{-- Custom header with star badge --}}
        <div class="carousel-section-header">
          <div class="carousel-title-group">
            <span class="section-title-badge bg-amber-100">
              <svg class="h-5 w-5 text-amber-600 lg:h-6 lg:w-6" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
              </svg>
            </span>
            <div>
              <h2 class="text-xl font-bold text-secondary-900 lg:text-2xl">
                {{ __('Bestsellers', 'sage') }}
              </h2>
              <p class="mt-0.5 hidden text-sm text-secondary-600 sm:block">
                {{ __('Most popular products this month', 'sage') }}
              </p>
            </div>
            {{-- Bestseller badge --}}
            <span class="bestseller-badge ml-2 hidden sm:inline-flex">
              <svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M5 2a2 2 0 00-2 2v14l3.5-2 3.5 2 3.5-2 3.5 2V4a2 2 0 00-2-2H5zm2.5 3a1.5 1.5 0 100 3 1.5 1.5 0 000-3zm6.207.293a1 1 0 00-1.414 0l-6 6a1 1 0 101.414 1.414l6-6a1 1 0 000-1.414zM12.5 10a1.5 1.5 0 100 3 1.5 1.5 0 000-3z" clip-rule="evenodd" />
              </svg>
              {{ __('Top Rated', 'sage') }}
            </span>
          </div>
          <a href="{{ $bestsellersUrl }}" class="view-all-link">
            {{ __('View All', 'sage') }}
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
            </svg>
          </a>
        </div>

        {{-- Product Carousel --}}
        @include('partials.carousel', [
          'products' => $bestsellers,
          'title' => '',
          'id' => 'carousel-bestsellers',
          'slidesPerView' => 4,
          'spaceBetween' => 24,
          'autoplay' => false,
          'loop' => true,
          'navigation' => true,
          'pagination' => false,
          'showHeader' => false,
        ])
      </div>
    </section>
  @endif

  {{-- Features/Trust Badges Section --}}
  <section class="homepage-section section-bg-primary section-padding shop-container">
    <div class="trust-badges-grid">
      {{-- Free Shipping --}}
      <div class="trust-badge flex flex-col items-center rounded-xl border border-secondary-200 bg-white p-4 text-center lg:p-6">
        <div class="trust-badge-icon mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-primary-100 lg:h-14 lg:w-14">
          <svg class="h-6 w-6 text-primary-600 lg:h-7 lg:w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" />
          </svg>
        </div>
        <h3 class="trust-badge-title mb-1 text-sm font-semibold text-secondary-900 lg:text-base">
          {{ __('Free Shipping', 'sage') }}
        </h3>
        <p class="text-xs text-secondary-600 lg:text-sm">
          {{ __('On orders over $50', 'sage') }}
        </p>
      </div>

      {{-- Secure Payment --}}
      <div class="trust-badge flex flex-col items-center rounded-xl border border-secondary-200 bg-white p-4 text-center lg:p-6">
        <div class="trust-badge-icon mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-green-100 lg:h-14 lg:w-14">
          <svg class="h-6 w-6 text-green-600 lg:h-7 lg:w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
          </svg>
        </div>
        <h3 class="trust-badge-title mb-1 text-sm font-semibold text-secondary-900 lg:text-base">
          {{ __('Secure Payment', 'sage') }}
        </h3>
        <p class="text-xs text-secondary-600 lg:text-sm">
          {{ __('100% protected', 'sage') }}
        </p>
      </div>

      {{-- 24/7 Support --}}
      <div class="trust-badge flex flex-col items-center rounded-xl border border-secondary-200 bg-white p-4 text-center lg:p-6">
        <div class="trust-badge-icon mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-blue-100 lg:h-14 lg:w-14">
          <svg class="h-6 w-6 text-blue-600 lg:h-7 lg:w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 8.511c.884.284 1.5 1.128 1.5 2.097v4.286c0 1.136-.847 2.1-1.98 2.193-.34.027-.68.052-1.02.072v3.091l-3-3c-1.354 0-2.694-.055-4.02-.163a2.115 2.115 0 01-.825-.242m9.345-8.334a2.126 2.126 0 00-.476-.095 48.64 48.64 0 00-8.048 0c-1.131.094-1.976 1.057-1.976 2.192v4.286c0 .837.46 1.58 1.155 1.951m9.345-8.334V6.637c0-1.621-1.152-3.026-2.76-3.235A48.455 48.455 0 0011.25 3c-2.115 0-4.198.137-6.24.402-1.608.209-2.76 1.614-2.76 3.235v6.226c0 1.621 1.152 3.026 2.76 3.235.577.075 1.157.14 1.74.194V21l4.155-4.155" />
          </svg>
        </div>
        <h3 class="trust-badge-title mb-1 text-sm font-semibold text-secondary-900 lg:text-base">
          {{ __('24/7 Support', 'sage') }}
        </h3>
        <p class="text-xs text-secondary-600 lg:text-sm">
          {{ __('Dedicated support', 'sage') }}
        </p>
      </div>

      {{-- Easy Returns --}}
      <div class="trust-badge flex flex-col items-center rounded-xl border border-secondary-200 bg-white p-4 text-center lg:p-6">
        <div class="trust-badge-icon mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-purple-100 lg:h-14 lg:w-14">
          <svg class="h-6 w-6 text-purple-600 lg:h-7 lg:w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12c0-1.232-.046-2.453-.138-3.662a4.006 4.006 0 00-3.7-3.7 48.678 48.678 0 00-7.324 0 4.006 4.006 0 00-3.7 3.7c-.017.22-.032.441-.046.662M19.5 12l3-3m-3 3l-3-3m-12 3c0 1.232.046 2.453.138 3.662a4.006 4.006 0 003.7 3.7 48.656 48.656 0 007.324 0 4.006 4.006 0 003.7-3.7c.017-.22.032-.441.046-.662M4.5 12l3 3m-3-3l-3 3" />
          </svg>
        </div>
        <h3 class="trust-badge-title mb-1 text-sm font-semibold text-secondary-900 lg:text-base">
          {{ __('Easy Returns', 'sage') }}
        </h3>
        <p class="text-xs text-secondary-600 lg:text-sm">
          {{ __('30-day return policy', 'sage') }}
        </p>
      </div>
    </div>
  </section>
@endsection
