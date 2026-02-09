{{--
  Template: My Account Orders
  Description: Displays the customer's order history with status, date, total, and actions
  @see woocommerce/templates/myaccount/orders.php
  @version 9.5.0
--}}

@php
  // Variables passed by WooCommerce:
  // $has_orders - boolean
  // $customer_orders - object with orders array and pagination info
  // $current_page - int
  // $wp_button_class - string (optional)

  do_action('woocommerce_before_account_orders', $has_orders);

  // Status color mapping
  $status_colors = [
    'pending' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-800', 'dot' => 'bg-yellow-500'],
    'processing' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800', 'dot' => 'bg-blue-500'],
    'on-hold' => ['bg' => 'bg-orange-100', 'text' => 'text-orange-800', 'dot' => 'bg-orange-500'],
    'completed' => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'dot' => 'bg-green-500'],
    'cancelled' => ['bg' => 'bg-red-100', 'text' => 'text-red-800', 'dot' => 'bg-red-500'],
    'refunded' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-800', 'dot' => 'bg-purple-500'],
    'failed' => ['bg' => 'bg-red-100', 'text' => 'text-red-800', 'dot' => 'bg-red-500'],
  ];
@endphp

@if ($has_orders)
  {{-- Header --}}
  <div class="mb-6 flex items-center justify-between">
    <div>
      <h2 class="text-xl font-semibold text-secondary-900">
        {{ __('Your Orders', 'sega-woo-theme') }}
      </h2>
      <p class="mt-1 text-sm text-secondary-600">
        {{ __('View and manage your order history', 'sega-woo-theme') }}
      </p>
    </div>
    <div class="hidden items-center gap-2 text-sm text-secondary-500 sm:flex">
      <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
      </svg>
      <span>{{ count($customer_orders->orders) }} {{ _n('order', 'orders', count($customer_orders->orders), 'sega-woo-theme') }}</span>
    </div>
  </div>

  {{-- Desktop Table View --}}
  <div class="hidden overflow-hidden rounded-xl border border-secondary-200 bg-white md:block">
    <table class="woocommerce-orders-table woocommerce-MyAccount-orders shop_table shop_table_responsive my_account_orders account-orders-table w-full">
      <thead>
        <tr class="border-b border-secondary-200 bg-secondary-50">
          @foreach (wc_get_account_orders_columns() as $column_id => $column_name)
            <th
              scope="col"
              class="woocommerce-orders-table__header woocommerce-orders-table__header-{{ esc_attr($column_id) }} px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-secondary-600"
            >
              <span class="nobr">{{ esc_html($column_name) }}</span>
            </th>
          @endforeach
        </tr>
      </thead>

      <tbody class="divide-y divide-secondary-100">
        @foreach ($customer_orders->orders as $customer_order)
          @php
            $order = wc_get_order($customer_order);
            $item_count = $order->get_item_count() - $order->get_item_count_refunded();
            $status = $order->get_status();
            $colors = $status_colors[$status] ?? ['bg' => 'bg-secondary-100', 'text' => 'text-secondary-800', 'dot' => 'bg-secondary-500'];
          @endphp

          <tr class="woocommerce-orders-table__row woocommerce-orders-table__row--status-{{ esc_attr($status) }} order transition-colors hover:bg-secondary-50">
            @foreach (wc_get_account_orders_columns() as $column_id => $column_name)
              @php $is_order_number = 'order-number' === $column_id; @endphp

              @if ($is_order_number)
                <th
                  class="woocommerce-orders-table__cell woocommerce-orders-table__cell-{{ esc_attr($column_id) }} whitespace-nowrap px-6 py-4"
                  data-title="{{ esc_attr($column_name) }}"
                  scope="row"
                >
                  <a
                    href="{{ esc_url($order->get_view_order_url()) }}"
                    class="font-semibold text-primary-600 transition-colors hover:text-primary-700"
                    aria-label="{{ esc_attr(sprintf(__('View order number %s', 'sega-woo-theme'), $order->get_order_number())) }}"
                  >
                    {{ esc_html(_x('#', 'hash before order number', 'sega-woo-theme') . $order->get_order_number()) }}
                  </a>
                </th>
              @else
                <td
                  class="woocommerce-orders-table__cell woocommerce-orders-table__cell-{{ esc_attr($column_id) }} px-6 py-4"
                  data-title="{{ esc_attr($column_name) }}"
                >
                  @if (has_action('woocommerce_my_account_my_orders_column_' . $column_id))
                    @php do_action('woocommerce_my_account_my_orders_column_' . $column_id, $order); @endphp

                  @elseif ('order-date' === $column_id)
                    <time
                      datetime="{{ esc_attr($order->get_date_created()->date('c')) }}"
                      class="text-sm text-secondary-600"
                    >
                      {{ esc_html(wc_format_datetime($order->get_date_created())) }}
                    </time>

                  @elseif ('order-status' === $column_id)
                    <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-medium {{ $colors['bg'] }} {{ $colors['text'] }}">
                      <span class="h-1.5 w-1.5 rounded-full {{ $colors['dot'] }}"></span>
                      {{ esc_html(wc_get_order_status_name($status)) }}
                    </span>

                  @elseif ('order-total' === $column_id)
                    <div class="text-sm">
                      <span class="font-semibold text-secondary-900">{!! $order->get_formatted_order_total() !!}</span>
                      <span class="text-secondary-500">
                        {{ sprintf(_n('for %s item', 'for %s items', $item_count, 'sega-woo-theme'), $item_count) }}
                      </span>
                    </div>

                  @elseif ('order-actions' === $column_id)
                    @php $actions = wc_get_account_orders_actions($order); @endphp

                    @if (!empty($actions))
                      <div class="flex flex-wrap gap-2">
                        @foreach ($actions as $key => $action)
                          @php
                            $action_aria_label = !empty($action['aria-label'])
                              ? $action['aria-label']
                              : sprintf(__('%1$s order number %2$s', 'sega-woo-theme'), $action['name'], $order->get_order_number());
                          @endphp

                          <a
                            href="{{ esc_url($action['url']) }}"
                            class="woocommerce-button button {{ sanitize_html_class($key) }} inline-flex items-center rounded-lg border border-secondary-300 bg-white px-3 py-1.5 text-xs font-medium text-secondary-700 shadow-sm transition-all hover:bg-secondary-50 hover:text-secondary-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-1"
                            aria-label="{{ esc_attr($action_aria_label) }}"
                          >
                            @if ($key === 'view')
                              <svg class="mr-1 h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                              </svg>
                            @elseif ($key === 'pay')
                              <svg class="mr-1 h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                              </svg>
                            @elseif ($key === 'cancel')
                              <svg class="mr-1 h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                              </svg>
                            @endif
                            {{ esc_html($action['name']) }}
                          </a>
                        @endforeach
                      </div>
                    @endif
                  @endif
                </td>
              @endif
            @endforeach
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  {{-- Mobile Card View --}}
  <div class="space-y-4 md:hidden">
    @foreach ($customer_orders->orders as $customer_order)
      @php
        $order = wc_get_order($customer_order);
        $item_count = $order->get_item_count() - $order->get_item_count_refunded();
        $status = $order->get_status();
        $colors = $status_colors[$status] ?? ['bg' => 'bg-secondary-100', 'text' => 'text-secondary-800', 'dot' => 'bg-secondary-500'];
        $actions = wc_get_account_orders_actions($order);
      @endphp

      <div class="rounded-xl border border-secondary-200 bg-white p-4">
        {{-- Card Header --}}
        <div class="mb-3 flex items-start justify-between">
          <div>
            <a
              href="{{ esc_url($order->get_view_order_url()) }}"
              class="text-base font-semibold text-primary-600"
            >
              {{ __('Order', 'sega-woo-theme') }} {{ esc_html(_x('#', 'hash before order number', 'sega-woo-theme') . $order->get_order_number()) }}
            </a>
            <p class="mt-0.5 text-sm text-secondary-500">
              <time datetime="{{ esc_attr($order->get_date_created()->date('c')) }}">
                {{ esc_html(wc_format_datetime($order->get_date_created())) }}
              </time>
            </p>
          </div>
          <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium {{ $colors['bg'] }} {{ $colors['text'] }}">
            <span class="h-1.5 w-1.5 rounded-full {{ $colors['dot'] }}"></span>
            {{ esc_html(wc_get_order_status_name($status)) }}
          </span>
        </div>

        {{-- Card Body --}}
        <div class="mb-4 flex items-center justify-between border-t border-secondary-100 pt-3">
          <span class="text-sm text-secondary-600">{{ __('Total', 'sega-woo-theme') }}</span>
          <div class="text-right">
            <span class="font-semibold text-secondary-900">{!! $order->get_formatted_order_total() !!}</span>
            <span class="text-sm text-secondary-500">
              ({{ sprintf(_n('%s item', '%s items', $item_count, 'sega-woo-theme'), $item_count) }})
            </span>
          </div>
        </div>

        {{-- Card Actions --}}
        @if (!empty($actions))
          <div class="flex flex-wrap gap-2 border-t border-secondary-100 pt-3">
            @foreach ($actions as $key => $action)
              @php
                $action_aria_label = !empty($action['aria-label'])
                  ? $action['aria-label']
                  : sprintf(__('%1$s order number %2$s', 'sega-woo-theme'), $action['name'], $order->get_order_number());

                $button_class = $key === 'view'
                  ? 'flex-1 justify-center rounded-lg bg-primary-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition-all hover:bg-primary-700'
                  : 'flex-1 justify-center rounded-lg border border-secondary-300 bg-white px-4 py-2 text-sm font-medium text-secondary-700 shadow-sm transition-all hover:bg-secondary-50';
              @endphp

              <a
                href="{{ esc_url($action['url']) }}"
                class="inline-flex items-center {{ $button_class }}"
                aria-label="{{ esc_attr($action_aria_label) }}"
              >
                {{ esc_html($action['name']) }}
              </a>
            @endforeach
          </div>
        @endif
      </div>
    @endforeach
  </div>

  @php do_action('woocommerce_before_account_orders_pagination'); @endphp

  {{-- Pagination --}}
  @if (1 < $customer_orders->max_num_pages)
    <div class="woocommerce-pagination woocommerce-pagination--without-numbers woocommerce-Pagination mt-8 flex items-center justify-between border-t border-secondary-200 pt-6">
      <div class="flex flex-1">
        @if (1 !== $current_page)
          <a
            href="{{ esc_url(wc_get_endpoint_url('orders', $current_page - 1)) }}"
            class="woocommerce-button woocommerce-button--previous woocommerce-Button woocommerce-Button--previous button inline-flex items-center gap-2 rounded-lg border border-secondary-300 bg-white px-4 py-2 text-sm font-medium text-secondary-700 shadow-sm transition-all hover:bg-secondary-50 hover:text-secondary-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
          >
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
            {{ __('Previous', 'sega-woo-theme') }}
          </a>
        @endif
      </div>

      <div class="hidden text-sm text-secondary-600 sm:block">
        {{ sprintf(__('Page %1$d of %2$d', 'sega-woo-theme'), $current_page, $customer_orders->max_num_pages) }}
      </div>

      <div class="flex flex-1 justify-end">
        @if (intval($customer_orders->max_num_pages) !== $current_page)
          <a
            href="{{ esc_url(wc_get_endpoint_url('orders', $current_page + 1)) }}"
            class="woocommerce-button woocommerce-button--next woocommerce-Button woocommerce-Button--next button inline-flex items-center gap-2 rounded-lg border border-secondary-300 bg-white px-4 py-2 text-sm font-medium text-secondary-700 shadow-sm transition-all hover:bg-secondary-50 hover:text-secondary-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
          >
            {{ __('Next', 'sega-woo-theme') }}
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
            </svg>
          </a>
        @endif
      </div>
    </div>
  @endif

@else
  {{-- Empty State --}}
  <div class="flex flex-col items-center justify-center py-12 text-center">
    <div class="mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-secondary-100">
      <svg class="h-10 w-10 text-secondary-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
      </svg>
    </div>
    <h3 class="mb-2 text-lg font-semibold text-secondary-900">
      {{ __('No orders yet', 'sega-woo-theme') }}
    </h3>
    <p class="mb-6 max-w-sm text-secondary-600">
      {{ __("You haven't placed any orders yet. Browse our products and find something you'll love!", 'sega-woo-theme') }}
    </p>
    <a
      href="{{ esc_url(apply_filters('woocommerce_return_to_shop_redirect', wc_get_page_permalink('shop'))) }}"
      class="woocommerce-Button wc-forward button inline-flex items-center gap-2 rounded-xl bg-primary-600 px-6 py-3 text-base font-semibold text-white shadow-lg shadow-primary-600/25 transition-all hover:bg-primary-700 hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 active:scale-[0.98]"
    >
      <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
      </svg>
      {{ __('Browse products', 'sega-woo-theme') }}
    </a>
  </div>
@endif

@php do_action('woocommerce_after_account_orders', $has_orders); @endphp
