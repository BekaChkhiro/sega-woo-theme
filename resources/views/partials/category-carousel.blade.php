{{--
  Category Carousel - Homepage Categories Carousel

  Required variables:
  - $categories (array): Categories to display

  Optional variables:
  - $id (string): Unique carousel ID (default: 'category-carousel')
  - $slidesPerView (int): Slides per view on desktop (default: 6)
  - $spaceBetween (int): Space between slides (default: 24)
  - $loop (bool): Enable loop (default: true)
  - $navigation (bool): Show navigation arrows (default: true)
--}}

@php
  // Set defaults
  $id = $id ?? 'category-carousel';
  $slidesPerView = $slidesPerView ?? 6;
  $spaceBetween = $spaceBetween ?? 24;
  $loop = $loop ?? true;
  $navigation = $navigation ?? true;

  // Ensure we have an array
  $categoriesArray = is_array($categories) ? $categories : [];

  // Check if we have categories
  $hasCategories = !empty($categoriesArray);
  $categoryCount = count($categoriesArray);

  // Should loop only if we have more categories than slides per view
  $shouldLoop = $loop && $categoryCount > $slidesPerView;

  // Build Swiper config - optimized for 6 visible on desktop
  $swiperConfig = [
    'slidesPerView' => 2,
    'spaceBetween' => 12,
    'loop' => $shouldLoop,
    'breakpoints' => [
      480 => ['slidesPerView' => 3, 'spaceBetween' => 12],
      640 => ['slidesPerView' => 4, 'spaceBetween' => 16],
      768 => ['slidesPerView' => 5, 'spaceBetween' => 20],
      1024 => ['slidesPerView' => $slidesPerView, 'spaceBetween' => $spaceBetween],
      1280 => ['slidesPerView' => $slidesPerView, 'spaceBetween' => $spaceBetween],
    ],
  ];

  $swiperConfigJson = json_encode($swiperConfig);
@endphp

@if ($hasCategories)
  <div
    x-data="productCarousel({{ $swiperConfigJson }})"
    x-init="init()"
    class="category-carousel relative"
    id="{{ $id }}"
  >
    {{-- Swiper Container --}}
    <div x-ref="swiper" class="swiper category-carousel-swiper">
      <div class="swiper-wrapper">
        @foreach ($categoriesArray as $category)
          <div class="swiper-slide h-auto">
            <a
              href="{{ $category['url'] }}"
              class="category-card group flex flex-col items-center rounded-xl border border-secondary-200 bg-white p-4 text-center transition-all hover:border-primary-200 hover:shadow-md lg:p-6"
            >
              <div class="category-icon mb-3 flex h-16 w-16 items-center justify-center overflow-hidden rounded-full bg-secondary-100 transition-colors group-hover:bg-primary-50 lg:h-20 lg:w-20">
                @if (!empty($category['thumbnail']))
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
          </div>
        @endforeach
      </div>
    </div>

    {{-- Navigation Arrows --}}
    @if ($navigation && $categoryCount > $slidesPerView)
      {{-- Previous Button --}}
      <button
        x-ref="prev"
        type="button"
        class="absolute left-0 top-1/2 z-10 -ml-4 hidden -translate-y-1/2 lg:flex h-10 w-10 items-center justify-center rounded-full bg-white text-secondary-700 shadow-lg transition-all hover:bg-primary-600 hover:text-white hover:shadow-xl disabled:cursor-not-allowed disabled:opacity-40 disabled:hover:bg-white disabled:hover:text-secondary-700"
        aria-label="{{ __('Previous categories', 'sage') }}"
      >
        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
        </svg>
      </button>

      {{-- Next Button --}}
      <button
        x-ref="next"
        type="button"
        class="absolute right-0 top-1/2 z-10 -mr-4 hidden -translate-y-1/2 lg:flex h-10 w-10 items-center justify-center rounded-full bg-white text-secondary-700 shadow-lg transition-all hover:bg-primary-600 hover:text-white hover:shadow-xl disabled:cursor-not-allowed disabled:opacity-40 disabled:hover:bg-white disabled:hover:text-secondary-700"
        aria-label="{{ __('Next categories', 'sage') }}"
      >
        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
        </svg>
      </button>
    @endif
  </div>

  {{-- Mobile Swipe Hint --}}
  @if ($categoryCount > 2)
    <div class="mt-3 text-center text-xs text-secondary-500 lg:hidden">
      {{ __('Swipe to see more categories', 'sage') }}
    </div>
  @endif
@endif
