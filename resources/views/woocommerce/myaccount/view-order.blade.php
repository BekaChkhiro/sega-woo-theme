{{--
  Template: My Account View Order
  Description: Displays the details of a specific order on the account page
  @see woocommerce/templates/myaccount/view-order.php
  @version 10.1.0
--}}

@php
  // Variables passed by WooCommerce:
  // $order_id - int
  // $order - WC_Order object

  // Get order notes
  $notes = $order->get_customer_order_notes();

  // Get order status and colors
  $status = $order->get_status();
  $status_colors = [
    'pending' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-800', 'dot' => 'bg-yellow-500', 'border' => 'border-yellow-200'],
    'processing' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800', 'dot' => 'bg-blue-500', 'border' => 'border-blue-200'],
    'on-hold' => ['bg' => 'bg-orange-100', 'text' => 'text-orange-800', 'dot' => 'bg-orange-500', 'border' => 'border-orange-200'],
    'completed' => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'dot' => 'bg-green-500', 'border' => 'border-green-200'],
    'cancelled' => ['bg' => 'bg-red-100', 'text' => 'text-red-800', 'dot' => 'bg-red-500', 'border' => 'border-red-200'],
    'refunded' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-800', 'dot' => 'bg-purple-500', 'border' => 'border-purple-200'],
    'failed' => ['bg' => 'bg-red-100', 'text' => 'text-red-800', 'dot' => 'bg-red-500', 'border' => 'border-red-200'],
  ];
  $colors = $status_colors[$status] ?? ['bg' => 'bg-secondary-100', 'text' => 'text-secondary-800', 'dot' => 'bg-secondary-500', 'border' => 'border-secondary-200'];

  // Get order actions
  $actions = wc_get_account_orders_actions($order);

  // Get item count
  $item_count = $order->get_item_count() - $order->get_item_count_refunded();
@endphp

{{-- Back to Orders Link --}}
<div class="mb-6">
  <a
    href="{{ esc_url(wc_get_account_endpoint_url('orders')) }}"
    class="inline-flex items-center gap-2 text-sm font-medium text-secondary-600 transition-colors hover:text-secondary-900"
  >
    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
      <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
    </svg>
    {{ __('Back to Orders', 'sage') }}
  </a>
</div>

{{-- Order Header --}}
<div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
  <div>
    <div class="flex items-center gap-3">
      <h2 class="text-xl font-semibold text-secondary-900">
        {{ __('Order', 'sage') }} {{ esc_html(_x('#', 'hash before order number', 'sage') . $order->get_order_number()) }}
      </h2>
      <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-medium {{ $colors['bg'] }} {{ $colors['text'] }}">
        <span class="h-1.5 w-1.5 rounded-full {{ $colors['dot'] }}"></span>
        {{ esc_html(wc_get_order_status_name($status)) }}
      </span>
    </div>
    <p class="mt-2 text-sm text-secondary-600">
      {!! wp_kses_post(
        sprintf(
          __('Placed on %1$s at %2$s', 'sage'),
          '<time datetime="' . esc_attr($order->get_date_created()->date('c')) . '">' . esc_html(wc_format_datetime($order->get_date_created(), wc_date_format())) . '</time>',
          esc_html(wc_format_datetime($order->get_date_created(), wc_time_format()))
        )
      ) !!}
    </p>
  </div>

  {{-- Order Actions --}}
  @if (!empty($actions))
    <div class="flex flex-wrap gap-2">
      @foreach ($actions as $key => $action)
        @php
          $is_primary = in_array($key, ['pay', 'view']);
          $button_class = $is_primary
            ? 'inline-flex items-center gap-2 rounded-lg bg-primary-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition-all hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2'
            : 'inline-flex items-center gap-2 rounded-lg border border-secondary-300 bg-white px-4 py-2 text-sm font-medium text-secondary-700 shadow-sm transition-all hover:bg-secondary-50 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2';
        @endphp

        <a href="{{ esc_url($action['url']) }}" class="{{ $button_class }}">
          @if ($key === 'pay')
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
            </svg>
          @elseif ($key === 'cancel')
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
          @endif
          {{ esc_html($action['name']) }}
        </a>
      @endforeach
    </div>
  @endif
