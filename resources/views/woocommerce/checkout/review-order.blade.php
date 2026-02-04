{{--
  Template: Checkout Order Review
  Description: Displays the order summary table on the checkout page
  Note: This template is loaded via AJAX when cart/shipping options change
  @see woocommerce/templates/checkout/review-order.php
--}}

@php
  $cart = WC()->cart;
@endphp

<table class="shop_table woocommerce-checkout-review-order-table w-full">
  {{-- Cart Items --}}
  <thead class="sr-only">
    <tr>
      <th class="product-name">{{ __('Product', 'woocommerce') }}</th>
      <th class="product-total">{{ __('Subtotal', 'woocommerce') }}</th>
    </tr>
  </thead>

  <tbody class="order-items">
    @php do_action('woocommerce_review_order_before_cart_contents'); @endphp

    @foreach ($cart->get_cart() as $cart_item_key => $cart_item)
      @php
        $_product = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
        $product_name = apply_filters('woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key);
        $product_permalink = apply_filters('woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink($cart_item) : '', $cart_item, $cart_item_key);
        $thumbnail = apply_filters('woocommerce_cart_item_thumbnail', $_product->get_image('woocommerce_gallery_thumbnail'), $cart_item, $cart_item_key);
        $product_subtotal = apply_filters('woocommerce_cart_item_subtotal', $cart->get_product_subtotal($_product, $cart_item['quantity']), $cart_item, $cart_item_key);
      @endphp

      @if ($_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters('woocommerce_checkout_cart_item_visible', true, $cart_item, $cart_item_key))
        <tr class="cart_item {{ apply_filters('woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key) }}">
          <td class="product-name py-3">
            <div class="flex items-start gap-3">
              {{-- Product Thumbnail --}}
              <div class="h-12 w-12 flex-shrink-0 overflow-hidden rounded-lg bg-secondary-100">
                @if ($product_permalink)
                  <a href="{{ $product_permalink }}" class="block">
                    {!! $thumbnail !!}
                  </a>
                @else
                  {!! $thumbnail !!}
                @endif
              </div>

              {{-- Product Details --}}
              <div class="min-w-0 flex-1">
                <div class="flex items-start justify-between gap-2">
                  <div class="min-w-0">
                    @if ($product_permalink)
                      <a href="{{ $product_permalink }}" class="block truncate text-sm font-medium text-secondary-900 hover:text-primary-600">
                        {{ $product_name }}
                      </a>
                    @else
                      <span class="block truncate text-sm font-medium text-secondary-900">
                        {{ $product_name }}
                      </span>
                    @endif
                    <span class="product-quantity text-xs text-secondary-500">
                      {{ __('Qty:', 'sage') }} {{ $cart_item['quantity'] }}
                    </span>
                  </div>
                </div>

                {{-- Variation Data --}}
                <div class="mt-1 text-xs text-secondary-500">
                  {!! wc_get_formatted_cart_item_data($cart_item) !!}
                </div>
              </div>
            </div>
          </td>

          <td class="product-total py-3 text-right align-top">
            <span class="flex-shrink-0 text-sm font-medium text-secondary-900">
              {!! $product_subtotal !!}
            </span>
          </td>
        </tr>
      @endif
    @endforeach

    @php do_action('woocommerce_review_order_after_cart_contents'); @endphp
  </tbody>

  {{-- Order Totals --}}
  <tfoot class="order-totals">
    {{-- Subtotal --}}
    <tr class="cart-subtotal border-t border-secondary-200">
      <th class="py-3 text-left text-sm font-normal text-secondary-600">
        {{ __('Subtotal', 'woocommerce') }}
      </th>
      <td class="py-3 text-right text-sm font-medium text-secondary-900">
        {!! $cart->get_cart_subtotal() !!}
      </td>
    </tr>

    {{-- Coupons --}}
    @foreach ($cart->get_coupons() as $code => $coupon)
      <tr class="cart-discount coupon-{{ sanitize_title($code) }}">
        <th class="py-2 text-left text-sm font-normal text-secondary-600">
          {{ wc_cart_totals_coupon_label($coupon) }}
          <a
            href="{{ esc_url(add_query_arg('remove_coupon', rawurlencode($coupon->get_code()), wc_get_checkout_url())) }}"
            class="ml-1 text-xs text-red-500 hover:text-red-600"
            data-coupon="{{ esc_attr($coupon->get_code()) }}"
          >
            [{{ __('Remove', 'woocommerce') }}]
          </a>
        </th>
        <td class="py-2 text-right text-sm font-medium text-green-600">
          -{!! wc_cart_totals_coupon_html($coupon) !!}
        </td>
      </tr>
    @endforeach

    {{-- Shipping --}}
    @if ($cart->needs_shipping() && $cart->show_shipping())
      @php do_action('woocommerce_review_order_before_shipping'); @endphp

      <tr class="woocommerce-shipping-totals shipping border-t border-secondary-100">
        <th class="py-3 text-left text-sm font-normal text-secondary-600">
          {{ __('Shipping', 'woocommerce') }}
        </th>
        <td class="py-3 text-right text-sm text-secondary-700" data-title="{{ esc_attr(__('Shipping', 'woocommerce')) }}">
          @php wc_cart_totals_shipping_html(); @endphp
        </td>
      </tr>

      @php do_action('woocommerce_review_order_after_shipping'); @endphp
    @endif

    {{-- Fees --}}
    @foreach ($cart->get_fees() as $fee)
      <tr class="fee">
        <th class="py-2 text-left text-sm font-normal text-secondary-600">
          {{ $fee->name }}
        </th>
        <td class="py-2 text-right text-sm font-medium text-secondary-900">
          {!! wc_cart_totals_fee_html($fee) !!}
        </td>
      </tr>
    @endforeach

    {{-- Tax (if prices exclude tax) --}}
    @if (wc_tax_enabled() && !$cart->display_prices_including_tax())
      @php
        $tax_totals = $cart->get_tax_totals();
      @endphp

      @if (get_option('woocommerce_tax_total_display') === 'itemized')
        @foreach ($tax_totals as $code => $tax)
          <tr class="tax-rate tax-rate-{{ sanitize_title($code) }}">
            <th class="py-2 text-left text-sm font-normal text-secondary-600">
              {{ $tax->label }}
            </th>
            <td class="py-2 text-right text-sm font-medium text-secondary-900">
              {!! wp_kses_post(wc_price($tax->amount)) !!}
            </td>
          </tr>
        @endforeach
      @else
        <tr class="tax-total">
          <th class="py-2 text-left text-sm font-normal text-secondary-600">
            {{ esc_html(WC()->countries->tax_or_vat()) }}
          </th>
          <td class="py-2 text-right text-sm font-medium text-secondary-900">
            {!! wp_kses_post(wc_price($cart->get_taxes_total())) !!}
          </td>
        </tr>
      @endif
    @endif

    @php do_action('woocommerce_review_order_before_order_total'); @endphp

    {{-- Order Total --}}
    <tr class="order-total border-t border-secondary-200">
      <th class="py-4 text-left text-base font-semibold text-secondary-900">
        {{ __('Total', 'woocommerce') }}
      </th>
      <td class="py-4 text-right" data-title="{{ esc_attr(__('Total', 'woocommerce')) }}">
        <strong class="text-xl font-bold text-secondary-900">
          {!! $cart->get_total() !!}
        </strong>

        {{-- Tax Note (if prices include tax) --}}
        @if (wc_tax_enabled() && $cart->display_prices_including_tax())
          <p class="mt-1 text-xs text-secondary-500">
            {!! sprintf(__('(includes %s tax)', 'woocommerce'), wc_price($cart->get_taxes_total())) !!}
          </p>
        @endif
      </td>
    </tr>

    @php do_action('woocommerce_review_order_after_order_total'); @endphp
  </tfoot>
</table>
