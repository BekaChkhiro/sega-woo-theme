{{--
  Template: Checkout Order Review (Improved)
  Description: Enhanced order summary with better thumbnails, clearer pricing, and improved UX
  Task: T10.7 - Order review section improvements
  @see woocommerce/templates/checkout/review-order.php
--}}

@php
  $cart = WC()->cart;
  $cart_count = $cart->get_cart_contents_count();
  $cart_subtotal = $cart->get_subtotal();
  $cart_total = $cart->get_total('edit');
  $has_discount = $cart->get_discount_total() > 0;
  $discount_total = $cart->get_discount_total();
@endphp

<div class="order-review-wrapper">
  {{-- Items Header --}}
  <div class="mb-4 flex items-center justify-between">
    <span class="text-sm font-medium text-secondary-600">
      {{ sprintf(_n('%d item', '%d items', $cart_count, 'sage'), $cart_count) }}
    </span>
    @if ($has_discount)
      <span class="inline-flex items-center gap-1 rounded-full bg-green-100 px-2.5 py-1 text-xs font-semibold text-green-700">
        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        {{ __('Discount Applied', 'sage') }}
      </span>
    @endif
  </div>

  {{-- Cart Items List --}}
  <div class="order-items-list space-y-3">
    @php do_action('woocommerce_review_order_before_cart_contents'); @endphp

    @foreach ($cart->get_cart() as $cart_item_key => $cart_item)
      @php
        $_product = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
        $product_name = apply_filters('woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key);
        $product_permalink = apply_filters('woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink($cart_item) : '', $cart_item, $cart_item_key);
        $thumbnail = $_product->get_image('woocommerce_thumbnail', ['class' => 'h-full w-full object-cover']);
        $product_subtotal = apply_filters('woocommerce_cart_item_subtotal', $cart->get_product_subtotal($_product, $cart_item['quantity']), $cart_item, $cart_item_key);
        $regular_price = $_product->get_regular_price();
        $sale_price = $_product->get_sale_price();
        $is_on_sale = $_product->is_on_sale();
        $variation_data = wc_get_formatted_cart_item_data($cart_item);
      @endphp

      @if ($_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters('woocommerce_checkout_cart_item_visible', true, $cart_item, $cart_item_key))
        <div class="order-item group rounded-xl border border-secondary-100 bg-secondary-50/30 p-3 transition-all hover:border-secondary-200 hover:bg-secondary-50">
          <div class="flex gap-4">
            {{-- Product Thumbnail (Improved - larger with aspect ratio) --}}
            <div class="relative h-20 w-20 flex-shrink-0 overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-secondary-200/50">
              @if ($product_permalink)
                <a href="{{ $product_permalink }}" class="block h-full w-full transition-transform group-hover:scale-105">
                  {!! $thumbnail !!}
                </a>
              @else
                <div class="h-full w-full">
                  {!! $thumbnail !!}
                </div>
              @endif

              {{-- Quantity Badge --}}
              <span class="absolute -right-1.5 -top-1.5 flex h-6 w-6 items-center justify-center rounded-full bg-secondary-800 text-xs font-bold text-white shadow-md">
                {{ $cart_item['quantity'] }}
              </span>

              {{-- Sale Badge --}}
              @if ($is_on_sale)
                <span class="absolute bottom-1 left-1 rounded bg-red-500 px-1.5 py-0.5 text-[10px] font-bold uppercase text-white shadow">
                  {{ __('Sale', 'sage') }}
                </span>
              @endif
            </div>

            {{-- Product Details --}}
            <div class="min-w-0 flex-1">
              <div class="flex items-start justify-between gap-2">
                {{-- Product Name & Variations --}}
                <div class="min-w-0 flex-1">
                  @if ($product_permalink)
                    <a href="{{ $product_permalink }}" class="block text-sm font-semibold text-secondary-900 transition-colors hover:text-primary-600">
                      {{ $product_name }}
                    </a>
                  @else
                    <span class="block text-sm font-semibold text-secondary-900">
                      {{ $product_name }}
                    </span>
                  @endif

                  {{-- Variation Attributes --}}
                  @if ($variation_data)
                    <div class="mt-1 space-y-0.5 text-xs text-secondary-500">
                      {!! $variation_data !!}
                    </div>
                  @endif

                  {{-- SKU (optional) --}}
                  @if ($_product->get_sku())
                    <p class="mt-1 text-[10px] uppercase tracking-wide text-secondary-400">
                      {{ __('SKU:', 'sage') }} {{ $_product->get_sku() }}
                    </p>
                  @endif
                </div>

                {{-- Price --}}
                <div class="flex-shrink-0 text-right">
                  <span class="text-sm font-bold text-secondary-900">
                    {!! $product_subtotal !!}
                  </span>
                  @if ($is_on_sale && $cart_item['quantity'] === 1)
                    <p class="text-xs text-secondary-400 line-through">
                      {!! wc_price($regular_price) !!}
                    </p>
                  @endif
                </div>
              </div>
            </div>
          </div>
        </div>
      @endif
    @endforeach

    @php do_action('woocommerce_review_order_after_cart_contents'); @endphp
  </div>

  {{-- Pricing Breakdown --}}
  <div class="pricing-breakdown mt-6 space-y-0">
    {{-- Subtotal --}}
    <div class="flex items-center justify-between border-t border-secondary-200 py-3">
      <span class="flex items-center gap-2 text-sm text-secondary-600">
        <svg class="h-4 w-4 text-secondary-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" />
        </svg>
        {{ __('Subtotal', 'woocommerce') }}
      </span>
      <span class="text-sm font-medium text-secondary-900">
        {!! $cart->get_cart_subtotal() !!}
      </span>
    </div>

    {{-- Applied Coupons (Enhanced) --}}
    @if ($cart->get_coupons())
      <div class="coupons-section">
        @foreach ($cart->get_coupons() as $code => $coupon)
          <div class="coupon-item flex items-center justify-between border-t border-dashed border-green-200 bg-green-50/50 py-3 -mx-6 px-6">
            <span class="flex items-center gap-2 text-sm text-green-700">
              <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z" />
              </svg>
              <span class="font-medium">{{ strtoupper($coupon->get_code()) }}</span>
              <a
                href="{{ esc_url(add_query_arg('remove_coupon', rawurlencode($coupon->get_code()), wc_get_checkout_url())) }}"
                class="rounded-full bg-green-200/50 p-1 text-green-600 transition-colors hover:bg-red-100 hover:text-red-600"
                title="{{ __('Remove coupon', 'sage') }}"
                data-coupon="{{ esc_attr($coupon->get_code()) }}"
              >
                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </a>
            </span>
            <span class="text-sm font-semibold text-green-600">
              -{!! wc_cart_totals_coupon_html($coupon) !!}
            </span>
          </div>
        @endforeach
      </div>
    @endif

    {{-- Shipping --}}
    @if ($cart->needs_shipping() && $cart->show_shipping())
      @php do_action('woocommerce_review_order_before_shipping'); @endphp

      <div class="shipping-section border-t border-secondary-100 py-3">
        <div class="flex items-start justify-between">
          <span class="flex items-center gap-2 text-sm text-secondary-600">
            <svg class="h-4 w-4 text-secondary-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" />
            </svg>
            {{ __('Shipping', 'woocommerce') }}
          </span>
          <div class="shipping-methods text-right text-sm text-secondary-700" data-title="{{ esc_attr(__('Shipping', 'woocommerce')) }}">
            @php wc_cart_totals_shipping_html(); @endphp
          </div>
        </div>
      </div>

      @php do_action('woocommerce_review_order_after_shipping'); @endphp
    @endif

    {{-- Fees --}}
    @if ($cart->get_fees())
      @foreach ($cart->get_fees() as $fee)
        <div class="fee-item flex items-center justify-between border-t border-secondary-100 py-3">
          <span class="flex items-center gap-2 text-sm text-secondary-600">
            <svg class="h-4 w-4 text-secondary-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            {{ $fee->name }}
          </span>
          <span class="text-sm font-medium text-secondary-900">
            {!! wc_cart_totals_fee_html($fee) !!}
          </span>
        </div>
      @endforeach
    @endif

    {{-- Tax Display (if prices exclude tax) --}}
    @if (wc_tax_enabled() && !$cart->display_prices_including_tax())
      @php
        $tax_totals = $cart->get_tax_totals();
      @endphp

      @if (get_option('woocommerce_tax_total_display') === 'itemized')
        @foreach ($tax_totals as $code => $tax)
          <div class="tax-item flex items-center justify-between border-t border-secondary-100 py-3">
            <span class="flex items-center gap-2 text-sm text-secondary-600">
              <svg class="h-4 w-4 text-secondary-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
              </svg>
              {{ $tax->label }}
            </span>
            <span class="text-sm font-medium text-secondary-900">
              {!! wp_kses_post(wc_price($tax->amount)) !!}
            </span>
          </div>
        @endforeach
      @else
        <div class="tax-total flex items-center justify-between border-t border-secondary-100 py-3">
          <span class="flex items-center gap-2 text-sm text-secondary-600">
            <svg class="h-4 w-4 text-secondary-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
            </svg>
            {{ esc_html(WC()->countries->tax_or_vat()) }}
          </span>
          <span class="text-sm font-medium text-secondary-900">
            {!! wp_kses_post(wc_price($cart->get_taxes_total())) !!}
          </span>
        </div>
      @endif
    @endif

    @php do_action('woocommerce_review_order_before_order_total'); @endphp

    {{-- Savings Display (if discount applied) --}}
    @if ($has_discount)
      <div class="savings-display flex items-center justify-between border-t border-dashed border-green-300 bg-gradient-to-r from-green-50 to-emerald-50 py-3 -mx-6 px-6">
        <span class="flex items-center gap-2 text-sm font-medium text-green-700">
          <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          {{ __('You\'re saving', 'sage') }}
        </span>
        <span class="text-sm font-bold text-green-600">
          {!! wc_price($discount_total) !!}
        </span>
      </div>
    @endif

    {{-- Order Total (Enhanced) --}}
    <div class="order-total-section mt-2 rounded-xl bg-gradient-to-br from-secondary-900 to-secondary-800 p-4 -mx-6 lg:-mx-6">
      <div class="flex items-center justify-between">
        <span class="flex items-center gap-2 text-base font-semibold text-white">
          <svg class="h-5 w-5 text-primary-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
          </svg>
          {{ __('Total', 'woocommerce') }}
        </span>
        <div class="text-right">
          <span class="text-2xl font-bold text-white">
            {!! $cart->get_total() !!}
          </span>

          {{-- Tax Note (if prices include tax) --}}
          @if (wc_tax_enabled() && $cart->display_prices_including_tax())
            <p class="mt-0.5 text-xs text-secondary-400">
              {!! sprintf(__('(includes %s tax)', 'woocommerce'), wc_price($cart->get_taxes_total())) !!}
            </p>
          @endif
        </div>
      </div>

      {{-- Estimated Delivery (optional enhancement) --}}
      @if ($cart->needs_shipping())
        @php
          $chosen_shipping = WC()->session->get('chosen_shipping_methods');
          $shipping_estimate = apply_filters('sage_checkout_shipping_estimate', null, $chosen_shipping);
        @endphp
        @if ($shipping_estimate)
          <div class="mt-3 flex items-center gap-2 border-t border-secondary-700 pt-3 text-xs text-secondary-400">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
            </svg>
            {{ $shipping_estimate }}
          </div>
        @endif
      @endif
    </div>

    @php do_action('woocommerce_review_order_after_order_total'); @endphp
  </div>
</div>

{{-- Inline styles for enhanced order review --}}
<style>
  /* Order review wrapper */
  .order-review-wrapper {
    @apply relative;
  }

  /* Items list scrollable on mobile */
  @media (max-width: 1023px) {
    .order-items-list {
      max-height: 300px;
      overflow-y: auto;
      scrollbar-width: thin;
      scrollbar-color: rgb(209 213 219) transparent;
    }

    .order-items-list::-webkit-scrollbar {
      width: 4px;
    }

    .order-items-list::-webkit-scrollbar-track {
      background: transparent;
    }

    .order-items-list::-webkit-scrollbar-thumb {
      background-color: rgb(209 213 219);
      border-radius: 2px;
    }
  }

  /* Product image enhancements */
  .order-item img {
    @apply h-full w-full object-cover transition-transform duration-200;
  }

  .order-item:hover img {
    @apply scale-105;
  }

  /* Variation data styling */
  .order-item .variation {
    @apply flex items-center gap-1;
  }

  .order-item .variation dt {
    @apply font-medium;
  }

  .order-item .variation dd {
    @apply inline;
  }

  .order-item .variation dd p {
    @apply inline m-0;
  }

  /* Shipping methods in order review */
  .shipping-methods ul {
    @apply list-none m-0 p-0 space-y-2;
  }

  .shipping-methods li {
    @apply flex items-center gap-2 text-sm;
  }

  .shipping-methods input[type="radio"] {
    @apply h-4 w-4 border-secondary-300 text-primary-600 focus:ring-primary-500;
  }

  .shipping-methods label {
    @apply flex-1 cursor-pointer text-secondary-700;
  }

  .shipping-methods .woocommerce-Price-amount {
    @apply font-medium text-secondary-900;
  }

  /* Coupon section enhancements */
  .coupon-item .woocommerce-Price-amount {
    @apply font-semibold;
  }

  /* Total section enhancement */
  .order-total-section {
    position: relative;
    overflow: hidden;
  }

  .order-total-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 50%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.05), transparent);
    animation: totalShine 3s infinite;
  }

  @keyframes totalShine {
    0% { left: -100%; }
    50%, 100% { left: 200%; }
  }

  /* Loading state */
  .order-review-wrapper.updating {
    @apply opacity-50 pointer-events-none;
  }

  .order-review-wrapper.updating::after {
    content: '';
    position: absolute;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(255, 255, 255, 0.7);
  }

  /* Free shipping highlight */
  .shipping-methods .free-shipping-method {
    @apply bg-green-50 rounded-lg p-2 -m-2 border border-green-200;
  }

  .shipping-methods .free-shipping-method .woocommerce-Price-amount {
    @apply text-green-600;
  }

  /* Responsive adjustments */
  @media (max-width: 640px) {
    .order-total-section {
      @apply -mx-4 px-4 rounded-none;
    }

    .order-item {
      @apply p-2;
    }

    .order-item .h-20.w-20 {
      @apply h-16 w-16;
    }
  }
</style>