</div>

{{-- Order Summary Cards --}}
<div class="mb-8 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
  {{-- Order Number --}}
  <div class="rounded-xl border border-secondary-200 bg-white p-4">
    <div class="flex items-center gap-3">
      <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary-100">
        <svg class="h-5 w-5 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
        </svg>
      </div>
      <div>
        <p class="text-xs font-medium uppercase tracking-wider text-secondary-500">{{ __('Order Number', 'sage') }}</p>
        <p class="font-semibold text-secondary-900">#{{ esc_html($order->get_order_number()) }}</p>
      </div>
    </div>
  </div>

  {{-- Date --}}
  <div class="rounded-xl border border-secondary-200 bg-white p-4">
    <div class="flex items-center gap-3">
      <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-100">
        <svg class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
        </svg>
      </div>
      <div>
        <p class="text-xs font-medium uppercase tracking-wider text-secondary-500">{{ __('Date', 'sage') }}</p>
        <p class="font-semibold text-secondary-900">{{ esc_html(wc_format_datetime($order->get_date_created())) }}</p>
      </div>
    </div>
  </div>

  {{-- Total --}}
  <div class="rounded-xl border border-secondary-200 bg-white p-4">
    <div class="flex items-center gap-3">
      <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-green-100">
        <svg class="h-5 w-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
      </div>
      <div>
        <p class="text-xs font-medium uppercase tracking-wider text-secondary-500">{{ __('Total', 'sage') }}</p>
        <p class="font-semibold text-secondary-900">{!! $order->get_formatted_order_total() !!}</p>
      </div>
    </div>
  </div>

  {{-- Payment Method --}}
  <div class="rounded-xl border border-secondary-200 bg-white p-4">
    <div class="flex items-center gap-3">
      <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-purple-100">
        <svg class="h-5 w-5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
        </svg>
      </div>
      <div>
        <p class="text-xs font-medium uppercase tracking-wider text-secondary-500">{{ __('Payment', 'sage') }}</p>
        <p class="font-semibold text-secondary-900">{{ esc_html($order->get_payment_method_title() ?: __('N/A', 'sage')) }}</p>
      </div>
    </div>
  </div>
</div>

