{{--
  Product Gallery Partial

  Displays the main product image with thumbnail navigation,
  zoom functionality, and lightbox support.

  @param array $images - Array of image data from Product Composer's allImages()
  @param bool $onSale - Whether product is on sale
  @param int $discount - Sale discount percentage
  @param bool $inStock - Whether product is in stock
--}}

@php
  // Default values for variables passed via @include
  $images = $images ?? [];
  $onSale = $onSale ?? false;
  $discount = $discount ?? 0;
  $inStock = $inStock ?? true;

  $hasMultipleImages = count($images) > 1;
  $mainImage = $images[0] ?? null;
@endphp

<div class="product-gallery" x-data="productGallery" data-images="{{ json_encode($images) }}" x-cloak>
  <div class="relative">
    {{-- Badges --}}
    <div class="absolute left-3 top-3 z-10 flex flex-col gap-2">
      @if ($onSale && $discount > 0)
        <span class="inline-flex items-center gap-1 rounded-full bg-red-500 px-3 py-1.5 text-sm font-bold text-white shadow-sm">
          <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
          </svg>
          -{{ $discount }}%
        </span>
      @elseif ($onSale)
        <span class="rounded-full bg-red-500 px-3 py-1.5 text-sm font-bold text-white shadow-sm">
          {{ __('Sale', 'sage') }}
        </span>
      @endif

      @if (!$inStock)
        <span class="rounded-full bg-secondary-800 px-3 py-1.5 text-sm font-semibold text-white shadow-sm">
          {{ __('Out of stock', 'sage') }}
        </span>
      @endif
    </div>

    {{-- Navigation Arrows (for multiple images) --}}
    <button
      x-show="images.length > 1"
      type="button"
      class="absolute left-2 top-1/2 z-10 -translate-y-1/2 rounded-full bg-white/90 p-2 text-secondary-700 shadow-md backdrop-blur-sm transition-all hover:bg-white hover:text-secondary-900 focus:outline-none focus:ring-2 focus:ring-primary-500"
      @click="previousImage()"
      aria-label="{{ __('Previous image', 'sage') }}"
    >
      <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
      </svg>
    </button>
    <button
      x-show="images.length > 1"
      type="button"
      class="absolute right-2 top-1/2 z-10 -translate-y-1/2 rounded-full bg-white/90 p-2 text-secondary-700 shadow-md backdrop-blur-sm transition-all hover:bg-white hover:text-secondary-900 focus:outline-none focus:ring-2 focus:ring-primary-500"
      @click="nextImage()"
      aria-label="{{ __('Next image', 'sage') }}"
    >
      <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
      </svg>
    </button>

    {{-- Zoom Button --}}
    @if ($mainImage)
      <button
        type="button"
        class="absolute right-3 top-3 z-10 rounded-full bg-white/90 p-2 text-secondary-700 shadow-md backdrop-blur-sm transition-all hover:bg-white hover:text-secondary-900 focus:outline-none focus:ring-2 focus:ring-primary-500"
        @click="openLightbox()"
        aria-label="{{ __('Zoom image', 'sage') }}"
      >
        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" />
        </svg>
      </button>
    @endif

    {{-- Main Image Container --}}
    <div
      id="main-product-image"
      class="aspect-square overflow-hidden rounded-2xl border border-secondary-200 bg-secondary-50 shadow-sm cursor-zoom-in transition-shadow duration-300 hover:shadow-lg"
      @click="openLightbox()"
    >
      @if (!empty($images))
        <template x-for="(image, index) in images" :key="index">
          <div
            x-show="currentIndex === index"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="h-full w-full"
          >
            <img
              :src="image.url"
              :alt="image.alt"
              :srcset="image.srcset"
              :sizes="image.sizes"
              class="h-full w-full object-contain"
              itemprop="image"
            />
          </div>
        </template>
      @else
        <div class="flex h-full w-full items-center justify-center">
          <svg class="h-24 w-24 text-secondary-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
          </svg>
        </div>
      @endif
    </div>

    {{-- Image Counter --}}
    <div x-show="images.length > 1" class="absolute bottom-3 left-1/2 z-10 -translate-x-1/2 rounded-full bg-black/60 px-3 py-1 text-xs font-medium text-white backdrop-blur-sm">
      <span x-text="currentIndex + 1"></span> / <span x-text="images.length"></span>
    </div>
  </div>

  {{-- Gallery Thumbnails Carousel --}}
  <div x-show="images.length > 1" class="relative mt-4" x-ref="thumbnailCarouselWrapper">
    {{-- Thumbnail Navigation - Previous --}}
    <button
      type="button"
      x-ref="thumbPrev"
      class="absolute -left-3 top-1/2 z-10 -translate-y-1/2 rounded-full bg-white p-1.5 text-secondary-600 shadow-md transition-all hover:bg-secondary-50 hover:text-secondary-900 focus:outline-none focus:ring-2 focus:ring-primary-500 disabled:opacity-40 disabled:cursor-not-allowed"
      :class="{ 'invisible': images.length <= 4 }"
      aria-label="{{ __('Previous thumbnails', 'sage') }}"
    >
      <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
      </svg>
    </button>

    {{-- Swiper Container --}}
    <div class="mx-5 overflow-hidden" x-ref="thumbnailSwiper">
      <div class="swiper-wrapper">
        <template x-for="(image, index) in images" :key="'thumb-' + index">
          <div class="swiper-slide">
            <button
              type="button"
              class="gallery-thumbnail group aspect-square w-full overflow-hidden rounded-xl border-2 bg-secondary-50 shadow-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
              :class="currentIndex === index ? 'border-primary-500 ring-2 ring-primary-500/20 shadow-md' : 'border-transparent hover:border-secondary-300 hover:shadow-md'"
              @click="setImage(index); slideThumbnailTo(index)"
              :aria-label="'{{ __('View image', 'sage') }} ' + (index + 1)"
            >
              <img
                :src="image.thumb_url"
                :alt="image.alt"
                class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105"
                loading="lazy"
                decoding="async"
              />
            </button>
          </div>
        </template>
      </div>
    </div>

    {{-- Thumbnail Navigation - Next --}}
    <button
      type="button"
      x-ref="thumbNext"
      class="absolute -right-3 top-1/2 z-10 -translate-y-1/2 rounded-full bg-white p-1.5 text-secondary-600 shadow-md transition-all hover:bg-secondary-50 hover:text-secondary-900 focus:outline-none focus:ring-2 focus:ring-primary-500 disabled:opacity-40 disabled:cursor-not-allowed"
      :class="{ 'invisible': images.length <= 4 }"
      aria-label="{{ __('Next thumbnails', 'sage') }}"
    >
      <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
      </svg>
    </button>
  </div>

  {{-- Lightbox Modal --}}
  <div
    x-show="lightboxOpen"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/90 p-4"
    @click.self="closeLightbox()"
    @keydown.escape.window="closeLightbox()"
    @keydown.arrow-left.window="previousImage()"
    @keydown.arrow-right.window="nextImage()"
    role="dialog"
    aria-modal="true"
    aria-label="{{ __('Product image gallery', 'sage') }}"
  >
    {{-- Close Button --}}
    <button
      type="button"
      class="absolute right-4 top-4 rounded-full bg-white/10 p-2 text-white transition-colors hover:bg-white/20 focus:outline-none focus:ring-2 focus:ring-white"
      @click="closeLightbox()"
      aria-label="{{ __('Close gallery', 'sage') }}"
    >
      <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
      </svg>
    </button>

    {{-- Navigation Arrows --}}
    <button
      x-show="images.length > 1"
      type="button"
      class="absolute left-4 top-1/2 -translate-y-1/2 rounded-full bg-white/10 p-3 text-white transition-colors hover:bg-white/20 focus:outline-none focus:ring-2 focus:ring-white"
      @click="previousImage()"
      aria-label="{{ __('Previous image', 'sage') }}"
    >
      <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
      </svg>
    </button>
    <button
      x-show="images.length > 1"
      type="button"
      class="absolute right-4 top-1/2 -translate-y-1/2 rounded-full bg-white/10 p-3 text-white transition-colors hover:bg-white/20 focus:outline-none focus:ring-2 focus:ring-white"
      @click="nextImage()"
      aria-label="{{ __('Next image', 'sage') }}"
    >
      <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
      </svg>
    </button>

    {{-- Lightbox Image --}}
    <div class="max-h-[90vh] max-w-[90vw]">
      <template x-for="(image, index) in images" :key="'lightbox-' + index">
        <img
          x-show="currentIndex === index"
          x-transition:enter="transition ease-out duration-200"
          x-transition:enter-start="opacity-0 scale-95"
          x-transition:enter-end="opacity-100 scale-100"
          :src="image.full_url"
          :alt="image.alt"
          class="max-h-[90vh] max-w-[90vw] object-contain"
        />
      </template>
    </div>

    {{-- Lightbox Thumbnails --}}
    <div x-show="images.length > 1" class="absolute bottom-4 left-1/2 flex -translate-x-1/2 gap-2">
      <template x-for="(image, index) in images" :key="'lightbox-thumb-' + index">
        <button
          type="button"
          class="h-16 w-16 overflow-hidden rounded-lg border-2 transition-all focus:outline-none"
          :class="currentIndex === index ? 'border-white' : 'border-transparent opacity-60 hover:opacity-100'"
          @click="setImage(index)"
        >
          <img
            :src="image.thumb_url"
            :alt="image.alt"
            class="h-full w-full object-cover"
            loading="lazy"
            decoding="async"
          />
        </button>
      </template>
    </div>

    {{-- Image Counter --}}
    <div x-show="images.length > 1" class="absolute left-4 top-4 rounded-full bg-white/10 px-4 py-2 text-sm font-medium text-white">
      <span x-text="currentIndex + 1"></span> / <span x-text="images.length"></span>
    </div>
  </div>
