@props([
  'showHeader' => true,
  'headerClass' => '',
  'containerClass' => '',
])

@if ($hasProducts())
  <section {{ $attributes->merge(['class' => 'product-carousel-section']) }} id="{{ $id }}">
    {{-- Section Header --}}
    @if ($showHeader && $title)
      <div class="mb-6 sm:mb-8 flex items-center justify-between {{ $headerClass }}">
        <h2 class="text-xl sm:text-2xl md:text-3xl font-bold text-secondary-900">
          {{ $title }}
        </h2>

        @if ($viewAllUrl)
          <a
            href="{{ $viewAllUrl }}"
            class="group inline-flex items-center gap-1.5 text-sm font-semibold text-primary-600 transition-colors hover:text-primary-700"
          >
            <span>{{ $viewAllText }}</span>
            <svg class="h-4 w-4 transition-transform group-hover:translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" />
            </svg>
          </a>
        @endif
      </div>
    @endif

    {{-- Carousel Container --}}
    <div
      x-data="productCarousel({{ $swiperConfig() }})"
      x-init="init()"
      class="relative {{ $containerClass }}"
    >
      {{-- Swiper Container --}}
      <div x-ref="swiper" class="swiper product-carousel-swiper">
        <div class="swiper-wrapper">
          @foreach ($products as $product)
            <div class="swiper-slide h-auto">
              <x-product-card :product="$product" class="h-full" />
            </div>
          @endforeach
        </div>
      </div>

      {{-- Navigation Arrows --}}
      @if ($navigation && $productCount() > $slidesPerView)
        {{-- Previous Button --}}
        <button
          x-ref="prev"
          type="button"
          class="absolute left-0 top-1/2 z-10 -ml-4 sm:-ml-5 md:-ml-6 hidden -translate-y-1/2 lg:flex h-10 w-10 sm:h-12 sm:w-12 items-center justify-center rounded-full bg-white text-secondary-700 shadow-lg transition-all hover:bg-primary-600 hover:text-white hover:shadow-xl disabled:cursor-not-allowed disabled:opacity-40 disabled:hover:bg-white disabled:hover:text-secondary-700"
          aria-label="{{ __('Previous products', 'sega-woo-theme') }}"
        >
          <svg class="h-5 w-5 sm:h-6 sm:w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
          </svg>
        </button>

        {{-- Next Button --}}
        <button
          x-ref="next"
          type="button"
          class="absolute right-0 top-1/2 z-10 -mr-4 sm:-mr-5 md:-mr-6 hidden -translate-y-1/2 lg:flex h-10 w-10 sm:h-12 sm:w-12 items-center justify-center rounded-full bg-white text-secondary-700 shadow-lg transition-all hover:bg-primary-600 hover:text-white hover:shadow-xl disabled:cursor-not-allowed disabled:opacity-40 disabled:hover:bg-white disabled:hover:text-secondary-700"
          aria-label="{{ __('Next products', 'sega-woo-theme') }}"
        >
          <svg class="h-5 w-5 sm:h-6 sm:w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
          </svg>
        </button>
      @endif

      {{-- Pagination Dots --}}
      @if ($pagination)
        <div x-ref="pagination" class="product-carousel-pagination mt-6 sm:mt-8 flex items-center justify-center gap-2"></div>
      @endif
    </div>

    {{-- Mobile Navigation Hint (for touch devices) --}}
    @if ($productCount() > 1)
      <div class="mt-4 text-center text-xs text-secondary-500 lg:hidden">
        {{ __('Swipe to see more products', 'sega-woo-theme') }}
      </div>
    @endif
  </section>
@endif