{{-- Order Items --}}
<div class="mb-8 overflow-hidden rounded-xl border border-secondary-200 bg-white">
  <div class="border-b border-secondary-200 bg-secondary-50 px-6 py-4">
    <h3 class="flex items-center gap-2 text-base font-semibold text-secondary-900">
      <svg class="h-5 w-5 text-secondary-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
      </svg>
      {{ __('Order Items', 'sage') }}
      <span class="ml-auto rounded-full bg-secondary-200 px-2.5 py-0.5 text-xs font-medium text-secondary-700">
        {{ sprintf(_n('%s item', '%s items', $item_count, 'sage'), $item_count) }}
      </span>
    </h3>
  </div>

  {{-- Desktop Table --}}
  <div class="hidden md:block">
    <table class="w-full">
      <thead class="border-b border-secondary-100 bg-secondary-50/50">
        <tr>
          <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-secondary-600">
            {{ __('Product', 'sage') }}
          </th>
          <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-secondary-600">
            {{ __('Quantity', 'sage') }}
          </th>
          <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-secondary-600">
            {{ __('Total', 'sage') }}
          </th>
        </tr>
      </thead>
      <tbody class="divide-y divide-secondary-100">
        @foreach ($order->get_items('line_item') as $item_id => $item)
          @php
            $product = $item->get_product();
            $is_visible = $product && $product->is_visible();
            $product_permalink = $is_visible ? $product->get_permalink($item) : '';
            $thumbnail = $product ? $product->get_image(['64', '64'], ['class' => 'rounded-lg']) : wc_placeholder_img(64);
          @endphp

          <tr class="transition-colors hover:bg-secondary-50">
            <td class="px-6 py-4">
              <div class="flex items-center gap-4">
                {{-- Product Image --}}
                <div class="h-16 w-16 flex-shrink-0 overflow-hidden rounded-lg border border-secondary-200 bg-secondary-100">
                  @if ($product_permalink)
                    <a href="{{ esc_url($product_permalink) }}" class="block">
                      {!! $thumbnail !!}
                    </a>
                  @else
                    {!! $thumbnail !!}
                  @endif
                </div>

                {{-- Product Info --}}
                <div class="min-w-0 flex-1">
                  @if ($product_permalink)
                    <a href="{{ esc_url($product_permalink) }}" class="font-medium text-secondary-900 hover:text-primary-600">
                      {{ esc_html($item->get_name()) }}
                    </a>
                  @else
                    <span class="font-medium text-secondary-900">{{ esc_html($item->get_name()) }}</span>
                  @endif

                  {{-- Product Meta (variations, etc.) --}}
                  @php
                    $item_meta = strip_tags(wc_display_item_meta($item, ['echo' => false, 'separator' => ', ']));
                  @endphp
                  @if ($item_meta)
                    <p class="mt-1 text-sm text-secondary-500">{{ $item_meta }}</p>
                  @endif

                  {{-- SKU --}}
                  @if ($product && $product->get_sku())
                    <p class="mt-1 text-xs text-secondary-400">
                      {{ __('SKU:', 'sage') }} {{ esc_html($product->get_sku()) }}
                    </p>
                  @endif
                </div>
              </div>
            </td>
            <td class="px-6 py-4 text-center">
              <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-secondary-100 text-sm font-medium text-secondary-700">
                {{ esc_html($item->get_quantity()) }}
              </span>
            </td>
            <td class="px-6 py-4 text-right">
              <span class="font-semibold text-secondary-900">{!! $order->get_formatted_line_subtotal($item) !!}</span>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  {{-- Mobile Cards --}}
  <div class="divide-y divide-secondary-100 md:hidden">
    @foreach ($order->get_items('line_item') as $item_id => $item)
      @php
        $product = $item->get_product();
        $is_visible = $product && $product->is_visible();
        $product_permalink = $is_visible ? $product->get_permalink($item) : '';
        $thumbnail = $product ? $product->get_image(['48', '48'], ['class' => 'rounded-lg']) : wc_placeholder_img(48);
      @endphp

      <div class="flex gap-4 p-4">
        {{-- Product Image --}}
        <div class="h-12 w-12 flex-shrink-0 overflow-hidden rounded-lg border border-secondary-200 bg-secondary-100">
          @if ($product_permalink)
            <a href="{{ esc_url($product_permalink) }}">
              {!! $thumbnail !!}
            </a>
          @else
            {!! $thumbnail !!}
          @endif
        </div>

        {{-- Product Info --}}
        <div class="min-w-0 flex-1">
          @if ($product_permalink)
            <a href="{{ esc_url($product_permalink) }}" class="font-medium text-secondary-900 hover:text-primary-600">
              {{ esc_html($item->get_name()) }}
            </a>
          @else
            <span class="font-medium text-secondary-900">{{ esc_html($item->get_name()) }}</span>
          @endif

          <div class="mt-1 flex items-center justify-between">
            <span class="text-sm text-secondary-500">
              {{ __('Qty:', 'sage') }} {{ esc_html($item->get_quantity()) }}
            </span>
            <span class="font-semibold text-secondary-900">{!! $order->get_formatted_line_subtotal($item) !!}</span>
          </div>
        </div>
      </div>
    @endforeach
  </div>

  {{-- Order Totals --}}
  <div class="border-t border-secondary-200 bg-secondary-50/50">
    <div class="divide-y divide-secondary-100">
      @foreach ($order->get_order_item_totals() as $key => $total)
        <div class="flex items-center justify-between px-6 py-3 {{ $key === 'order_total' ? 'bg-secondary-100' : '' }}">
          <span class="text-sm {{ $key === 'order_total' ? 'font-semibold text-secondary-900' : 'text-secondary-600' }}">
            {{ esc_html($total['label']) }}
          </span>
          <span class="{{ $key === 'order_total' ? 'text-lg font-bold text-secondary-900' : 'font-medium text-secondary-900' }}">
            {!! wp_kses_post($total['value']) !!}
          </span>
        </div>
      @endforeach
    </div>
  </div>
</div>

