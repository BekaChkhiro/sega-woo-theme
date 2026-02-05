@extends('layouts.app')

@section('breadcrumbs')
  <x-breadcrumbs :items="$breadcrumbs()" />
@endsection

@section('content')
  @php
    // Extract all View Composer values upfront to avoid InvokableComponentVariable issues
    $id = $productId();
    $name = $productName();
    $type = $productType();
    $isSimpleProduct = $isSimple();
    $isVariableProduct = $isVariable();
    $isExternalProduct = $isExternal();
    $onSale = $isOnSale();
    $discount = $salePercentage();
    $inStock = $isInStock();
    $purchasable = $isPurchasable();
    $stockQty = $stockQuantity();
    $managingStock = $managesStock();
    $allowBackorders = $backordersAllowed();
    $reviewsAllowed = $reviewsEnabled();
    $numRatings = $ratingCount();
    $numReviews = $reviewCount();
    $regPrice = $regularPrice();
    $discountPrice = $salePrice();
    $price = $currentPrice();
    $formattedPrice = $priceHtml();
    $shortDesc = $shortDescription();
    $fullDesc = $description();
    $productSku = $sku();
    $cartUrl = $addToCartUrl();
    $cartText = $addToCartText();
    $hasDesc = $hasDescription();
    $hasAddInfo = $hasAdditionalInfo();
    $productWeight = $weight();
    $productDimensions = $dimensions();
    $hasProductWeight = $hasWeight();
    $hasProductDimensions = $hasDimensions();
    $images = $allImages();
  @endphp

  <div id="product-{{ $id }}" class="woocommerce-product" itemscope itemtype="http://schema.org/Product">
    {{-- Product Main Section --}}
    <div class="grid grid-cols-1 gap-8 lg:grid-cols-2 lg:gap-16 xl:gap-20">
      {{-- Product Gallery --}}
      @php $images = $allImages() @endphp
      @include('woocommerce.single-product.product-image', [
        'images' => $images,
        'onSale' => $onSale,
        'discount' => $discount,
        'inStock' => $inStock,
      ])

      {{-- Product Summary --}}
      <div class="product-summary lg:sticky lg:top-8 lg:self-start">
        {{-- Product Title --}}
        <h1 class="text-2xl font-bold tracking-tight text-secondary-900 sm:text-3xl lg:text-4xl" itemprop="name">
          {{ $name }}
        </h1>

        {{-- Rating removed - reviews disabled --}}

        {{-- Price --}}
        <div class="mt-6" itemprop="offers" itemscope itemtype="http://schema.org/Offer">
          <meta itemprop="priceCurrency" content="{{ get_woocommerce_currency() }}" />
          <meta itemprop="price" content="{{ $price }}" />
          <link itemprop="availability" href="http://schema.org/{{ $inStock ? 'InStock' : 'OutOfStock' }}" />

          <x-price :product="$id" size="xl" :show-badge="true" />
        </div>

        {{-- Short Description --}}
        @if ($shortDesc)
          <div class="mt-6 prose prose-secondary max-w-none text-secondary-600" itemprop="description">
            {!! $shortDesc !!}
          </div>
        @endif

        {{-- Stock Status --}}
        <div class="mt-6">
          @if ($inStock)
            <div class="inline-flex items-center gap-2 rounded-full bg-green-50 px-4 py-2 ring-1 ring-inset ring-green-500/20">
              <svg class="h-4 w-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
              </svg>
              <span class="text-sm font-medium text-green-700">
                @if ($stockQty && $managingStock)
                  {{ sprintf(__('%d in stock', 'sage'), $stockQty) }}
                @else
                  {{ __('In stock', 'sage') }}
                @endif
              </span>
            </div>
          @elseif ($allowBackorders)
            <div class="inline-flex items-center gap-2 rounded-full bg-amber-50 px-4 py-2 ring-1 ring-inset ring-amber-500/20">
              <svg class="h-4 w-4 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
              </svg>
              <span class="text-sm font-medium text-amber-700">{{ __('Available on backorder', 'sage') }}</span>
            </div>
          @else
            <div class="inline-flex items-center gap-2 rounded-full bg-red-50 px-4 py-2 ring-1 ring-inset ring-red-500/20">
              <svg class="h-4 w-4 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
              </svg>
              <span class="text-sm font-medium text-red-700">{{ __('Out of stock', 'sage') }}</span>
            </div>
          @endif
        </div>

        {{-- Add to Cart Form --}}
        @if ($purchasable && $inStock)
          <div class="mt-8">
            @if ($isSimpleProduct)
              {{-- Simple Product Add to Cart --}}
              @include('woocommerce.single-product.add-to-cart.simple', [
                'productId' => $id,
                'cartUrl' => $cartUrl,
                'cartText' => $cartText,
                'quantityData' => $quantityInputData(),
                'inStock' => $inStock,
                'purchasable' => $purchasable,
                'stockQty' => $stockQty,
                'managingStock' => $managingStock,
              ])
            @elseif ($isVariableProduct)
              {{-- Variable Product Add to Cart --}}
              @include('woocommerce.single-product.add-to-cart.variable', [
                'productId' => $id,
                'cartUrl' => $cartUrl,
                'cartText' => $cartText,
                'quantityData' => $quantityInputData(),
                'inStock' => $inStock,
                'purchasable' => $purchasable,
                'variationAttributes' => $variationAttributes(),
                'defaultAttributes' => $defaultAttributes(),
                'variationsJson' => $variationsJson(),
                'priceRange' => $priceRange(),
                'variationAttributesWithDisplay' => $variationAttributesWithDisplay(),
              ])
            @elseif ($isExternalProduct)
              {{-- External/Affiliate Product --}}
              @php $product = $product() @endphp
              <a
                href="{{ $product->get_product_url() }}"
                target="_blank"
                rel="noopener noreferrer"
                class="inline-flex items-center gap-2 rounded-lg bg-primary-600 px-8 py-3 text-base font-semibold text-white transition-colors hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
              >
                {{ $product->get_button_text() ?: __('Buy product', 'sage') }}
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                </svg>
              </a>
            @endif
          </div>
        @elseif (!$inStock)
          {{-- Out of Stock Message --}}
          <div class="mt-8 rounded-lg border border-red-200 bg-red-50 p-4">
            <p class="flex items-center gap-2 text-red-700">
              <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
              </svg>
              {{ __('This product is currently out of stock and unavailable.', 'sage') }}
            </p>
          </div>
        @endif

        {{-- Product Meta --}}
        <div class="mt-10 rounded-xl border border-secondary-200 bg-secondary-50/50 p-6">
          @php
            $productCategories = $categories();
            $productTags = $tags();
          @endphp
          <dl class="divide-y divide-secondary-200">
            @if ($productSku)
              <div class="flex items-center justify-between py-3 first:pt-0 last:pb-0">
                <dt class="text-sm font-medium text-secondary-500">{{ __('SKU', 'sage') }}</dt>
                <dd class="text-sm font-mono text-secondary-900" itemprop="sku">{{ $productSku }}</dd>
              </div>
            @endif

            @if (!empty($productCategories))
              <div class="flex flex-wrap items-start justify-between gap-3 py-3 first:pt-0 last:pb-0">
                <dt class="text-sm font-medium text-secondary-500">{{ __('Categories', 'sage') }}</dt>
                <dd class="flex flex-wrap justify-end gap-2">
                  @foreach ($productCategories as $category)
                    <a
                      href="{{ $category['url'] }}"
                      class="inline-flex items-center rounded-md bg-primary-50 px-2 py-1 text-xs font-medium text-primary-700 ring-1 ring-inset ring-primary-600/20 transition-colors hover:bg-primary-100"
                    >
                      {{ $category['name'] }}
                    </a>
                  @endforeach
                </dd>
              </div>
            @endif

            @if (!empty($productTags))
              <div class="flex flex-wrap items-start justify-between gap-3 py-3 first:pt-0 last:pb-0">
                <dt class="text-sm font-medium text-secondary-500">{{ __('Tags', 'sage') }}</dt>
                <dd class="flex flex-wrap justify-end gap-1.5">
                  @foreach ($productTags as $tag)
                    <a
                      href="{{ $tag['url'] }}"
                      class="rounded-full bg-secondary-100 px-2.5 py-1 text-xs font-medium text-secondary-600 transition-colors hover:bg-secondary-200 hover:text-secondary-900"
                    >
                      {{ $tag['name'] }}
                    </a>
                  @endforeach
                </dd>
              </div>
            @endif
          </dl>
        </div>
      </div>
    </div>

    {{-- Product Tabs --}}
    @include('woocommerce.single-product.tabs.tabs', [
      'hasDescription' => $hasDesc,
      'hasAdditionalInfo' => $hasAddInfo,
      'reviewsEnabled' => $reviewsAllowed,
      'description' => $fullDesc,
      'visibleAttributes' => $visibleAttributes(),
      'weight' => $productWeight,
      'dimensions' => $productDimensions,
      'hasWeight' => $hasProductWeight,
      'hasDimensions' => $hasProductDimensions,
      'reviewCount' => $numReviews,
    ])

    {{-- Related Products --}}
    @include('woocommerce.single-product.related', [
      'products' => $relatedProducts(4),
    ])

    {{-- Upsell Products --}}
    @include('woocommerce.single-product.upsells', [
      'products' => $upsellProducts(4),
    ])
  </div>

  {{-- Mobile Sticky Add to Cart Bar --}}
  @if ($purchasable && $inStock)
    <div
      id="mobile-sticky-cart"
      class="fixed inset-x-0 bottom-0 z-40 translate-y-full border-t border-secondary-200 bg-white/95 px-4 pb-safe pt-3 shadow-[0_-4px_20px_-4px_rgba(0,0,0,0.1)] backdrop-blur-lg transition-transform duration-300 lg:hidden"
    >
      <div class="flex items-center gap-4">
        {{-- Product Info --}}
        <div class="min-w-0 flex-1">
          <p class="truncate text-sm font-semibold text-secondary-900">{{ $name }}</p>
          <div class="mt-0.5">
            <x-price :product="$id" size="sm" :show-badge="false" />
          </div>
        </div>

        {{-- Add to Cart Button --}}
        @if ($isSimpleProduct)
          <form action="{{ $cartUrl }}" method="post" class="mobile-quick-cart">
            <input type="hidden" name="add-to-cart" value="{{ $id }}" />
            <input type="hidden" name="quantity" value="1" />
            <button
              type="submit"
              class="flex items-center gap-2 rounded-xl bg-primary-600 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-primary-600/25 transition-all hover:bg-primary-700 active:scale-95"
            >
              <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
              </svg>
              {{ __('Add to Cart', 'sage') }}
            </button>
          </form>
        @elseif ($isVariableProduct)
          <a
            href="#product-{{ $id }}"
            class="flex items-center gap-2 rounded-xl bg-primary-600 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-primary-600/25 transition-all hover:bg-primary-700 active:scale-95"
          >
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
            </svg>
            {{ __('Select Options', 'sage') }}
          </a>
        @endif
      </div>
    </div>

    {{-- Mobile Sticky Cart Script --}}
    <script>
      (function() {
        const stickyBar = document.getElementById('mobile-sticky-cart');
        const productSummary = document.querySelector('.product-summary');

        if (!stickyBar || !productSummary) return;

        let lastScroll = 0;
        let ticking = false;

        function updateStickyBar() {
          const summaryRect = productSummary.getBoundingClientRect();
          const isAboveViewport = summaryRect.bottom < 0;
          const scrollingDown = window.scrollY > lastScroll;

          // Show sticky bar when product summary is scrolled out of view
          if (isAboveViewport || (scrollingDown && window.scrollY > 300)) {
            stickyBar.classList.remove('translate-y-full');
          } else {
            stickyBar.classList.add('translate-y-full');
          }

          lastScroll = window.scrollY;
          ticking = false;
        }

        window.addEventListener('scroll', function() {
          if (!ticking) {
            window.requestAnimationFrame(updateStickyBar);
            ticking = true;
          }
        }, { passive: true });
      })();
    </script>
  @endif

  {{-- Variations Quantity JavaScript --}}
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Quantity buttons for variable products only
      // (Simple products have their own handler in add-to-cart/simple.blade.php)
      // (Tab switching is handled in tabs/tabs.blade.php)
      document.querySelectorAll('.variations-form .quantity-minus, .variations-form .quantity-plus').forEach(function(btn) {
        btn.addEventListener('click', function() {
          const input = this.parentElement.querySelector('input[type="number"]');
          const min = parseInt(input.min) || 1;
          const max = parseInt(input.max) || 999;
          const step = parseInt(input.step) || 1;
          let value = parseInt(input.value) || min;

          if (this.classList.contains('quantity-minus')) {
            value = Math.max(min, value - step);
          } else {
            value = Math.min(max, value + step);
          }

          input.value = value;
          input.dispatchEvent(new Event('change', { bubbles: true }));
        });
      });
    });
  </script>
@endsection
