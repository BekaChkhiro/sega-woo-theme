{{--
  Template: Thank You / Order Received
  Description: Displays order confirmation after successful checkout
  @see woocommerce/templates/checkout/thankyou.php
--}}

@extends('layouts.app')

@section('breadcrumbs')
  <x-breadcrumbs :items="[
    ['label' => __('Home', 'sage'), 'url' => home_url('/')],
    ['label' => __('Shop', 'sage'), 'url' => wc_get_page_permalink('shop')],
    ['label' => __('Checkout', 'sage'), 'url' => wc_get_checkout_url()],
    ['label' => __('Order Received', 'sage'), 'url' => null],
  ]" />
@endsection

@section('content')
  <div class="woocommerce-order">
    @php
      $order = isset($order) ? $order : false;

      // Try to get order from global or query var if not passed
      if (!$order) {
        global $wp;
        $order_id = isset($wp->query_vars['order-received']) ? absint($wp->query_vars['order-received']) : 0;
        if ($order_id) {
          $order = wc_get_order($order_id);
        }
      }
    @endphp

    @if ($order)
      @php
        $order_items = $order->get_items(apply_filters('woocommerce_purchase_order_item_types', 'line_item'));
        $show_purchase_note = $order->has_status(apply_filters('woocommerce_purchase_note_order_statuses', array('completed', 'processing')));
        $show_customer_details = is_user_logged_in() && $order->get_user_id() === get_current_user_id();
      @endphp

      {{-- Order Confirmation Header --}}
      <div class="mb-8 text-center">
        {{-- Success Icon --}}
        <div class="mx-auto mb-6 flex h-20 w-20 items-center justify-center rounded-full bg-green-100">
          <svg class="h-10 w-10 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
          </svg>
        </div>

        @if ($order->has_status('failed'))
          {{-- Failed Order Message --}}
          <h1 class="mb-3 text-2xl font-bold text-secondary-900 lg:text-3xl">
            {{ __('Unfortunately your order cannot be processed', 'woocommerce') }}
          </h1>
          <p class="mb-6 text-secondary-600">
            {{ __('Please attempt your purchase again.', 'woocommerce') }}
          </p>
          <a
            href="{{ wc_get_checkout_url() }}"
            class="inline-flex items-center gap-2 rounded-xl bg-primary-600 px-6 py-3 font-semibold text-white shadow-lg shadow-primary-600/25 transition-all hover:bg-primary-700 hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
          >
            {{ __('Pay', 'woocommerce') }}
          </a>
        @else
          {{-- Success Message --}}
          <h1 class="mb-3 text-2xl font-bold text-secondary-900 lg:text-3xl">
            {{ __('Thank you. Your order has been received.', 'woocommerce') }}
          </h1>
          <p class="text-secondary-600">
            {{ __('A confirmation email has been sent to', 'sage') }}
            <strong class="text-secondary-900">{{ $order->get_billing_email() }}</strong>
          </p>
        @endif
      </div>

      @php do_action('woocommerce_before_thankyou', $order->get_id()); @endphp

      {{-- Order Overview Cards --}}
      <div class="mb-8 grid grid-cols-2 gap-4 sm:grid-cols-4">
        {{-- Order Number --}}
        <div class="rounded-xl border border-secondary-200 bg-white p-4 text-center">
          <span class="mb-1 block text-xs font-medium uppercase tracking-wide text-secondary-500">
            {{ __('Order number', 'woocommerce') }}
          </span>
          <span class="text-lg font-bold text-secondary-900">
            {{ $order->get_order_number() }}
          </span>
        </div>

        {{-- Date --}}
        <div class="rounded-xl border border-secondary-200 bg-white p-4 text-center">
          <span class="mb-1 block text-xs font-medium uppercase tracking-wide text-secondary-500">
            {{ __('Date', 'woocommerce') }}
          </span>
          <span class="text-lg font-bold text-secondary-900">
            {{ wc_format_datetime($order->get_date_created()) }}
          </span>
        </div>

        {{-- Total --}}
        <div class="rounded-xl border border-secondary-200 bg-white p-4 text-center">
          <span class="mb-1 block text-xs font-medium uppercase tracking-wide text-secondary-500">
            {{ __('Total', 'woocommerce') }}
          </span>
          <span class="text-lg font-bold text-secondary-900">
            {!! $order->get_formatted_order_total() !!}
          </span>
        </div>

        {{-- Payment Method --}}
        @if ($order->get_payment_method_title())
          <div class="rounded-xl border border-secondary-200 bg-white p-4 text-center">
            <span class="mb-1 block text-xs font-medium uppercase tracking-wide text-secondary-500">
              {{ __('Payment method', 'woocommerce') }}
            </span>
            <span class="text-lg font-bold text-secondary-900">
              {{ $order->get_payment_method_title() }}
            </span>
          </div>
        @endif
      </div>

      @php do_action('woocommerce_thankyou_' . $order->get_payment_method(), $order->get_id()); @endphp

      {{-- Main Content Grid --}}
      <div class="grid grid-cols-1 gap-8 lg:grid-cols-3 lg:gap-12">
        {{-- Order Details --}}
        <div class="lg:col-span-2">
          {{-- Order Items --}}
          <div class="rounded-xl border border-secondary-200 bg-white p-6">
            <h2 class="mb-6 flex items-center gap-3 text-lg font-semibold text-secondary-900">
              <span class="flex h-8 w-8 items-center justify-center rounded-full bg-secondary-100 text-sm font-bold text-secondary-600">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
              </span>
              {{ __('Order details', 'woocommerce') }}
            </h2>

            {{-- Order Items Table --}}
            <table class="woocommerce-table woocommerce-table--order-details shop_table order_details w-full">
              <thead class="sr-only">
                <tr>
                  <th class="product-name">{{ __('Product', 'woocommerce') }}</th>
                  <th class="product-total">{{ __('Total', 'woocommerce') }}</th>
                </tr>
              </thead>

              <tbody class="order-items divide-y divide-secondary-100">
                @foreach ($order_items as $item_id => $item)
                  @php
                    $product = $item->get_product();
                    $is_visible = apply_filters('woocommerce_order_item_visible', true, $item);
                    $product_permalink = $product && $product->is_visible() ? $product->get_permalink() : '';
                  @endphp

                  @if ($is_visible)
                    <tr class="woocommerce-table__line-item order_item {{ apply_filters('woocommerce_order_item_class', 'order_item', $item, $order) }}">
                      <td class="woocommerce-table__product-name product-name py-4">
                        <div class="flex items-start gap-4">
                          {{-- Product Thumbnail --}}
                          @if ($product)
                            <div class="h-16 w-16 flex-shrink-0 overflow-hidden rounded-lg bg-secondary-100">
                              @if ($product_permalink)
                                <a href="{{ $product_permalink }}" class="block">
                                  {!! $product->get_image('woocommerce_gallery_thumbnail') !!}
                                </a>
                              @else
                                {!! $product->get_image('woocommerce_gallery_thumbnail') !!}
                              @endif
                            </div>
                          @endif

                          {{-- Product Info --}}
                          <div class="min-w-0 flex-1">
                            @if ($product_permalink)
                              <a href="{{ $product_permalink }}" class="block text-sm font-medium text-secondary-900 hover:text-primary-600">
                                {!! wp_kses_post(apply_filters('woocommerce_order_item_name', $item->get_name(), $item, $is_visible)) !!}
                              </a>
                            @else
                              <span class="block text-sm font-medium text-secondary-900">
                                {!! wp_kses_post(apply_filters('woocommerce_order_item_name', $item->get_name(), $item, $is_visible)) !!}
                              </span>
                            @endif

                            <span class="product-quantity mt-1 block text-xs text-secondary-500">
                              {{ __('Qty:', 'sage') }} {{ $item->get_quantity() }}
                            </span>

                            @php do_action('woocommerce_order_item_meta_start', $item_id, $item, $order, false); @endphp

                            {{-- Item Meta Data --}}
                            <div class="mt-1 text-xs text-secondary-500">
                              {!! wc_display_item_meta($item) !!}
                            </div>

                            @php do_action('woocommerce_order_item_meta_end', $item_id, $item, $order, false); @endphp

                            {{-- Purchase Note --}}
                            @if ($show_purchase_note && $product && $product->get_purchase_note())
                              <div class="mt-2 rounded-lg bg-secondary-50 p-2 text-xs text-secondary-600">
                                {!! wpautop(do_shortcode(wp_kses_post($product->get_purchase_note()))) !!}
                              </div>
                            @endif
                          </div>
                        </div>
                      </td>

                      <td class="woocommerce-table__product-total product-total py-4 text-right align-top">
                        <span class="text-sm font-semibold text-secondary-900">
                          {!! $order->get_formatted_line_subtotal($item) !!}
                        </span>
                      </td>
                    </tr>
                  @endif
                @endforeach
              </tbody>

              <tfoot class="order-totals">
                {{-- Subtotal --}}
                @foreach ($order->get_order_item_totals() as $key => $total)
                  <tr class="{{ $key === 'order_total' ? 'border-t-2 border-secondary-200' : 'border-t border-secondary-100' }}">
                    <th scope="row" class="py-3 text-left text-sm {{ $key === 'order_total' ? 'font-semibold text-secondary-900' : 'font-normal text-secondary-600' }}">
                      {{ $total['label'] }}
                    </th>
                    <td class="py-3 text-right {{ $key === 'order_total' ? 'text-lg font-bold text-secondary-900' : 'text-sm font-medium text-secondary-900' }}">
                      {!! $total['value'] !!}
                    </td>
                  </tr>
                @endforeach
              </tfoot>
            </table>

            {{-- Order Note --}}
            @if ($order->get_customer_note())
              <div class="mt-6 rounded-lg bg-secondary-50 p-4">
                <h4 class="mb-2 text-sm font-semibold text-secondary-900">
                  {{ __('Note:', 'woocommerce') }}
                </h4>
                <p class="text-sm text-secondary-600">
                  {{ wptexturize($order->get_customer_note()) }}
                </p>
              </div>
            @endif
          </div>
        </div>

        {{-- Customer Details Sidebar --}}
        <div class="lg:col-span-1">
          <div class="sticky top-8 space-y-6">
            {{-- Billing Address --}}
            <div class="rounded-xl border border-secondary-200 bg-white p-6">
              <h2 class="mb-4 flex items-center gap-3 text-lg font-semibold text-secondary-900">
                <span class="flex h-8 w-8 items-center justify-center rounded-full bg-secondary-100 text-sm font-bold text-secondary-600">
                  <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                  </svg>
                </span>
                {{ __('Billing address', 'woocommerce') }}
              </h2>

              <address class="not-italic text-sm leading-relaxed text-secondary-600">
                {!! wp_kses_post($order->get_formatted_billing_address(__('N/A', 'woocommerce'))) !!}

                @if ($order->get_billing_phone())
                  <p class="mt-2">
                    <span class="font-medium text-secondary-700">{{ __('Phone:', 'sage') }}</span>
                    {{ $order->get_billing_phone() }}
                  </p>
                @endif

                @if ($order->get_billing_email())
                  <p class="mt-1">
                    <span class="font-medium text-secondary-700">{{ __('Email:', 'sage') }}</span>
                    {{ $order->get_billing_email() }}
                  </p>
                @endif
              </address>
            </div>

            {{-- Shipping Address --}}
            @if ($order->needs_shipping_address() && $order->get_formatted_shipping_address())
              <div class="rounded-xl border border-secondary-200 bg-white p-6">
                <h2 class="mb-4 flex items-center gap-3 text-lg font-semibold text-secondary-900">
                  <span class="flex h-8 w-8 items-center justify-center rounded-full bg-secondary-100 text-sm font-bold text-secondary-600">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                      <path stroke-linecap="round" stroke-linejoin="round" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                    </svg>
                  </span>
                  {{ __('Shipping address', 'woocommerce') }}
                </h2>

                <address class="not-italic text-sm leading-relaxed text-secondary-600">
                  {!! wp_kses_post($order->get_formatted_shipping_address()) !!}

                  @if ($order->get_shipping_phone())
                    <p class="mt-2">
                      <span class="font-medium text-secondary-700">{{ __('Phone:', 'sage') }}</span>
                      {{ $order->get_shipping_phone() }}
                    </p>
                  @endif
                </address>
              </div>
            @endif

            {{-- What's Next Info Box --}}
            <div class="rounded-xl border border-primary-200 bg-primary-50 p-6">
              <h3 class="mb-3 flex items-center gap-2 text-sm font-semibold text-primary-900">
                <svg class="h-5 w-5 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ __("What's next?", 'sage') }}
              </h3>
              <ul class="space-y-2 text-sm text-primary-700">
                <li class="flex items-start gap-2">
                  <svg class="mt-0.5 h-4 w-4 flex-shrink-0 text-primary-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                  </svg>
                  {{ __('You will receive an order confirmation email shortly.', 'sage') }}
                </li>
                <li class="flex items-start gap-2">
                  <svg class="mt-0.5 h-4 w-4 flex-shrink-0 text-primary-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                  </svg>
                  {{ __('We will notify you once your order has shipped.', 'sage') }}
                </li>
                @if (is_user_logged_in())
                  <li class="flex items-start gap-2">
                    <svg class="mt-0.5 h-4 w-4 flex-shrink-0 text-primary-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>
                      {{ __('Track your order in', 'sage') }}
                      <a href="{{ wc_get_account_endpoint_url('orders') }}" class="font-medium underline hover:no-underline">
                        {{ __('My Account', 'sage') }}
                      </a>
                    </span>
                  </li>
                @endif
              </ul>
            </div>

            {{-- Continue Shopping --}}
            <div class="text-center">
              <a
                href="{{ wc_get_page_permalink('shop') }}"
                class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-primary-600 px-6 py-4 text-base font-semibold text-white shadow-lg shadow-primary-600/25 transition-all hover:bg-primary-700 hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 active:scale-[0.98]"
              >
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                </svg>
                {{ __('Continue Shopping', 'sage') }}
              </a>

              @if (is_user_logged_in())
                <a
                  href="{{ wc_get_account_endpoint_url('orders') }}"
                  class="mt-3 inline-flex items-center gap-1 text-sm text-secondary-600 transition-colors hover:text-primary-600"
                >
                  <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                  </svg>
                  {{ __('View all orders', 'sage') }}
                </a>
              @endif
            </div>
          </div>
        </div>
      </div>

      @php do_action('woocommerce_thankyou', $order->get_id()); @endphp

    @else
      {{-- No Order Found --}}
      <div class="flex flex-col items-center justify-center py-16 text-center">
        <div class="mb-6 flex h-24 w-24 items-center justify-center rounded-full bg-secondary-100">
          <svg class="h-12 w-12 text-secondary-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
          </svg>
        </div>

        <h2 class="mb-2 text-xl font-semibold text-secondary-900">
          {{ __('Order not found', 'sage') }}
        </h2>

        <p class="woocommerce-notice woocommerce-notice--success woocommerce-thankyou-order-received mb-8 max-w-sm text-secondary-600">
          {{ apply_filters('woocommerce_thankyou_order_received_text', __('Thank you. Your order has been received.', 'woocommerce'), null) }}
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
    @endif
  </div>
@endsection
