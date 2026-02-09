@extends('layouts.app')

@section('container-class', '')

@section('hero')
  {{-- Hero Section: Mega Menu (20%) + Slider (80%) --}}
  <section class="hero-section section-bg-secondary py-4 lg:py-6">
    <div class="shop-container grid gap-4 lg:grid-cols-5 lg:gap-6">
      {{-- Mega Menu (20% width = 1 col out of 5 on desktop) --}}
      <div class="mega-menu-wrapper hidden h-[385px] sm:h-[455px] lg:col-span-1 lg:block lg:h-[525px]">
        <x-mega-menu
          mode="menu"
          menu-location="mega_menu"
          :limit="0"
          :show-product-count="false"
          :show-thumbnails="true"
          :show-view-all="false"
          :title="__('Categories', 'sega-woo-theme')"
          class="h-full"
        />
      </div>

      {{-- Hero Slider (80% width = 4 cols out of 5 on desktop) --}}
      <div class="hero-slider-wrapper h-[385px] sm:h-[455px] lg:col-span-4 lg:h-[525px]">
        <x-hero-slider
          :slides="$sliderSlides"
          :autoplay="$sliderSettings['autoplay'] ?? true"
          :delay="$sliderSettings['delay'] ?? 5000"
          :show-navigation="$sliderSettings['navigation'] ?? true"
          :show-pagination="$sliderSettings['pagination'] ?? true"
        />
      </div>
    </div>
  </section>
@endsection

@section('content')
  {{-- Featured Categories Carousel Section --}}
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
            {{ __('Shop by Category', 'sega-woo-theme') }}
          </h2>
        </div>
        <a href="{{ $shopUrl }}" class="view-all-link">
          {{ __('View All', 'sega-woo-theme') }}
          <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
          </svg>
        </a>
      </div>

      {{-- Category Carousel --}}
      @include('partials.category-carousel', [
        'categories' => $featuredCategories,
        'id' => 'homepage-category-carousel',
        'slidesPerView' => 6,
        'spaceBetween' => 24,
        'loop' => true,
        'navigation' => true,
      ])
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
                {{ __('New Arrivals', 'sega-woo-theme') }}
              </h2>
              <p class="mt-0.5 hidden text-sm text-secondary-600 sm:block">
                {{ __('Fresh products just added to our store', 'sega-woo-theme') }}
              </p>
            </div>
          </div>
          <a href="{{ $newArrivalsUrl }}" class="view-all-link">
            {{ __('View All', 'sega-woo-theme') }}
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
                {{ __('On Sale', 'sega-woo-theme') }}
              </h2>
              <p class="mt-0.5 hidden text-sm text-secondary-600 sm:block">
                {{ __("Don't miss these amazing deals", 'sega-woo-theme') }}
              </p>
            </div>
            {{-- Sale pulse indicator --}}
            <span class="sale-countdown ml-2 hidden sm:inline-flex">
              <span class="sale-countdown-pulse"></span>
              {{ __('Hot Deals', 'sega-woo-theme') }}
            </span>
          </div>
          <a href="{{ $onSaleUrl }}" class="view-all-link">
            {{ __('View All', 'sega-woo-theme') }}
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
                {{ __('Bestsellers', 'sega-woo-theme') }}
              </h2>
              <p class="mt-0.5 hidden text-sm text-secondary-600 sm:block">
                {{ __('Most popular products this month', 'sega-woo-theme') }}
              </p>
            </div>
            {{-- Bestseller badge --}}
            <span class="bestseller-badge ml-2 hidden sm:inline-flex">
              <svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M5 2a2 2 0 00-2 2v14l3.5-2 3.5 2 3.5-2 3.5 2V4a2 2 0 00-2-2H5zm2.5 3a1.5 1.5 0 100 3 1.5 1.5 0 000-3zm6.207.293a1 1 0 00-1.414 0l-6 6a1 1 0 101.414 1.414l6-6a1 1 0 000-1.414zM12.5 10a1.5 1.5 0 100 3 1.5 1.5 0 000-3z" clip-rule="evenodd" />
              </svg>
              {{ __('Top Rated', 'sega-woo-theme') }}
            </span>
          </div>
          <a href="{{ $bestsellersUrl }}" class="view-all-link">
            {{ __('View All', 'sega-woo-theme') }}
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

@endsection
