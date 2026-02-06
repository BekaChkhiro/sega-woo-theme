{{--
  Hero Slider Component

  Usage:
  <x-hero-slider />
  <x-hero-slider :slides="$slides" :autoplay="true" :delay="5000" />

  Props:
  - slides: array of slide objects [{image, link}]
  - autoplay: boolean (default: true)
  - delay: int in ms (default: 5000)
  - showNavigation: boolean (default: true)
  - showPagination: boolean (default: true)
--}}

@props([
    'slides' => [],
    'autoplay' => true,
    'delay' => 5000,
    'showNavigation' => true,
    'showPagination' => true,
])

<div
  class="hero-slider relative h-full w-full overflow-hidden rounded-xl shadow-lg"
  x-data="heroSlider({
    autoplay: {{ $autoplay ? 'true' : 'false' }},
    delay: {{ $delay }},
    showNavigation: {{ $showNavigation ? 'true' : 'false' }},
    showPagination: {{ $showPagination ? 'true' : 'false' }}
  })"
  x-init="init()"
  x-on:destroy.window="destroy()"
>
  {{-- Swiper Container --}}
  <div x-ref="swiper" class="swiper h-full w-full">
    <div class="swiper-wrapper">
      @forelse ($slides as $index => $slide)
        <div class="swiper-slide">
          @if (!empty($slide['link']))
            <a href="{{ $slide['link'] }}" class="block h-full w-full">
              <img
                src="{{ $slide['image'] }}"
                alt="{{ __('Slide', 'sage') }} {{ $index + 1 }}"
                class="h-full w-full object-cover"
                loading="{{ $index === 0 ? 'eager' : 'lazy' }}"
              >
            </a>
          @else
            <img
              src="{{ $slide['image'] }}"
              alt="{{ __('Slide', 'sage') }} {{ $index + 1 }}"
              class="h-full w-full object-cover"
              loading="{{ $index === 0 ? 'eager' : 'lazy' }}"
            >
          @endif
        </div>
      @empty
        {{-- Empty state --}}
        <div class="swiper-slide">
          <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-primary-500 to-primary-700 text-white">
            <div class="text-center">
              <svg class="mx-auto mb-3 h-12 w-12 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <rect x="3" y="3" width="18" height="18" rx="2"/>
                <circle cx="8.5" cy="8.5" r="1.5"/>
                <path d="M21 15l-5-5L5 21"/>
              </svg>
              <p class="text-sm opacity-75">{{ __('Add slides in Customizer', 'sage') }}</p>
            </div>
          </div>
        </div>
      @endforelse
    </div>
  </div>

  {{-- Navigation Arrows --}}
  <button
    x-ref="prev"
    type="button"
    class="hero-slider-nav hero-slider-prev absolute left-4 top-1/2 z-10 flex h-10 w-10 -translate-y-1/2 items-center justify-center rounded-full bg-white shadow-md transition-all hover:scale-105 hover:shadow-lg lg:left-5 lg:h-11 lg:w-11 {{ $showNavigation ? '' : 'hidden' }}"
    aria-label="{{ __('Previous slide', 'sage') }}"
  >
    <svg class="h-5 w-5 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
      <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
    </svg>
  </button>
  <button
    x-ref="next"
    type="button"
    class="hero-slider-nav hero-slider-next absolute right-4 top-1/2 z-10 flex h-10 w-10 -translate-y-1/2 items-center justify-center rounded-full bg-white shadow-md transition-all hover:scale-105 hover:shadow-lg lg:right-5 lg:h-11 lg:w-11 {{ $showNavigation ? '' : 'hidden' }}"
    aria-label="{{ __('Next slide', 'sage') }}"
  >
    <svg class="h-5 w-5 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
      <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
    </svg>
  </button>

  {{-- Pagination Dots --}}
  <div
    x-ref="pagination"
    class="hero-slider-pagination {{ $showPagination ? '' : 'hidden' }}"
  ></div>

  {{-- Progress indicator --}}
  @if (count($slides) > 1)
    <div class="absolute bottom-0 left-0 right-0 h-1 bg-white/10">
      <div
        class="h-full bg-white/50 transition-all duration-300"
        :style="{ width: ((currentSlide + 1) / totalSlides * 100) + '%' }"
      ></div>
    </div>
  @endif
</div>

{{-- Swiper Styles (minimal, leveraging Tailwind) --}}
@once
  <style>
    /* Swiper base styles */
    .hero-slider .swiper {
      overflow: hidden;
    }
    .hero-slider .swiper-wrapper {
      display: flex;
      transition-property: transform;
    }
    .hero-slider .swiper-slide {
      flex-shrink: 0;
      width: 100%;
      height: 100%;
      position: relative;
    }

    /* Pagination container */
    .hero-slider-pagination {
      position: absolute;
      bottom: 1rem;
      left: 50%;
      transform: translateX(-50%);
      z-index: 10;
      display: flex;
      gap: 0.5rem;
      align-items: center;
    }
    .hero-slider-pagination.hidden {
      display: none;
    }

    /* Pagination bullets */
    .hero-slider-bullet {
      width: 0.625rem;
      height: 0.625rem;
      border-radius: 9999px;
      background-color: rgba(255, 255, 255, 0.5);
      border: none;
      padding: 0;
      cursor: pointer;
      transition: all 0.3s ease;
    }
    .hero-slider-bullet:hover {
      background-color: rgba(255, 255, 255, 0.75);
    }
    .hero-slider-bullet-active {
      width: 1.5rem;
      background-color: white;
    }

    /* Fade effect */
    .hero-slider .swiper-fade .swiper-slide {
      opacity: 0 !important;
      transition-property: opacity;
    }
    .hero-slider .swiper-fade .swiper-slide-active {
      opacity: 1 !important;
    }
  </style>
@endonce
