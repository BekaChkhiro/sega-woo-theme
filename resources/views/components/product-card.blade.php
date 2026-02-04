<article {{ $attributes->merge(['class' => 'group relative flex h-full flex-col overflow-hidden rounded-lg sm:rounded-xl border border-secondary-200 bg-white transition-shadow hover:shadow-lg']) }}>
  {{-- Product Badges --}}
  <div class="absolute left-2 top-2 sm:left-3 sm:top-3 z-10 flex flex-col gap-1 sm:gap-2">
    {{-- Sale Badge with Percentage --}}
    @if ($isOnSale() && $salePercentage() > 0)
      <span class="inline-flex items-center gap-0.5 sm:gap-1 rounded-full bg-red-500 px-1.5 py-0.5 sm:px-2.5 sm:py-1 text-[10px] sm:text-xs font-bold text-white shadow-sm">
        <svg class="h-2.5 w-2.5 sm:h-3 sm:w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
        </svg>
        {{ $salePercentage() }}%
      </span>
    @elseif ($isOnSale())
      <span class="rounded-full bg-red-500 px-1.5 py-0.5 sm:px-2.5 sm:py-1 text-[10px] sm:text-xs font-bold text-white shadow-sm">
        {{ __('Sale', 'sage') }}
      </span>
    @endif

    {{-- Out of Stock Badge --}}
    @if (!$isInStock())
      <span class="rounded-full bg-secondary-800 px-1.5 py-0.5 sm:px-2.5 sm:py-1 text-[10px] sm:text-xs font-semibold text-white shadow-sm">
        {{ __('Sold out', 'sage') }}
      </span>
    @endif
  </div>

  {{-- Featured Badge (top-right) --}}
  @if ($isFeatured())
    <span class="absolute right-2 top-2 sm:right-3 sm:top-3 z-10 inline-flex items-center gap-0.5 sm:gap-1 rounded-full bg-amber-400 px-1.5 py-0.5 sm:px-2.5 sm:py-1 text-[10px] sm:text-xs font-bold text-amber-900 shadow-sm">
      <svg class="h-2.5 w-2.5 sm:h-3 sm:w-3" fill="currentColor" viewBox="0 0 20 20">
        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
      </svg>
      <span class="hidden xs:inline">{{ __('Featured', 'sage') }}</span>
      <span class="xs:hidden">★</span>
    </span>
  @endif

  <a href="{{ $permalink() }}" class="relative aspect-square overflow-hidden bg-secondary-100">
    @if ($hasThumbnail())
      {!! $thumbnail() !!}
    @else
      <div class="flex h-full w-full items-center justify-center">
        <svg class="h-10 w-10 sm:h-16 sm:w-16 text-secondary-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
          <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
        </svg>
      </div>
    @endif
  </a>

  <div class="flex flex-1 flex-col p-2.5 sm:p-4">
    @if ($category = $category())
      <p class="mb-0.5 sm:mb-1 text-[10px] sm:text-xs font-medium uppercase tracking-wide text-secondary-500 truncate">
        {{ $category }}
      </p>
    @endif

    <h2 class="mb-1.5 sm:mb-2 flex-1 text-sm sm:text-base font-medium text-secondary-900 line-clamp-2">
      <a href="{{ $permalink() }}" class="transition-colors hover:text-primary-600">
        {{ $title() }}
      </a>
    </h2>

    @if ($ratingCount())
      <x-star-rating
        :product="$product"
        size="sm"
        :show-count="true"
        :show-link="false"
        class="mb-1.5 sm:mb-2"
      />
    @endif

    <div class="mt-auto flex items-center justify-between gap-2 sm:gap-3">
      <div class="min-w-0 flex-1">
        <x-price :product="$product" size="md" />
      </div>

      @if ($isInStock())
        @if ($isSimple())
          <button
            type="button"
            class="ajax_add_to_cart add_to_cart_button flex-shrink-0 flex h-8 w-8 sm:h-10 sm:w-10 items-center justify-center rounded-full bg-primary-600 text-white transition-colors hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
            aria-label="{{ sprintf(__('Add %s to cart', 'sage'), $title()) }}"
            data-product_id="{{ $product->get_id() }}"
            data-product_sku="{{ $product->get_sku() }}"
            data-quantity="1"
          >
            <svg class="h-4 w-4 sm:h-5 sm:w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
            </svg>
          </button>
        @else
          <a
            href="{{ $permalink() }}"
            class="flex-shrink-0 flex h-8 w-8 sm:h-10 sm:w-10 items-center justify-center rounded-full bg-primary-600 text-white transition-colors hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
            aria-label="{{ sprintf(__('View %s options', 'sage'), $title()) }}"
          >
            <svg class="h-4 w-4 sm:h-5 sm:w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
              <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>
          </a>
        @endif
      @else
        <span class="flex-shrink-0 text-[10px] sm:text-xs font-medium text-red-600">
          {{ __('Out of stock', 'sage') }}
        </span>
      @endif
    </div>
  </div>
</article>
