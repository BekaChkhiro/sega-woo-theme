{{--
  Template: Cart
  Description: Shopping cart page with modern, redesigned layout
  @see https://woocommerce.github.io/code-reference/files/woocommerce-templates-cart-cart.html
--}}

@extends('layouts.app')

@section('breadcrumbs')
  <x-breadcrumbs :items="[
    ['label' => __('Home', 'sage'), 'url' => home_url('/')],
    ['label' => __('Shop', 'sage'), 'url' => wc_get_page_permalink('shop')],
    ['label' => __('Cart', 'sage'), 'url' => null],
  ]" />
@endsection

@section('content')
  @php
    $cart = WC()->cart;
    $cartItems = $cart->get_cart();
    $cartIsEmpty = $cart->is_empty();
    $cartCount = $cart->get_cart_contents_count();
  @endphp

  @if ($cartIsEmpty)
    {{-- Empty Cart State --}}
    <div class="mx-auto max-w-lg py-16">
      <div class="flex flex-col items-center justify-center text-center">
        <div class="mb-8 flex h-32 w-32 items-center justify-center rounded-full bg-gradient-to-br from-secondary-100 to-secondary-50">
          <svg class="h-16 w-16 text-secondary-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" />
          </svg>
        </div>

        <h1 class="mb-3 text-2xl font-bold text-secondary-900">
          {{ __('Your cart is empty', 'sage') }}
        </h1>

        <p class="mb-8 text-secondary-500">
          {{ __('Looks like you haven\'t added anything to your cart yet.', 'sage') }}
        </p>

        <a
          href="{{ wc_get_page_permalink('shop') }}"
          class="inline-flex items-center gap-2 rounded-full bg-primary-600 px-8 py-4 text-base font-semibold text-white shadow-lg shadow-primary-600/20 transition-all hover:bg-primary-700 hover:shadow-xl hover:shadow-primary-600/30 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
        >
          <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
          </svg>
          {{ __('Start Shopping', 'sage') }}
        </a>
      </div>
    </div>
  @else
    {{-- Page Header --}}
    <div class="mb-6 flex flex-col gap-3 sm:mb-8 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h1 class="text-xl font-bold text-secondary-900 sm:text-2xl lg:text-3xl">
          {{ __('Shopping Cart', 'sage') }}
        </h1>
        <p class="mt-0.5 text-sm text-secondary-500 sm:mt-1">
          {{ sprintf(_n('%d item', '%d items', $cartCount, 'sage'), $cartCount) }}
        </p>
      </div>
      <a
        href="{{ wc_get_page_permalink('shop') }}"
        class="inline-flex w-fit items-center gap-1.5 rounded-lg px-3 py-2 text-sm font-medium text-secondary-600 transition-colors hover:bg-secondary-100 hover:text-primary-600 active:scale-[0.98] sm:px-0 sm:py-0 sm:hover:bg-transparent"
      >
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        {{ __('Continue Shopping', 'sage') }}
      </a>
    </div>

    {{-- Cart Form --}}
    <form
      id="cart-form"
      action="{{ wc_get_cart_url() }}"
      method="post"
      class="woocommerce-cart-form"
    >
      @php do_action('woocommerce_before_cart_table'); @endphp

      <div class="grid grid-cols-1 gap-8 xl:grid-cols-12 xl:gap-12">
        {{-- Cart Items Column --}}
        <div class="xl:col-span-8">
          {{-- Cart Items Container --}}
          <div class="overflow-hidden rounded-2xl border border-secondary-200 bg-white shadow-sm">
            {{-- Desktop Table Header --}}
            <div class="hidden border-b border-secondary-100 bg-secondary-50/50 px-6 py-4 md:block">
              <div class="grid grid-cols-12 gap-4">
                <div class="col-span-6 text-xs font-semibold uppercase tracking-wider text-secondary-500">
                  {{ __('Product', 'sage') }}
                </div>
                <div class="col-span-2 text-center text-xs font-semibold uppercase tracking-wider text-secondary-500">
                  {{ __('Price', 'sage') }}
                </div>
                <div class="col-span-2 text-center text-xs font-semibold uppercase tracking-wider text-secondary-500">
                  {{ __('Quantity', 'sage') }}
                </div>
                <div class="col-span-2 text-right text-xs font-semibold uppercase tracking-wider text-secondary-500">
                  {{ __('Total', 'sage') }}
                </div>
              </div>
            </div>

            {{-- Cart Items List --}}
            <div class="cart-items divide-y divide-secondary-100">
              @foreach ($cartItems as $cartItemKey => $cartItem)
                @php
                  $_product = apply_filters('woocommerce_cart_item_product', $cartItem['data'], $cartItem, $cartItemKey);
                  $productId = apply_filters('woocommerce_cart_item_product_id', $cartItem['product_id'], $cartItem, $cartItemKey);
                  $productPermalink = apply_filters('woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink($cartItem) : '', $cartItem, $cartItemKey);
                  $productName = apply_filters('woocommerce_cart_item_name', $_product->get_name(), $cartItem, $cartItemKey);
                  $thumbnail = apply_filters('woocommerce_cart_item_thumbnail', $_product->get_image('woocommerce_thumbnail'), $cartItem, $cartItemKey);
                  $productPrice = apply_filters('woocommerce_cart_item_price', WC()->cart->get_product_price($_product), $cartItem, $cartItemKey);
                  $productSubtotal = apply_filters('woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal($_product, $cartItem['quantity']), $cartItem, $cartItemKey);
                @endphp

                @if ($_product && $_product->exists() && $cartItem['quantity'] > 0 && apply_filters('woocommerce_cart_item_visible', true, $cartItem, $cartItemKey))
                  <div
                    class="cart-item group p-4 md:px-6 md:py-5"
                    data-cart-item-key="{{ $cartItemKey }}"
                  >
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-12 md:items-center">
                      {{-- Product Info --}}
                      <div class="flex gap-4 md:col-span-6">
                        {{-- Product Thumbnail --}}
                        <div class="relative h-24 w-24 flex-shrink-0 overflow-hidden rounded-xl bg-secondary-100 md:h-20 md:w-20">
                          @if ($productPermalink)
                            <a href="{{ $productPermalink }}" class="block h-full w-full">
                              <div class="flex h-full w-full items-center justify-center [&_img]:h-full [&_img]:w-full [&_img]:object-cover">
                                {!! $thumbnail !!}
                              </div>
                            </a>
                          @else
                            <div class="flex h-full w-full items-center justify-center [&_img]:h-full [&_img]:w-full [&_img]:object-cover">
                              {!! $thumbnail !!}
                            </div>
                          @endif

                        </div>

                        {{-- Product Details --}}
                        <div class="flex min-w-0 flex-1 flex-col justify-center">
                          @if ($productPermalink)
                            <a href="{{ $productPermalink }}" class="line-clamp-2 text-sm font-medium text-secondary-900 transition-colors hover:text-primary-600 md:text-base">
                              {{ $productName }}
                            </a>
                          @else
                            <span class="line-clamp-2 text-sm font-medium text-secondary-900 md:text-base">
                              {{ $productName }}
                            </span>
                          @endif

                          {{-- Variation Data --}}
                          <div class="mt-1 text-xs text-secondary-500 [&_dl]:flex [&_dl]:flex-wrap [&_dl]:gap-x-3 [&_dd]:font-medium [&_dd]:text-secondary-600 [&_dt]:after:content-[':']">
                            @php echo wc_get_formatted_cart_item_data($cartItem); @endphp
                          </div>

                          {{-- Backorder Notice --}}
                          @if ($_product->backorders_require_notification() && $_product->is_on_backorder($cartItem['quantity']))
                            <p class="mt-1.5 inline-flex items-center gap-1 text-xs text-amber-600">
                              <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                              </svg>
                              {{ esc_html__('Available on backorder', 'woocommerce') }}
                            </p>
                          @endif

                          {{-- Price (Mobile Only) --}}
                          <div class="mt-2 text-sm font-semibold text-secondary-900 md:hidden">
                            {!! $productPrice !!}
                          </div>

                          {{-- Remove Button (Desktop) --}}
                          <button
                            type="button"
                            class="remove-item mt-2 hidden items-center gap-1 text-xs text-secondary-400 transition-all hover:text-red-500 md:inline-flex"
                            data-cart-item-key="{{ $cartItemKey }}"
                          >
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                              <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            {{ __('Remove', 'sage') }}
                          </button>
                        </div>
                      </div>

                      {{-- Price (Desktop) --}}
                      <div class="hidden text-center text-sm font-medium text-secondary-700 md:col-span-2 md:block">
                        {!! $productPrice !!}
                      </div>

                      {{-- Quantity --}}
                      <div class="flex items-center justify-between md:col-span-2 md:justify-center">
                        <span class="text-xs font-medium uppercase tracking-wider text-secondary-400 md:hidden">{{ __('Qty', 'sage') }}</span>
                        <div class="quantity-wrapper group/qty inline-flex items-center gap-1 rounded-full bg-secondary-100/80 p-1 transition-all duration-200 hover:bg-secondary-100 hover:shadow-md">
                          {{-- Minus Button --}}
                          <button
                            type="button"
                            class="quantity-btn quantity-minus flex h-8 w-8 items-center justify-center rounded-full bg-white text-secondary-500 shadow-sm ring-1 ring-secondary-200/50 transition-all duration-200 hover:bg-primary-50 hover:text-primary-600 hover:ring-primary-200 active:scale-95 disabled:cursor-not-allowed disabled:opacity-40 disabled:hover:bg-white disabled:hover:text-secondary-500 disabled:hover:ring-secondary-200/50 disabled:active:scale-100 sm:h-9 sm:w-9"
                            aria-label="{{ __('Decrease quantity', 'sage') }}"
                          >
                            <svg class="h-3.5 w-3.5 sm:h-4 sm:w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                              <path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4" />
                            </svg>
                          </button>

                          {{-- Quantity Input --}}
                          <input
                            type="number"
                            name="cart[{{ $cartItemKey }}][qty]"
                            value="{{ $cartItem['quantity'] }}"
                            min="0"
                            max="{{ $_product->get_max_purchase_quantity() > 0 ? $_product->get_max_purchase_quantity() : '' }}"
                            step="1"
                            class="quantity-input h-8 w-10 border-0 bg-transparent text-center text-sm font-bold text-secondary-900 transition-colors focus:outline-none focus:ring-0 sm:h-9 sm:w-12 [appearance:textfield] [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:appearance-none"
                            aria-label="{{ __('Product quantity', 'sage') }}"
                          />

                          {{-- Plus Button --}}
                          <button
                            type="button"
                            class="quantity-btn quantity-plus flex h-8 w-8 items-center justify-center rounded-full bg-white text-secondary-500 shadow-sm ring-1 ring-secondary-200/50 transition-all duration-200 hover:bg-primary-50 hover:text-primary-600 hover:ring-primary-200 active:scale-95 disabled:cursor-not-allowed disabled:opacity-40 disabled:hover:bg-white disabled:hover:text-secondary-500 disabled:hover:ring-secondary-200/50 disabled:active:scale-100 sm:h-9 sm:w-9"
                            aria-label="{{ __('Increase quantity', 'sage') }}"
                          >
                            <svg class="h-3.5 w-3.5 sm:h-4 sm:w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                              <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                            </svg>
                          </button>
                        </div>
                      </div>

                      {{-- Subtotal --}}
                      <div class="flex items-center justify-between border-t border-secondary-100 pt-3 md:col-span-2 md:justify-end md:border-0 md:pt-0">
                        <span class="text-xs font-medium uppercase tracking-wider text-secondary-400 md:hidden">{{ __('Total', 'sage') }}</span>
                        <span class="cart-item-subtotal text-base font-bold text-secondary-900 transition-all">
                          {!! $productSubtotal !!}
                        </span>
                      </div>

                      {{-- Remove Button (Mobile) --}}
                      <div class="col-span-full border-t border-secondary-100 pt-3 md:hidden">
                        <button
                          type="button"
                          class="remove-item flex w-full items-center justify-center gap-2 rounded-lg bg-red-50 px-4 py-2.5 text-sm font-medium text-red-600 transition-all hover:bg-red-100 active:scale-[0.98]"
                          data-cart-item-key="{{ $cartItemKey }}"
                        >
                          <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                          </svg>
                          {{ __('Remove from cart', 'sage') }}
                        </button>
                      </div>
                    </div>
                  </div>
                @endif
              @endforeach
            </div>

            {{-- Cart Actions Footer --}}
            <div class="border-t border-secondary-100 bg-secondary-50/30 px-4 py-4 md:px-6">
              <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                {{-- Coupon Form --}}
                @if (wc_coupons_enabled())
                  <div class="coupon flex w-full gap-2 sm:w-auto">
                    <div class="relative min-w-0 flex-1 sm:w-48 sm:flex-none">
                      <input
                        type="text"
                        name="coupon_code"
                        id="coupon_code"
                        class="h-11 w-full rounded-lg border border-secondary-200 bg-white px-3 pr-10 text-base text-secondary-900 placeholder-secondary-400 shadow-sm transition-colors focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500 sm:h-10 sm:text-sm"
                        placeholder="{{ __('Coupon code', 'sage') }}"
                        value=""
                      />
                      <svg class="absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 text-secondary-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                      </svg>
                    </div>
                    <button
                      type="submit"
                      name="apply_coupon"
                      class="h-11 flex-shrink-0 rounded-lg bg-secondary-900 px-4 text-base font-medium text-white shadow-sm transition-colors hover:bg-secondary-800 focus:outline-none focus:ring-2 focus:ring-secondary-500 focus:ring-offset-2 active:scale-[0.98] sm:h-10 sm:text-sm"
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
                  class="update-cart-btn h-11 w-full rounded-lg border border-secondary-200 bg-white px-5 text-base font-medium text-secondary-600 shadow-sm transition-colors hover:bg-secondary-50 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-40 sm:h-10 sm:w-auto sm:text-sm"
                  value="{{ __('Update cart', 'sage') }}"
                  disabled
                >
                  <span class="btn-text inline-flex items-center justify-center gap-2">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    {{ __('Update cart', 'sage') }}
                  </span>
                  <span class="btn-loading hidden items-center justify-center gap-2">
                    <svg class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    {{ __('Updating...', 'sage') }}
                  </span>
                </button>
              </div>
            </div>
          </div>

          @php wp_nonce_field('woocommerce-cart', 'woocommerce-cart-nonce'); @endphp
        </div>

        {{-- Cart Summary Sidebar --}}
        <div class="xl:col-span-4">
          <div class="cart-collaterals lg:sticky lg:top-6">
            <div class="overflow-hidden rounded-2xl border border-secondary-200 bg-white shadow-sm">
              {{-- Summary Header --}}
              <div class="border-b border-secondary-100 bg-secondary-50/50 px-4 py-3 sm:px-6 sm:py-4">
                <h2 class="text-base font-bold text-secondary-900 sm:text-lg">
                  {{ __('Order Summary', 'sage') }}
                </h2>
              </div>

              {{-- Summary Content --}}
              <div class="cart_totals p-4 sm:p-6">
                <div class="space-y-3 sm:space-y-4">
                  {{-- Subtotal --}}
                  <div class="flex items-center justify-between">
                    <span class="text-sm text-secondary-600">{{ __('Subtotal', 'sage') }}</span>
                    <span class="cart-subtotal text-sm font-semibold text-secondary-900">
                      {!! WC()->cart->get_cart_subtotal() !!}
                    </span>
                  </div>

                  {{-- Coupons Applied --}}
                  @foreach (WC()->cart->get_coupons() as $code => $coupon)
                    <div class="coupon-{{ sanitize_title($code) }} flex items-center justify-between rounded-lg bg-green-50 px-3 py-2">
                      <span class="flex items-center gap-2 text-sm text-green-700">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                        {{ $code }}
                      </span>
                      <span class="flex items-center gap-2 text-sm font-semibold text-green-700">
                        -{!! wc_cart_totals_coupon_html($coupon) !!}
                        <a href="{{ esc_url(add_query_arg('remove_coupon', rawurlencode($coupon->get_code()), wc_get_cart_url())) }}" class="text-green-600 hover:text-green-800" title="{{ __('Remove coupon', 'sage') }}">
                          <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                          </svg>
                        </a>
                      </span>
                    </div>
                  @endforeach

                  {{-- Shipping --}}
                  @if (WC()->cart->needs_shipping() && WC()->cart->show_shipping())
                    <div class="shipping border-t border-secondary-100 pt-4">
                      <div class="mb-2 flex items-center justify-between">
                        <span class="text-sm text-secondary-600">{{ __('Shipping', 'sage') }}</span>
                      </div>
                      <div class="shipping-calculator rounded-lg bg-secondary-50 p-3 text-sm text-secondary-600 [&_.woocommerce-shipping-calculator]:mt-2 [&_label]:block [&_label]:text-sm [&_label]:font-medium [&_label]:text-secondary-700">
                        @php wc_cart_totals_shipping_html(); @endphp
                      </div>
                    </div>
                  @elseif (WC()->cart->needs_shipping() && 'yes' === get_option('woocommerce_enable_shipping_calc'))
                    <div class="flex items-center justify-between border-t border-secondary-100 pt-4">
                      <span class="text-sm text-secondary-600">{{ __('Shipping', 'sage') }}</span>
                      <span class="text-sm text-secondary-500">{{ __('Calculated at checkout', 'sage') }}</span>
                    </div>
                  @endif

                  {{-- Fees --}}
                  @foreach (WC()->cart->get_fees() as $fee)
                    <div class="fee flex items-center justify-between border-t border-secondary-100 pt-4">
                      <span class="text-sm text-secondary-600">{{ $fee->name }}</span>
                      <span class="text-sm font-semibold text-secondary-900">
                        {!! wc_cart_totals_fee_html($fee) !!}
                      </span>
                    </div>
                  @endforeach

                  {{-- Tax (if displayed separately) --}}
                  @if (wc_tax_enabled() && !WC()->cart->display_prices_including_tax())
                    @foreach (WC()->cart->get_tax_totals() as $code => $tax)
                      <div class="tax-rate tax-rate-{{ sanitize_title($code) }} flex items-center justify-between border-t border-secondary-100 pt-4">
                        <span class="text-sm text-secondary-600">{{ $tax->label }}</span>
                        <span class="text-sm font-semibold text-secondary-900">
                          {!! wc_price($tax->amount) !!}
                        </span>
                      </div>
                    @endforeach
                  @endif
                </div>

                {{-- Total --}}
                <div class="order-total mt-4 flex items-center justify-between border-t border-secondary-200 pt-4 sm:mt-6 sm:pt-6">
                  <span class="text-base font-bold text-secondary-900">{{ __('Total', 'sage') }}</span>
                  <span class="cart-total text-xl font-bold text-secondary-900 sm:text-2xl">
                    {!! WC()->cart->get_total() !!}
                  </span>
                </div>

                {{-- Tax note --}}
                @if (wc_tax_enabled() && WC()->cart->display_prices_including_tax())
                  <p class="mt-2 text-right text-xs text-secondary-500">
                    {!! sprintf(__('(includes %s tax)', 'woocommerce'), wc_price(WC()->cart->get_taxes_total())) !!}
                  </p>
                @endif

                {{-- Proceed to Checkout --}}
                <div class="wc-proceed-to-checkout mt-4 sm:mt-6">
                  <a
                    href="{{ wc_get_checkout_url() }}"
                    class="checkout-button flex w-full items-center justify-center gap-2 rounded-xl bg-primary-600 px-5 py-3.5 text-base font-bold text-white shadow-lg shadow-primary-600/20 transition-all hover:bg-primary-700 hover:shadow-xl hover:shadow-primary-600/30 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 active:scale-[0.98] sm:px-6 sm:py-4"
                  >
                    {{ __('Proceed to Checkout', 'sage') }}
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                  </a>
                </div>

              </div>
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
      <div class="cross-sells mt-10 sm:mt-16">
        <div class="mb-4 flex items-center justify-between sm:mb-6">
          <h2 class="text-lg font-bold text-secondary-900 sm:text-xl">
            {{ __('You may also like', 'sage') }}
          </h2>
          <a href="{{ wc_get_page_permalink('shop') }}" class="text-sm font-medium text-primary-600 hover:text-primary-700">
            {{ __('View all', 'sage') }}
          </a>
        </div>

        <ul class="products grid grid-cols-2 gap-3 sm:gap-6 lg:grid-cols-4">
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
      let isUpdating = false;
      let updateDebounceTimer = null;

      // Get AJAX URL - multiple fallbacks for reliability
      const ajaxUrl = '{{ admin_url('admin-ajax.php') }}';
      const cartNonce = '{{ wp_create_nonce('woocommerce-cart') }}';

      // Show loading state on cart item
      function showItemLoading(cartItem) {
        cartItem.classList.add('opacity-50');
        const wrapper = cartItem.querySelector('.quantity-wrapper');
        if (wrapper) {
          wrapper.classList.add('pointer-events-none');
        }
      }

      // Hide loading state
      function hideItemLoading(cartItem) {
        cartItem.classList.remove('opacity-50');
        const wrapper = cartItem.querySelector('.quantity-wrapper');
        if (wrapper) {
          wrapper.classList.remove('pointer-events-none');
        }
      }

      // Show global updating state
      function showCartUpdating() {
        isUpdating = true;
        const totalsSection = document.querySelector('.cart_totals');
        if (totalsSection && !totalsSection.querySelector('.totals-overlay')) {
          const overlay = document.createElement('div');
          overlay.className = 'totals-overlay absolute inset-0 z-10 flex items-center justify-center rounded-xl bg-white/80 backdrop-blur-sm';
          overlay.innerHTML = `
            <svg class="h-6 w-6 animate-spin text-primary-600" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
          `;
          totalsSection.style.position = 'relative';
          totalsSection.appendChild(overlay);
        }
      }

      // Hide global updating state
      function hideCartUpdating() {
        isUpdating = false;
        const overlay = document.querySelector('.totals-overlay');
        if (overlay) overlay.remove();
      }

      // Update cart via AJAX
      async function updateCartQuantity(cartItemKey, quantity, cartItem) {
        if (isUpdating) return;

        isUpdating = true;
        showItemLoading(cartItem);
        showCartUpdating();

        try {
          const response = await fetch(ajaxUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            credentials: 'same-origin',
            body: new URLSearchParams({
              action: 'update_cart_item_qty',
              cart_item_key: cartItemKey,
              quantity: quantity,
              security: cartNonce
            })
          });

          if (!response.ok) throw new Error('Network error: ' + response.status);

          const result = await response.json();

          if (result.success) {
            updateCartUI(result.data);

            if (result.data.item_removed) {
              removeCartItemFromDOM(cartItemKey);

              // Update mini-cart fragments without triggering full page refresh
              if (typeof jQuery !== 'undefined' && result.data.fragments) {
                // Apply fragments directly without triggering refresh
                Object.entries(result.data.fragments).forEach(([selector, html]) => {
                  const elements = document.querySelectorAll(selector);
                  elements.forEach(el => {
                    el.outerHTML = html;
                  });
                });
              }

              if (result.data.is_empty) {
                // Small delay to show animation before reload
                setTimeout(() => window.location.reload(), 500);
                return;
              }
            } else {
              showQuantitySuccess(cartItem);

              // Only trigger fragment refresh for quantity updates, not removals
              if (typeof jQuery !== 'undefined') {
                jQuery(document.body).trigger('wc_fragment_refresh');
                jQuery(document.body).trigger('updated_cart_totals');
              }
            }

            if (updateCartBtn) updateCartBtn.disabled = true;
          } else {
            showToast(result.data?.message || '{{ __('Error updating cart', 'sage') }}', 'error');
          }
        } catch (error) {
          await updateCartViaForm(cartItemKey, quantity);
        } finally {
          isUpdating = false;
          hideItemLoading(cartItem);
          hideCartUpdating();
        }
      }

      // Fallback form update
      async function updateCartViaForm(cartItemKey, quantity) {
        try {
          const formData = new FormData(cartForm);
          formData.set(`cart[${cartItemKey}][qty]`, quantity);
          formData.set('update_cart', 'Update cart');

          const response = await fetch(cartForm.action, { method: 'POST', body: formData });

          if (response.ok) {
            const html = await response.text();
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            updateCartTotalsFromHTML(doc);
            updateItemSubtotalFromHTML(doc, cartItemKey);

            if (quantity === 0) removeCartItemFromDOM(cartItemKey);

            if (typeof jQuery !== 'undefined') {
              jQuery(document.body).trigger('wc_fragment_refresh');
            }
            if (updateCartBtn) updateCartBtn.disabled = true;
          }
        } catch (error) {
          showToast('{{ __('Error updating cart', 'sage') }}', 'error');
        }
      }

      // Update totals from HTML response
      function updateCartTotalsFromHTML(doc) {
        ['cart-subtotal', 'cart-total'].forEach(cls => {
          const newEl = doc.querySelector('.' + cls);
          const curEl = document.querySelector('.' + cls);
          if (newEl && curEl) {
            curEl.innerHTML = newEl.innerHTML;
            animateValue(curEl);
          }
        });
      }

      // Update item subtotal from HTML
      function updateItemSubtotalFromHTML(doc, cartItemKey) {
        const newItem = doc.querySelector(`[data-cart-item-key="${cartItemKey}"]`);
        const curItem = document.querySelector(`[data-cart-item-key="${cartItemKey}"]`);
        if (newItem && curItem) {
          const newSub = newItem.querySelector('.cart-item-subtotal');
          const curSub = curItem.querySelector('.cart-item-subtotal');
          if (newSub && curSub) {
            curSub.innerHTML = newSub.innerHTML;
            animateValue(curSub);
          }
        }
      }

      // Remove item with animation
      function removeCartItemFromDOM(cartItemKey) {
        const item = document.querySelector(`.cart-item[data-cart-item-key="${cartItemKey}"]`);

        if (item) {
          item.style.transition = 'all 0.3s ease-out';
          item.style.opacity = '0';
          item.style.transform = 'translateX(-20px)';
          item.style.maxHeight = item.offsetHeight + 'px';
          item.style.overflow = 'hidden';

          setTimeout(() => {
            item.style.maxHeight = '0';
            item.style.padding = '0';
            item.style.margin = '0';
            item.style.border = 'none';
          }, 200);

          setTimeout(() => {
            item.remove();
            const remaining = document.querySelectorAll('.cart-item');

            if (remaining.length === 0) {
              window.location.reload();
            } else {
              const countEl = document.querySelector('.cart-count, .mini-cart-count');
              if (countEl) {
                countEl.textContent = remaining.length;
              }
            }
          }, 400);
        }
      }

      // Animate value change
      function animateValue(el) {
        el.classList.add('scale-110', 'text-primary-600');
        setTimeout(() => el.classList.remove('scale-110', 'text-primary-600'), 300);
      }

      // Show success feedback
      function showQuantitySuccess(cartItem) {
        const wrapper = cartItem.querySelector('.quantity-wrapper');
        if (wrapper) {
          // Add success ring animation
          wrapper.classList.add('ring-2', 'ring-green-500/50', 'bg-green-50');
          wrapper.style.transform = 'scale(1.02)';
          setTimeout(() => {
            wrapper.classList.remove('ring-2', 'ring-green-500/50', 'bg-green-50');
            wrapper.style.transform = '';
          }, 600);
        }

        // Animate the quantity input
        const input = cartItem.querySelector('.quantity-input');
        if (input) {
          input.classList.add('text-green-600');
          setTimeout(() => input.classList.remove('text-green-600'), 600);
        }
      }

      // Toast notification
      function showToast(message, type = 'info') {
        document.body.dispatchEvent(new CustomEvent('show-toast', { detail: { message, type } }));
      }

      // Update UI from AJAX response
      function updateCartUI(data) {
        if (data.cart_subtotal) {
          const el = document.querySelector('.cart-subtotal');
          if (el) { el.innerHTML = data.cart_subtotal; animateValue(el); }
        }
        if (data.cart_total) {
          const el = document.querySelector('.cart-total');
          if (el) { el.innerHTML = data.cart_total; animateValue(el); }
        }
        if (data.item_subtotal && data.cart_item_key && !data.item_removed) {
          const el = document.querySelector(`[data-cart-item-key="${data.cart_item_key}"] .cart-item-subtotal`);
          if (el) { el.innerHTML = data.item_subtotal; animateValue(el); }
        }

        // Update cart count in header
        if (typeof data.cart_count !== 'undefined') {
          const countBadge = document.querySelector('.mini-cart-count');
          if (countBadge) {
            countBadge.textContent = data.cart_count > 99 ? '99+' : data.cart_count;
            countBadge.classList.toggle('scale-0', data.cart_count === 0);
            countBadge.classList.toggle('scale-100', data.cart_count > 0);
          }

          // Update page header count
          const pageCount = document.querySelector('.cart-page-count');
          if (pageCount) {
            pageCount.textContent = data.cart_count;
          }
        }

        // Don't reinitialize if item was removed (animation is in progress)
        if (!data.item_removed) {
          reinitializeEventListeners();
        }
      }

      // Debounced update
      function debouncedUpdate(cartItemKey, quantity, cartItem) {
        clearTimeout(updateDebounceTimer);
        updateDebounceTimer = setTimeout(() => {
          updateCartQuantity(cartItemKey, quantity, cartItem);
        }, 500);
      }

      // Initialize quantity controls
      function initializeQuantityControls() {
        const quantityBtns = cartForm.querySelectorAll('.quantity-btn');

        quantityBtns.forEach(btn => {
          btn.removeEventListener('click', btn._clickHandler);

          btn._clickHandler = function(e) {
            e.preventDefault();

            const wrapper = this.closest('.quantity-wrapper');
            const input = wrapper?.querySelector('.quantity-input');
            const cartItem = this.closest('.cart-item');
            const cartItemKey = cartItem?.dataset?.cartItemKey;

            if (!input || !cartItemKey) return;

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
            debouncedUpdate(cartItemKey, value, cartItem);
          };

          btn.addEventListener('click', btn._clickHandler);
        });

        cartForm.querySelectorAll('.quantity-input').forEach(input => {
          input.addEventListener('change', function() {
            const cartItem = this.closest('.cart-item');
            const cartItemKey = cartItem.dataset.cartItemKey;
            const value = parseInt(this.value) || 0;
            debouncedUpdate(cartItemKey, value, cartItem);
          });

          input.addEventListener('input', function() {
            if (updateCartBtn) updateCartBtn.disabled = false;
          });
        });
      }

      // Initialize remove buttons
      function initializeRemoveButtons() {
        const removeBtns = cartForm.querySelectorAll('.remove-item');

        removeBtns.forEach(btn => {
          btn.removeEventListener('click', btn._removeHandler);

          btn._removeHandler = function(e) {
            e.preventDefault();

            const cartItemKey = this.dataset.cartItemKey;
            const cartItem = this.closest('.cart-item');

            if (!cartItemKey) return;

            updateCartQuantity(cartItemKey, 0, cartItem);
          };

          btn.addEventListener('click', btn._removeHandler);
        });
      }

      // Reinitialize after DOM updates
      function reinitializeEventListeners() {
        initializeQuantityControls();
        initializeRemoveButtons();
      }

      // Initial setup
      initializeQuantityControls();
      initializeRemoveButtons();

      // Form submit handler
      cartForm.addEventListener('submit', function() {
        if (updateCartBtn && !updateCartBtn.disabled) {
          updateCartBtn.querySelector('.btn-text')?.classList.add('hidden');
          updateCartBtn.querySelector('.btn-loading')?.classList.remove('hidden');
        }
      });

      // WooCommerce events
      if (typeof jQuery !== 'undefined') {
        jQuery(document.body).on('updated_cart_totals wc_fragments_refreshed', function() {
          hideCartUpdating();
          reinitializeEventListeners();
        });
      }
    });
  </script>

  @php do_action('woocommerce_after_cart'); @endphp
@endsection
