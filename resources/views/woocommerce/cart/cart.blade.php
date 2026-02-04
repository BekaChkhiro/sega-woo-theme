@extends('layouts.app')

@section('breadcrumbs')
  <x-breadcrumbs :items="[
    ['label' => __('Home', 'sage'), 'url' => home_url('/')],
    ['label' => __('Shop', 'sage'), 'url' => wc_get_page_permalink('shop')],
    ['label' => __('Cart', 'sage'), 'url' => null],
  ]" />
@endsection

@section('page-header')
  <div class="mb-8">
    <h1 class="text-2xl font-bold text-secondary-900 lg:text-3xl">
      {{ __('Shopping Cart', 'sage') }}
    </h1>
  </div>
@endsection

@section('content')
  @php
    $cart = WC()->cart;
    $cartItems = $cart->get_cart();
    $cartIsEmpty = $cart->is_empty();
  @endphp

  @if ($cartIsEmpty)
    {{-- Empty Cart State --}}
    <div class="flex flex-col items-center justify-center py-16 text-center">
      <div class="mb-6 flex h-24 w-24 items-center justify-center rounded-full bg-secondary-100">
        <svg class="h-12 w-12 text-secondary-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" />
        </svg>
      </div>

      <h2 class="mb-2 text-xl font-semibold text-secondary-900">
        {{ __('Your cart is empty', 'sage') }}
      </h2>

      <p class="mb-8 max-w-sm text-secondary-600">
        {{ __('Looks like you haven\'t added anything to your cart yet. Start shopping to fill it up!', 'sage') }}
      </p>

      <a
        href="{{ wc_get_page_permalink('shop') }}"
        class="inline-flex items-center gap-2 rounded-xl bg-primary-600 px-8 py-4 text-base font-semibold text-white shadow-lg shadow-primary-600/25 transition-all hover:bg-primary-700 hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 active:scale-[0.98]"
      >
        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
        </svg>
        {{ __('Continue Shopping', 'sage') }}
      </a>
    </div>
  @else
    {{-- Cart Form --}}
    <form
      id="cart-form"
      action="{{ wc_get_cart_url() }}"
      method="post"
      class="woocommerce-cart-form"
    >
      @php do_action('woocommerce_before_cart_table'); @endphp

      <div class="grid grid-cols-1 gap-8 lg:grid-cols-3 lg:gap-12">
        {{-- Cart Items --}}
        <div class="lg:col-span-2">
          {{-- Desktop Table Header --}}
          <div class="mb-4 hidden border-b border-secondary-200 pb-4 md:grid md:grid-cols-12 md:gap-4">
            <div class="col-span-6 text-sm font-semibold text-secondary-700">
              {{ __('Product', 'sage') }}
            </div>
            <div class="col-span-2 text-center text-sm font-semibold text-secondary-700">
              {{ __('Price', 'sage') }}
            </div>
            <div class="col-span-2 text-center text-sm font-semibold text-secondary-700">
              {{ __('Quantity', 'sage') }}
            </div>
            <div class="col-span-2 text-right text-sm font-semibold text-secondary-700">
              {{ __('Subtotal', 'sage') }}
            </div>
          </div>

          {{-- Cart Items List --}}
          <div class="cart-items space-y-4">
            @foreach ($cartItems as $cartItemKey => $cartItem)
              @php
                $_product = apply_filters('woocommerce_cart_item_product', $cartItem['data'], $cartItem, $cartItemKey);
                $productId = apply_filters('woocommerce_cart_item_product_id', $cartItem['product_id'], $cartItem, $cartItemKey);
                $productPermalink = apply_filters('woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink($cartItem) : '', $cartItem, $cartItemKey);
                $productName = apply_filters('woocommerce_cart_item_name', $_product->get_name(), $cartItem, $cartItemKey);
                $thumbnail = apply_filters('woocommerce_cart_item_thumbnail', $_product->get_image('woocommerce_thumbnail'), $cartItem, $cartItemKey);
                $productPrice = apply_filters('woocommerce_cart_item_price', WC()->cart->get_product_price($_product), $cartItem, $cartItemKey);
                $productSubtotal = apply_filters('woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal($_product, $cartItem['quantity']), $cartItem, $cartItemKey);
                $quantityInput = woocommerce_quantity_input(
                  [
                    'input_name' => "cart[{$cartItemKey}][qty]",
                    'input_value' => $cartItem['quantity'],
                    'max_value' => $_product->get_max_purchase_quantity(),
                    'min_value' => 0,
                    'product_name' => $productName,
                  ],
                  $_product,
                  false
                );
              @endphp

              @if ($_product && $_product->exists() && $cartItem['quantity'] > 0 && apply_filters('woocommerce_cart_item_visible', true, $cartItem, $cartItemKey))
                <div
                  class="cart-item group rounded-xl border border-secondary-200 bg-white p-4 transition-shadow hover:shadow-md md:grid md:grid-cols-12 md:items-center md:gap-4 md:rounded-lg md:p-0 md:px-4 md:py-4"
                  data-cart-item-key="{{ $cartItemKey }}"
                >
                  {{-- Product Info (Mobile & Desktop) --}}
                  <div class="flex items-start gap-4 md:col-span-6">
                    {{-- Remove Button (Mobile) --}}
                    <button
                      type="button"
                      class="remove-item -ml-1 -mt-1 flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full text-secondary-400 transition-colors hover:bg-red-50 hover:text-red-500 md:hidden"
                      data-cart-item-key="{{ $cartItemKey }}"
                      aria-label="{{ __('Remove item', 'sage') }}"
                    >
                      <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                      </svg>
                    </button>

                    {{-- Product Thumbnail --}}
                    <div class="h-20 w-20 flex-shrink-0 overflow-hidden rounded-lg bg-secondary-100 md:h-16 md:w-16">
                      @if ($productPermalink)
                        <a href="{{ $productPermalink }}" class="block">
                          {!! $thumbnail !!}
                        </a>
                      @else
                        {!! $thumbnail !!}
                      @endif
                    </div>

                    {{-- Product Details --}}
                    <div class="min-w-0 flex-1">
                      @if ($productPermalink)
                        <a href="{{ $productPermalink }}" class="block text-sm font-medium text-secondary-900 transition-colors hover:text-primary-600 md:text-base">
                          {{ $productName }}
                        </a>
                      @else
                        <span class="block text-sm font-medium text-secondary-900 md:text-base">
                          {{ $productName }}
                        </span>
                      @endif

                      {{-- Variation Data --}}
                      @php
                        echo wc_get_formatted_cart_item_data($cartItem);
                      @endphp

                      {{-- Backorder Notice --}}
                      @if ($_product->backorders_require_notification() && $_product->is_on_backorder($cartItem['quantity']))
                        <p class="mt-1 text-xs text-amber-600">
                          {{ esc_html__('Available on backorder', 'woocommerce') }}
                        </p>
                      @endif

                      {{-- Price (Mobile Only) --}}
                      <div class="mt-2 text-sm font-medium text-secondary-900 md:hidden">
                        {!! $productPrice !!}
                      </div>
                    </div>

                    {{-- Remove Button (Desktop) --}}
                    <button
                      type="button"
                      class="remove-item hidden h-8 w-8 flex-shrink-0 items-center justify-center rounded-full text-secondary-400 opacity-0 transition-all hover:bg-red-50 hover:text-red-500 group-hover:opacity-100 md:flex"
                      data-cart-item-key="{{ $cartItemKey }}"
                      aria-label="{{ __('Remove item', 'sage') }}"
                    >
                      <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                      </svg>
                    </button>
                  </div>

                  {{-- Price (Desktop) --}}
                  <div class="hidden text-center text-sm font-medium text-secondary-900 md:col-span-2 md:block">
                    {!! $productPrice !!}
                  </div>

                  {{-- Quantity --}}
                  <div class="mt-4 flex items-center justify-between md:col-span-2 md:mt-0 md:justify-center">
                    <span class="text-sm text-secondary-500 md:hidden">{{ __('Quantity:', 'sage') }}</span>
                    <div class="quantity-wrapper flex items-center">
                      <button
                        type="button"
                        class="quantity-btn quantity-minus flex h-8 w-8 items-center justify-center rounded-l-lg border border-r-0 border-secondary-300 bg-secondary-50 text-secondary-600 transition-colors hover:bg-secondary-100"
                        aria-label="{{ __('Decrease quantity', 'sage') }}"
                      >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4" />
                        </svg>
                      </button>
                      <input
                        type="number"
                        name="cart[{{ $cartItemKey }}][qty]"
                        value="{{ $cartItem['quantity'] }}"
                        min="0"
                        max="{{ $_product->get_max_purchase_quantity() > 0 ? $_product->get_max_purchase_quantity() : '' }}"
                        step="1"
                        class="quantity-input h-8 w-14 border-y border-secondary-300 bg-white text-center text-sm text-secondary-900 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500 [appearance:textfield] [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:appearance-none"
                        aria-label="{{ __('Product quantity', 'sage') }}"
                      />
                      <button
                        type="button"
                        class="quantity-btn quantity-plus flex h-8 w-8 items-center justify-center rounded-r-lg border border-l-0 border-secondary-300 bg-secondary-50 text-secondary-600 transition-colors hover:bg-secondary-100"
                        aria-label="{{ __('Increase quantity', 'sage') }}"
                      >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                      </button>
                    </div>
                  </div>

                  {{-- Subtotal --}}
                  <div class="mt-4 flex items-center justify-between border-t border-secondary-100 pt-4 md:col-span-2 md:mt-0 md:justify-end md:border-t-0 md:pt-0">
                    <span class="text-sm text-secondary-500 md:hidden">{{ __('Subtotal:', 'sage') }}</span>
                    <span class="cart-item-subtotal text-base font-semibold text-secondary-900 transition-all duration-300">
                      {!! $productSubtotal !!}
                    </span>
                  </div>
                </div>
              @endif
            @endforeach
          </div>

          {{-- Cart Actions --}}
          <div class="mt-6 flex flex-col gap-4 border-t border-secondary-200 pt-6 sm:flex-row sm:items-center sm:justify-between">
            {{-- Coupon Form --}}
            @if (wc_coupons_enabled())
              <div class="coupon flex flex-1 gap-2">
                <input
                  type="text"
                  name="coupon_code"
                  id="coupon_code"
                  class="w-full rounded-lg border border-secondary-300 bg-white px-4 py-2.5 text-sm text-secondary-900 placeholder-secondary-400 shadow-sm transition-colors focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500 sm:max-w-[200px]"
                  placeholder="{{ __('Coupon code', 'sage') }}"
                  value=""
                />
                <button
                  type="submit"
                  name="apply_coupon"
                  class="flex-shrink-0 rounded-lg border border-secondary-300 bg-white px-4 py-2.5 text-sm font-medium text-secondary-700 shadow-sm transition-colors hover:bg-secondary-50 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
                  value="{{ __('Apply', 'sage') }}"
                >
                  {{ __('Apply', 'sage') }}
                </button>
              </div>
            @endif

            {{-- Update Cart Button --}}
            <button
              type="submit"
              name="update_cart"
              id="update-cart-btn"
              class="update-cart-btn rounded-lg border border-secondary-300 bg-white px-6 py-2.5 text-sm font-medium text-secondary-700 shadow-sm transition-colors hover:bg-secondary-50 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
              value="{{ __('Update cart', 'sage') }}"
              disabled
            >
              <span class="btn-text">{{ __('Update cart', 'sage') }}</span>
              <span class="btn-loading hidden">
                <svg class="inline h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                {{ __('Updating...', 'sage') }}
              </span>
            </button>
          </div>

          @php wp_nonce_field('woocommerce-cart', 'woocommerce-cart-nonce'); @endphp
        </div>

        {{-- Cart Totals Sidebar --}}
        <div class="lg:col-span-1">
          <div class="cart-collaterals sticky top-8 rounded-xl border border-secondary-200 bg-secondary-50/50 p-6">
            <h2 class="mb-6 text-lg font-semibold text-secondary-900">
              {{ __('Cart totals', 'sage') }}
            </h2>

            <div class="cart_totals space-y-4">
              {{-- Subtotal --}}
              <div class="flex items-center justify-between border-b border-secondary-200 pb-4">
                <span class="text-sm text-secondary-600">{{ __('Subtotal', 'sage') }}</span>
                <span class="cart-subtotal text-sm font-medium text-secondary-900 transition-all duration-300">
                  {!! WC()->cart->get_cart_subtotal() !!}
                </span>
              </div>

              {{-- Coupons Applied --}}
              @foreach (WC()->cart->get_coupons() as $code => $coupon)
                <div class="coupon-{{ sanitize_title($code) }} flex items-center justify-between border-b border-secondary-200 pb-4">
                  <span class="text-sm text-secondary-600">
                    {{ __('Coupon:', 'sage') }} {{ $code }}
                    <a href="{{ esc_url(add_query_arg('remove_coupon', rawurlencode($coupon->get_code()), wc_get_cart_url())) }}" class="ml-1 text-error-500 hover:text-error-600" title="{{ __('Remove coupon', 'sage') }}">[{{ __('Remove', 'sage') }}]</a>
                  </span>
                  <span class="text-sm font-medium text-green-600">
                    -{!! wc_cart_totals_coupon_html($coupon) !!}
                  </span>
                </div>
              @endforeach

              {{-- Shipping --}}
              @if (WC()->cart->needs_shipping() && WC()->cart->show_shipping())
                <div class="shipping border-b border-secondary-200 pb-4">
                  <div class="mb-2 flex items-center justify-between">
                    <span class="text-sm text-secondary-600">{{ __('Shipping', 'sage') }}</span>
                  </div>
                  <div class="shipping-calculator text-sm text-secondary-600">
                    @php wc_cart_totals_shipping_html(); @endphp
                  </div>
                </div>
              @elseif (WC()->cart->needs_shipping() && 'yes' === get_option('woocommerce_enable_shipping_calc'))
                <div class="shipping border-b border-secondary-200 pb-4">
                  <div class="flex items-center justify-between">
                    <span class="text-sm text-secondary-600">{{ __('Shipping', 'sage') }}</span>
                    <span class="text-sm text-secondary-500">{{ __('Calculated at checkout', 'sage') }}</span>
                  </div>
                </div>
              @endif

              {{-- Fees --}}
              @foreach (WC()->cart->get_fees() as $fee)
                <div class="fee flex items-center justify-between border-b border-secondary-200 pb-4">
                  <span class="text-sm text-secondary-600">{{ $fee->name }}</span>
                  <span class="text-sm font-medium text-secondary-900">
                    {!! wc_cart_totals_fee_html($fee) !!}
                  </span>
                </div>
              @endforeach

              {{-- Tax (if displayed separately) --}}
              @if (wc_tax_enabled() && !WC()->cart->display_prices_including_tax())
                @foreach (WC()->cart->get_tax_totals() as $code => $tax)
                  <div class="tax-rate tax-rate-{{ sanitize_title($code) }} flex items-center justify-between border-b border-secondary-200 pb-4">
                    <span class="text-sm text-secondary-600">{{ $tax->label }}</span>
                    <span class="text-sm font-medium text-secondary-900">
                      {!! wc_price($tax->amount) !!}
                    </span>
                  </div>
                @endforeach
              @endif

              {{-- Total --}}
              <div class="order-total flex items-center justify-between pt-2">
                <span class="text-base font-semibold text-secondary-900">{{ __('Total', 'sage') }}</span>
                <span class="cart-total text-xl font-bold text-secondary-900 transition-all duration-300">
                  {!! WC()->cart->get_total() !!}
                </span>
              </div>

              {{-- Tax note --}}
              @if (wc_tax_enabled() && WC()->cart->display_prices_including_tax())
                <p class="text-xs text-secondary-500">
                  {!! sprintf(__('(includes %s tax)', 'woocommerce'), wc_price(WC()->cart->get_taxes_total())) !!}
                </p>
              @endif
            </div>

            {{-- Proceed to Checkout --}}
            <div class="wc-proceed-to-checkout mt-6">
              @php do_action('woocommerce_proceed_to_checkout'); @endphp

              <a
                href="{{ wc_get_checkout_url() }}"
                class="checkout-button flex w-full items-center justify-center gap-2 rounded-xl bg-primary-600 px-6 py-4 text-base font-semibold text-white shadow-lg shadow-primary-600/25 transition-all hover:bg-primary-700 hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 active:scale-[0.98]"
              >
                {{ __('Proceed to Checkout', 'sage') }}
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                </svg>
              </a>
            </div>

            {{-- Continue Shopping Link --}}
            <div class="mt-4 text-center">
              <a
                href="{{ wc_get_page_permalink('shop') }}"
                class="inline-flex items-center gap-1 text-sm text-secondary-600 transition-colors hover:text-primary-600"
              >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M7 16l-4-4m0 0l4-4m-4 4h18" />
                </svg>
                {{ __('Continue Shopping', 'sage') }}
              </a>
            </div>
          </div>
        </div>
      </div>

      @php do_action('woocommerce_after_cart_table'); @endphp
    </form>

    {{-- Cross-sells --}}
    @php
      $crossSells = array_filter(array_map('wc_get_product', WC()->cart->get_cross_sells()), 'wc_products_array_filter_visible');
      $crossSells = array_slice($crossSells, 0, 4);
    @endphp

    @if (!empty($crossSells))
      <div class="cross-sells mt-12 border-t border-secondary-200 pt-12">
        <h2 class="mb-6 text-xl font-bold text-secondary-900">
          {{ __('You may be interested in...', 'sage') }}
        </h2>

        <ul class="products grid grid-cols-1 gap-4 xs:grid-cols-2 sm:gap-6 lg:grid-cols-4">
          @foreach ($crossSells as $crossSellProduct)
            <li class="flex">
              <x-product-card :product="$crossSellProduct" class="w-full" />
            </li>
          @endforeach
        </ul>
      </div>
    @endif
  @endif

  {{-- Cart JavaScript --}}
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const cartForm = document.getElementById('cart-form');
      if (!cartForm) return;

      const updateCartBtn = document.getElementById('update-cart-btn');
      let quantityInputs = cartForm.querySelectorAll('.quantity-input');
      let removeButtons = cartForm.querySelectorAll('.remove-item');

      // Track if cart has been modified and debounce timer
      let cartModified = false;
      let updateDebounceTimer = null;
      let isUpdating = false;

      // Get AJAX URL and nonce (with multiple fallbacks for reliability)
      const ajaxUrl = woocommerce_params?.ajax_url || sega_cart_params?.ajax_url || '/wp-admin/admin-ajax.php';
      const wcAjaxUrl = wc_cart_fragments_params?.wc_ajax_url || sega_cart_params?.wc_ajax_url || '/?wc-ajax=%%endpoint%%';
      const updateNonce = sega_cart_params?.update_cart_nonce || '';

      // Debug log to help identify issues
      console.log('Cart AJAX Config:', { ajaxUrl, wcAjaxUrl, hasWcParams: !!woocommerce_params, hasSegaParams: !!sega_cart_params });

      // Show loading state on cart item
      function showItemLoading(cartItem) {
        cartItem.classList.add('opacity-60', 'pointer-events-none');
        const wrapper = cartItem.querySelector('.quantity-wrapper');
        if (wrapper && !wrapper.querySelector('.quantity-loading')) {
          const loadingSpinner = document.createElement('div');
          loadingSpinner.className = 'quantity-loading absolute inset-0 flex items-center justify-center bg-white/80 rounded-lg';
          loadingSpinner.innerHTML = `
            <svg class="h-5 w-5 animate-spin text-primary-600" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
          `;
          wrapper.style.position = 'relative';
          wrapper.appendChild(loadingSpinner);
        }
      }

      // Hide loading state on cart item
      function hideItemLoading(cartItem) {
        cartItem.classList.remove('opacity-60', 'pointer-events-none');
        const loadingSpinner = cartItem.querySelector('.quantity-loading');
        if (loadingSpinner) {
          loadingSpinner.remove();
        }
      }

      // Show global cart updating state
      function showCartUpdating() {
        isUpdating = true;
        const totalsSection = document.querySelector('.cart_totals');
        if (totalsSection && !totalsSection.querySelector('.totals-loading-overlay')) {
          totalsSection.style.position = 'relative';
          const overlay = document.createElement('div');
          overlay.className = 'totals-loading-overlay absolute inset-0 flex items-center justify-center bg-white/70 rounded-xl z-10';
          overlay.innerHTML = `
            <svg class="h-6 w-6 animate-spin text-primary-600" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
          `;
          totalsSection.appendChild(overlay);
        }
      }

      // Hide global cart updating state
      function hideCartUpdating() {
        isUpdating = false;
        const overlay = document.querySelector('.totals-loading-overlay');
        if (overlay) {
          overlay.remove();
        }
      }

      // Update cart via AJAX
      async function updateCartQuantity(cartItemKey, quantity, cartItem) {
        if (isUpdating) return;

        showItemLoading(cartItem);
        showCartUpdating();

        try {
          // Get nonce from form or our custom params
          const nonce = document.querySelector('[name="woocommerce-cart-nonce"]')?.value || updateNonce || '';

          console.log('Updating cart:', { cartItemKey, quantity, ajaxUrl, hasNonce: !!nonce });

          // Use our custom AJAX endpoint
          const response = await fetch(ajaxUrl, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/x-www-form-urlencoded',
            },
            credentials: 'same-origin',
            body: new URLSearchParams({
              action: 'update_cart_item_qty',
              cart_item_key: cartItemKey,
              quantity: quantity,
              security: nonce
            })
          });

          console.log('Cart update response status:', response.status);

          if (!response.ok) {
            console.error('Cart update failed with status:', response.status);
            throw new Error('Network response was not ok: ' + response.status);
          }

          const responseText = await response.text();
          console.log('Cart update raw response:', responseText.substring(0, 200));

          let result;
          try {
            result = JSON.parse(responseText);
          } catch (parseError) {
            console.error('Failed to parse JSON response:', parseError);
            throw new Error('Invalid JSON response from server');
          }

          if (result.success) {
            // Update the UI with new values
            updateCartUI(result.data);

            // Update fragments
            if (result.data.fragments) {
              for (const [selector, html] of Object.entries(result.data.fragments)) {
                const elements = document.querySelectorAll(selector);
                elements.forEach(el => {
                  el.outerHTML = html;
                });
              }
            }

            // Handle item removal
            if (result.data.item_removed) {
              removeCartItemFromDOM(cartItemKey);

              // Check if cart is now empty
              if (result.data.is_empty) {
                window.location.reload();
                return;
              }
            } else {
              // Show success feedback
              showQuantitySuccess(cartItem);
            }

            // Trigger WooCommerce events
            if (typeof jQuery !== 'undefined') {
              jQuery(document.body).trigger('wc_fragment_refresh');
              jQuery(document.body).trigger('updated_cart_totals');
            }

            cartModified = false;
            if (updateCartBtn) {
              updateCartBtn.disabled = true;
            }
          } else {
            // Show error message from server
            const errorMessage = result.data?.message || '{{ __('Error updating cart. Please try again.', 'sage') }}';
            showToast(errorMessage, 'error');

            // If max quantity error, reset input to max
            if (result.data?.max_quantity) {
              const input = cartItem.querySelector('.quantity-input');
              if (input) {
                input.value = result.data.max_quantity;
              }
            }
          }
        } catch (error) {
          console.error('Error updating quantity:', error);

          // Fall back to form-based update
          await updateCartViaForm(cartItemKey, quantity);
        } finally {
          hideItemLoading(cartItem);
          hideCartUpdating();
        }
      }

      // Fallback: Update cart via form submission with fetch
      async function updateCartViaForm(cartItemKey, quantity) {
        try {
          const formData = new FormData(cartForm);

          // Update the specific quantity in form data
          formData.set(`cart[${cartItemKey}][qty]`, quantity);
          formData.set('update_cart', 'Update cart');

          const response = await fetch(cartForm.action, {
            method: 'POST',
            body: formData
          });

          if (response.ok) {
            // Parse the HTML response to extract updated values
            const html = await response.text();
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');

            // Update cart totals
            updateCartTotalsFromHTML(doc);

            // Update item subtotal
            updateItemSubtotalFromHTML(doc, cartItemKey);

            // Check if item was removed (quantity = 0)
            if (quantity === 0) {
              removeCartItemFromDOM(cartItemKey);
            }

            // Trigger WooCommerce fragment refresh
            if (typeof jQuery !== 'undefined') {
              jQuery(document.body).trigger('wc_fragment_refresh');
            }

            cartModified = false;
            if (updateCartBtn) {
              updateCartBtn.disabled = true;
            }
          }
        } catch (error) {
          console.error('Form update error:', error);
          showToast('{{ __('Error updating cart. Please try again.', 'sage') }}', 'error');
        }
      }

      // Update cart totals from parsed HTML
      function updateCartTotalsFromHTML(doc) {
        // Update subtotal
        const newSubtotal = doc.querySelector('.cart-subtotal');
        const currentSubtotal = document.querySelector('.cart-subtotal');
        if (newSubtotal && currentSubtotal) {
          currentSubtotal.innerHTML = newSubtotal.innerHTML;
          animateValue(currentSubtotal);
        }

        // Update total
        const newTotal = doc.querySelector('.cart-total');
        const currentTotal = document.querySelector('.cart-total');
        if (newTotal && currentTotal) {
          currentTotal.innerHTML = newTotal.innerHTML;
          animateValue(currentTotal);
        }

        // Update shipping if present
        const newShipping = doc.querySelector('.shipping-calculator');
        const currentShipping = document.querySelector('.shipping-calculator');
        if (newShipping && currentShipping) {
          currentShipping.innerHTML = newShipping.innerHTML;
        }
      }

      // Update specific item subtotal from parsed HTML
      function updateItemSubtotalFromHTML(doc, cartItemKey) {
        const newItem = doc.querySelector(`[data-cart-item-key="${cartItemKey}"]`);
        const currentItem = document.querySelector(`[data-cart-item-key="${cartItemKey}"]`);

        if (newItem && currentItem) {
          const newSubtotal = newItem.querySelector('.cart-item-subtotal');
          const currentSubtotal = currentItem.querySelector('.cart-item-subtotal');
          if (newSubtotal && currentSubtotal) {
            currentSubtotal.innerHTML = newSubtotal.innerHTML;
            animateValue(currentSubtotal);
          }
        }
      }

      // Remove cart item from DOM with animation
      function removeCartItemFromDOM(cartItemKey) {
        const cartItem = document.querySelector(`[data-cart-item-key="${cartItemKey}"]`);
        if (cartItem) {
          cartItem.style.transition = 'all 0.3s ease-out';
          cartItem.style.opacity = '0';
          cartItem.style.transform = 'translateX(-20px)';

          setTimeout(() => {
            cartItem.remove();

            // Check if cart is now empty
            const remainingItems = document.querySelectorAll('.cart-item');
            if (remainingItems.length === 0) {
              window.location.reload();
            }
          }, 300);
        }
      }

      // Animate value change
      function animateValue(element) {
        element.classList.add('scale-105', 'text-primary-600');
        setTimeout(() => {
          element.classList.remove('scale-105', 'text-primary-600');
        }, 300);
      }

      // Show success feedback on quantity change
      function showQuantitySuccess(cartItem) {
        const wrapper = cartItem.querySelector('.quantity-wrapper');
        if (wrapper) {
          wrapper.classList.add('ring-2', 'ring-green-500', 'ring-opacity-50', 'rounded-lg');
          setTimeout(() => {
            wrapper.classList.remove('ring-2', 'ring-green-500', 'ring-opacity-50', 'rounded-lg');
          }, 500);
        }
      }

      // Show toast notification
      function showToast(message, type = 'info') {
        document.body.dispatchEvent(new CustomEvent('show-toast', {
          detail: { message, type }
        }));
      }

      // Update cart UI from AJAX response
      function updateCartUI(data) {
        // Update cart subtotal
        if (data.cart_subtotal) {
          const subtotalEl = document.querySelector('.cart-subtotal');
          if (subtotalEl) {
            subtotalEl.innerHTML = data.cart_subtotal;
            animateValue(subtotalEl);
          }
        }

        // Update cart total
        if (data.cart_total) {
          const totalEl = document.querySelector('.cart-total');
          if (totalEl) {
            totalEl.innerHTML = data.cart_total;
            animateValue(totalEl);
          }
        }

        // Update specific item subtotal
        if (data.item_subtotal && data.cart_item_key) {
          const itemEl = document.querySelector(`[data-cart-item-key="${data.cart_item_key}"] .cart-item-subtotal`);
          if (itemEl) {
            itemEl.innerHTML = data.item_subtotal;
            animateValue(itemEl);
          }
        }

        // Re-query elements after potential DOM updates
        quantityInputs = cartForm.querySelectorAll('.quantity-input');
        removeButtons = cartForm.querySelectorAll('.remove-item');
        reinitializeEventListeners();
      }

      // Debounced quantity update
      function debouncedUpdate(cartItemKey, quantity, cartItem) {
        clearTimeout(updateDebounceTimer);
        updateDebounceTimer = setTimeout(() => {
          updateCartQuantity(cartItemKey, quantity, cartItem);
        }, 500);
      }

      // Enable update button when quantities change
      function enableUpdateButton() {
        if (updateCartBtn) {
          updateCartBtn.disabled = false;
          cartModified = true;
        }
      }

      // Initialize event listeners for quantity buttons and inputs
      function initializeQuantityControls() {
        const quantityBtns = cartForm.querySelectorAll('.quantity-btn');
        console.log('Found quantity buttons:', quantityBtns.length);

        // Quantity buttons
        quantityBtns.forEach(function(btn) {
          btn.addEventListener('click', function(e) {
            console.log('Quantity button clicked:', this.classList.contains('quantity-minus') ? 'minus' : 'plus');

            const wrapper = this.closest('.quantity-wrapper');
            const input = wrapper.querySelector('.quantity-input');
            const cartItem = this.closest('.cart-item');
            const cartItemKey = cartItem?.dataset?.cartItemKey;

            console.log('Cart item key:', cartItemKey, 'Current value:', input?.value);

            const min = parseInt(input.min) || 0;
            const max = parseInt(input.max) || 9999;
            const step = parseInt(input.step) || 1;
            let value = parseInt(input.value) || min;

            if (this.classList.contains('quantity-minus')) {
              value = Math.max(min, value - step);
            } else {
              value = Math.min(max, value + step);
            }

            input.value = value;

            // AJAX update with debounce
            debouncedUpdate(cartItemKey, value, cartItem);
          });
        });

        // Direct input changes with debounce
        cartForm.querySelectorAll('.quantity-input').forEach(function(input) {
          input.addEventListener('change', function() {
            const cartItem = this.closest('.cart-item');
            const cartItemKey = cartItem.dataset.cartItemKey;
            const value = parseInt(this.value) || 0;

            debouncedUpdate(cartItemKey, value, cartItem);
          });

          // Also handle manual typing with longer debounce
          input.addEventListener('input', function() {
            enableUpdateButton();
          });
        });
      }

      // Initialize remove button handlers
      function initializeRemoveButtons() {
        const removeBtns = cartForm.querySelectorAll('.remove-item');
        console.log('Found remove buttons:', removeBtns.length);

        removeBtns.forEach(function(btn) {
          btn.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('Remove button clicked');

            const cartItemKey = this.dataset.cartItemKey;
            const cartItem = this.closest('.cart-item');

            console.log('Removing cart item:', cartItemKey);

            // Update quantity to 0 (removes item)
            updateCartQuantity(cartItemKey, 0, cartItem);
          });
        });
      }

      // Reinitialize event listeners after DOM updates
      function reinitializeEventListeners() {
        initializeQuantityControls();
        initializeRemoveButtons();
      }

      // Initial setup
      initializeQuantityControls();
      initializeRemoveButtons();

      // Form submission with loading state (fallback for coupon apply, etc.)
      cartForm.addEventListener('submit', function(e) {
        // Don't prevent default - let form submit normally for coupons
        if (updateCartBtn && !updateCartBtn.disabled) {
          updateCartBtn.querySelector('.btn-text')?.classList.add('hidden');
          updateCartBtn.querySelector('.btn-loading')?.classList.remove('hidden');
        }
        cartModified = false;
      });

      // Warn before leaving with unsaved changes (only if not using AJAX updates)
      window.addEventListener('beforeunload', function(e) {
        if (cartModified && !isUpdating) {
          e.preventDefault();
          e.returnValue = '';
        }
      });

      // Listen for WooCommerce cart updated events
      if (typeof jQuery !== 'undefined') {
        jQuery(document.body).on('updated_cart_totals', function() {
          hideCartUpdating();
          reinitializeEventListeners();
        });

        jQuery(document.body).on('wc_fragments_refreshed', function() {
          reinitializeEventListeners();
        });
      }
    });
  </script>

  @php do_action('woocommerce_after_cart'); @endphp
@endsection
