{{--
  Hero Slider Component

  Usage:
  <x-hero-slider />
  <x-hero-slider :slides="$customSlides" :autoplay="true" :delay="5000" />

  Props:
  - slides: array of slide objects (optional, uses defaults if empty)
  - autoplay: boolean (default: true)
  - delay: int in ms (default: 5000)
  - showNavigation: boolean (default: true)
  - showPagination: boolean (default: true)

  Each slide object can have:
  - badge: string (optional badge text)
  - title: string (main heading)
  - description: string (supporting text)
  - button_text: string (CTA button label)
  - button_url: string (CTA button link)
  - gradient_from: string (Tailwind gradient class, e.g., 'from-primary-600')
  - gradient_to: string (Tailwind gradient class, e.g., 'to-primary-800')
  - image: string (optional background image URL)
--}}

@if (!empty($slides))
  <div
    x-data="heroSlider()"
    x-init="init()"
    x-on:destroy.window="destroy()"
    class="relative h-full w-full overflow-hidden rounded-xl shadow-lg"
  >
    {{-- Swiper Container --}}
    <div x-ref="swiper" class="swiper h-full w-full">
      <div class="swiper-wrapper">
        @foreach ($slides as $index => $slide)
          <div class="swiper-slide">
            {{-- Full Image Background --}}
            @if (!empty($slide['image']))
              <img
                src="{{ $slide['image'] }}"
                alt="{{ $slide['title'] ?? 'Slide ' . ($index + 1) }}"
                class="absolute inset-0 h-full w-full object-cover"
                loading="{{ $index === 0 ? 'eager' : 'lazy' }}"
              >
            @else
              {{-- Fallback gradient if no image --}}
              <div class="absolute inset-0 bg-gradient-to-r {{ $slide['gradient_from'] ?? 'from-primary-600' }} {{ $slide['gradient_to'] ?? 'to-primary-800' }}"></div>
            @endif
          </div>
        @endforeach
      </div>
    </div>

    {{-- Navigation Arrows --}}
    @if ($showNavigation)
      <button
        x-ref="prev"
        type="button"
        class="absolute left-3 top-1/2 z-10 flex h-10 w-10 -translate-y-1/2 items-center justify-center rounded-full bg-white/20 text-white backdrop-blur-sm transition-all hover:bg-white/30 lg:left-4 lg:h-12 lg:w-12"
        aria-label="{{ __('Previous slide', 'sage') }}"
      >
        <svg class="h-5 w-5 lg:h-6 lg:w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
        </svg>
      </button>
      <button
        x-ref="next"
        type="button"
        class="absolute right-3 top-1/2 z-10 flex h-10 w-10 -translate-y-1/2 items-center justify-center rounded-full bg-white/20 text-white backdrop-blur-sm transition-all hover:bg-white/30 lg:right-4 lg:h-12 lg:w-12"
        aria-label="{{ __('Next slide', 'sage') }}"
      >
        <svg class="h-5 w-5 lg:h-6 lg:w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
        </svg>
      </button>
    @endif

    {{-- Pagination Dots --}}
    @if ($showPagination)
      <div
        x-ref="pagination"
        class="absolute bottom-4 left-1/2 z-10 flex -translate-x-1/2 gap-2"
      ></div>
    @endif

    {{-- Progress indicator --}}
    <div class="absolute bottom-0 left-0 right-0 h-1 bg-white/10">
      <div
        class="h-full bg-white/50 transition-all duration-300"
        :style="{ width: ((currentSlide + 1) / totalSlides * 100) + '%' }"
      ></div>
    </div>
  </div>

  {{-- Swiper Styles (minimal, leveraging Tailwind) --}}
  @once
    <style>
      /* Swiper base styles */
      .swiper {
        overflow: hidden;
      }
      .swiper-wrapper {
        display: flex;
        transition-property: transform;
      }
      .swiper-slide {
        flex-shrink: 0;
        width: 100%;
        height: 100%;
        position: relative;
      }

      /* Pagination bullets */
      .hero-slider-bullet {
        width: 0.5rem;
        height: 0.5rem;
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
      .swiper-fade .swiper-slide {
        opacity: 0 !important;
        transition-property: opacity;
      }
      .swiper-fade .swiper-slide-active {
        opacity: 1 !important;
      }
    </style>
  @endonce
@endif
