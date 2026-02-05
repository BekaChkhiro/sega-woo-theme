{{--
  Product Card - Standalone Version
  Required: $product (WC_Product object)
--}}

@php
  // Product data
  $productId = $product->get_id();
  $productName = $product->get_name();
  $productUrl = $product->get_permalink();
  $productImage = $product->get_image('woocommerce_thumbnail', ['loading' => 'lazy', 'decoding' => 'async']);

  // Sale info
  $isOnSale = $product->is_on_sale();
  $salePercentage = 0;
  if ($isOnSale) {
    $regularPrice = (float) $product->get_regular_price();
    $salePrice = (float) $product->get_sale_price();
    if ($regularPrice > 0 && $salePrice > 0) {
      $salePercentage = (int) round((($regularPrice - $salePrice) / $regularPrice) * 100);
    }
  }

  // Stock info
  $isInStock = $product->is_in_stock();

  // Price HTML
  $priceHtml = $product->get_price_html();
@endphp

<article class="group relative flex h-full flex-col overflow-hidden rounded-lg sm:rounded-xl border border-secondary-200 bg-white transition-shadow hover:shadow-lg">
  {{-- Product Badges --}}
  <div class="absolute left-2 top-2 sm:left-3 sm:top-3 z-10 flex flex-col gap-1 sm:gap-2">
    {{-- Sale Badge --}}
    @if ($isOnSale && $salePercentage > 0)
      <span class="inline-flex items-center gap-0.5 sm:gap-1 rounded-full bg-red-500 px-1.5 py-0.5 sm:px-2.5 sm:py-1 text-[10px] sm:text-xs font-bold text-white shadow-sm">
        <svg class="h-2.5 w-2.5 sm:h-3 sm:w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
        </svg>
        {{ $salePercentage }}%
      </span>
    @elseif ($isOnSale)
      <span class="rounded-full bg-red-500 px-1.5 py-0.5 sm:px-2.5 sm:py-1 text-[10px] sm:text-xs font-bold text-white shadow-sm">
        {{ __('Sale', 'sage') }}
      </span>
    @endif

    {{-- Out of Stock Badge --}}
    @if (!$isInStock)
      <span class="rounded-full bg-secondary-800 px-1.5 py-0.5 sm:px-2.5 sm:py-1 text-[10px] sm:text-xs font-semibold text-white shadow-sm">
        {{ __('Out of Stock', 'sage') }}
      </span>
    @endif
  </div>

  {{-- Product Image --}}
  <div class="relative aspect-square w-full overflow-hidden bg-secondary-50">
    <a href="{{ $productUrl }}" class="block h-full w-full">
      {!! $productImage !!}
    </a>
  </div>

  {{-- Product Details --}}
  <div class="flex flex-1 flex-col gap-1 sm:gap-2 p-2 sm:p-4">
    {{-- Product Title --}}
    <h3 class="text-xs sm:text-sm font-medium text-secondary-900 transition-colors group-hover:text-primary-600">
      <a href="{{ $productUrl }}" class="line-clamp-2">
        {{ $productName }}
      </a>
    </h3>

    {{-- Price --}}
    <div class="mt-auto text-base sm:text-lg font-bold text-secondary-900">
      {!! $priceHtml !!}
    </div>

    {{-- Add to Cart Button --}}
    <div class="mt-2 sm:mt-3">
      @if ($isInStock)
        {!! apply_filters('woocommerce_loop_add_to_cart_link',
          sprintf('<a href="%s" data-quantity="1" class="%s" %s>%s</a>',
            esc_url($product->add_to_cart_url()),
            esc_attr('button product_type_' . $product->get_type() . ' add_to_cart_button ajax_add_to_cart inline-flex w-full items-center justify-center rounded-md bg-primary-600 px-3 py-1.5 sm:px-4 sm:py-2 text-xs sm:text-sm font-medium text-white transition-colors hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 disabled:opacity-50'),
            sprintf('data-product_id="%s" data-product_sku="%s" aria-label="%s" rel="nofollow"',
              esc_attr($productId),
              esc_attr($product->get_sku()),
              esc_attr($product->add_to_cart_description())
            ),
            esc_html($product->add_to_cart_text())
          ),
          $product) !!}
      @else
        <button disabled class="inline-flex w-full items-center justify-center rounded-md bg-secondary-300 px-3 py-1.5 sm:px-4 sm:py-2 text-xs sm:text-sm font-medium text-secondary-700 cursor-not-allowed">
          {{ __('Out of Stock', 'sage') }}
        </button>
      @endif
    </div>
  </div>
</article>