</div>

{{-- WooCommerce Variation Event Listeners --}}
<script>
  // Listen for WooCommerce variation change events
  document.addEventListener('DOMContentLoaded', function() {
    const variationsForm = document.querySelector('.variations-form');
    const galleryEl = document.querySelector('.product-gallery');

    if (variationsForm && galleryEl && typeof jQuery !== 'undefined') {
      // Listen for variation found event
      jQuery(variationsForm).on('found_variation', function(event, variation) {
        if (variation.image && variation.image.src && galleryEl._x_dataStack) {
          const galleryComponent = galleryEl._x_dataStack[0];
          if (galleryComponent && galleryComponent.updateGalleryImage) {
            galleryComponent.updateGalleryImage(
              variation.image.src,
              variation.image.full_src,
              variation.image.thumb_src || variation.image.src,
              variation.image.alt || '',
              variation.image.srcset || '',
              variation.image.sizes || ''
            );
          }
        }
      });

      // Listen for variation reset
      jQuery(variationsForm).on('reset_image', function() {
        if (galleryEl._x_dataStack) {
          const galleryComponent = galleryEl._x_dataStack[0];
          if (galleryComponent && galleryComponent.resetGallery) {
            galleryComponent.resetGallery();
          }
        }
      });
    }
  });
</script>