{{-- Addresses --}}
<div class="mb-8 grid grid-cols-1 gap-6 lg:grid-cols-2">
  {{-- Billing Address --}}
  <div class="rounded-xl border border-secondary-200 bg-white">
    <div class="border-b border-secondary-200 bg-secondary-50 px-6 py-4">
      <h3 class="flex items-center gap-2 text-base font-semibold text-secondary-900">
        <svg class="h-5 w-5 text-secondary-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
        </svg>
        {{ __('Billing Address', 'sage') }}
      </h3>
    </div>
    <div class="p-6">
      @if ($order->get_formatted_billing_address())
        <address class="not-italic text-secondary-600">
          {!! wp_kses_post($order->get_formatted_billing_address()) !!}
        </address>

        @if ($order->get_billing_phone())
          <p class="mt-4 flex items-center gap-2 text-sm text-secondary-600">
            <svg class="h-4 w-4 text-secondary-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
            </svg>
            {{ esc_html($order->get_billing_phone()) }}
          </p>
        @endif

        @if ($order->get_billing_email())
          <p class="mt-2 flex items-center gap-2 text-sm text-secondary-600">
            <svg class="h-4 w-4 text-secondary-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
            {{ esc_html($order->get_billing_email()) }}
          </p>
        @endif
      @else
        <p class="text-secondary-500">{{ __('No billing address provided.', 'sage') }}</p>
      @endif
    </div>
  </div>

  {{-- Shipping Address --}}
  <div class="rounded-xl border border-secondary-200 bg-white">
    <div class="border-b border-secondary-200 bg-secondary-50 px-6 py-4">
      <h3 class="flex items-center gap-2 text-base font-semibold text-secondary-900">
        <svg class="h-5 w-5 text-secondary-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
          <path stroke-linecap="round" stroke-linejoin="round" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
        </svg>
        {{ __('Shipping Address', 'sage') }}
      </h3>
    </div>
    <div class="p-6">
      @if ($order->get_formatted_shipping_address())
        <address class="not-italic text-secondary-600">
          {!! wp_kses_post($order->get_formatted_shipping_address()) !!}
        </address>

        @if ($order->get_shipping_phone())
          <p class="mt-4 flex items-center gap-2 text-sm text-secondary-600">
            <svg class="h-4 w-4 text-secondary-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
            </svg>
            {{ esc_html($order->get_shipping_phone()) }}
          </p>
        @endif
      @else
        <p class="text-secondary-500">{{ __('No shipping address provided.', 'sage') }}</p>
      @endif
    </div>
  </div>
</div>

{{-- Order Notes --}}
@if ($notes)
  <div class="mb-8 rounded-xl border border-secondary-200 bg-white">
    <div class="border-b border-secondary-200 bg-secondary-50 px-6 py-4">
      <h3 class="flex items-center gap-2 text-base font-semibold text-secondary-900">
        <svg class="h-5 w-5 text-secondary-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
        </svg>
        {{ __('Order Updates', 'sage') }}
        <span class="ml-auto rounded-full bg-secondary-200 px-2.5 py-0.5 text-xs font-medium text-secondary-700">
          {{ count($notes) }}
        </span>
      </h3>
    </div>
    <ol class="woocommerce-OrderUpdates divide-y divide-secondary-100">
      @foreach ($notes as $note)
        <li class="woocommerce-OrderUpdate p-6">
          <div class="flex gap-4">
            <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-blue-100">
              <svg class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
              </svg>
            </div>
            <div class="min-w-0 flex-1">
              <p class="text-xs font-medium text-secondary-500">
                <time datetime="{{ esc_attr($note->comment_date) }}">
                  {{ esc_html(date_i18n(__('F j, Y \a\t g:i a', 'sage'), strtotime($note->comment_date))) }}
                </time>
              </p>
              <div class="woocommerce-OrderUpdate-description mt-2 text-sm text-secondary-700">
                {!! wpautop(wptexturize($note->comment_content)) !!}
              </div>
            </div>
          </div>
        </li>
      @endforeach
    </ol>
  </div>
@endif

{{-- WooCommerce Hook: woocommerce_view_order --}}
{{-- This hook loads order details, shipping info, etc. --}}
@php do_action('woocommerce_view_order', $order_id); @endphp
