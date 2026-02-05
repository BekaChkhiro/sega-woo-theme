{{--
  Template: My Account Dashboard
  Description: Custom dashboard with quick stats and recent activity
  @see woocommerce/templates/myaccount/dashboard.php
  @version 4.4.0
--}}

@php
  // Variables passed by WooCommerce:
  // $current_user - WP_User object

  // Get customer data
  $customer_id = $current_user->ID;
  $customer = new WC_Customer($customer_id);

  // Get order statistics
  $orders_count = wc_get_customer_order_count($customer_id);
  $total_spent = wc_get_customer_total_spent($customer_id);

  // Get recent orders (last 5)
  $recent_orders = wc_get_orders([
    'customer_id' => $customer_id,
    'limit' => 5,
    'orderby' => 'date',
    'order' => 'DESC',
  ]);

  // Count orders by status
  $processing_orders = wc_get_orders([
    'customer_id' => $customer_id,
    'status' => ['processing', 'on-hold', 'pending'],
    'return' => 'ids',
  ]);

  // Get downloads count
  $downloads = wc_get_customer_available_downloads($customer_id);
  $downloads_count = count($downloads);

  // Get saved payment methods count
  $payment_tokens = WC_Payment_Tokens::get_customer_tokens($customer_id);
  $payment_methods_count = count($payment_tokens);

  // Get member since date
  $user_registered = $current_user->user_registered;
  $member_since = date_i18n(get_option('date_format'), strtotime($user_registered));
  $days_as_member = floor((time() - strtotime($user_registered)) / DAY_IN_SECONDS);

  // Status colors
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

{{-- Welcome Section --}}
<div class="mb-8">
  <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
    <div>
      <h2 class="text-xl font-semibold text-secondary-900">
        {{ sprintf(__('Hello, %s!', 'sage'), esc_html($current_user->display_name)) }}
      </h2>
      <p class="mt-2 text-secondary-600">
        {!! sprintf(
          __('From your account dashboard you can view your <a href="%1$s" class="font-medium text-primary-600 hover:text-primary-700">recent orders</a>, manage your <a href="%2$s" class="font-medium text-primary-600 hover:text-primary-700">shipping and billing addresses</a>, and <a href="%3$s" class="font-medium text-primary-600 hover:text-primary-700">edit your password and account details</a>.', 'sage'),
          esc_url(wc_get_endpoint_url('orders')),
          esc_url(wc_get_endpoint_url('edit-address')),
          esc_url(wc_get_endpoint_url('edit-account'))
        ) !!}
      </p>
    </div>
    {{-- Member Since Badge --}}
    <div class="flex-shrink-0 rounded-lg bg-gradient-to-r from-primary-50 to-primary-100 px-4 py-2">
      <div class="flex items-center gap-2">
        <svg class="h-5 w-5 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
        </svg>
        <div>
          <span class="text-xs font-medium uppercase tracking-wider text-primary-600">{{ __('Member since', 'sage') }}</span>
          <p class="text-sm font-semibold text-primary-900">{{ $member_since }}</p>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- Quick Stats --}}
<div class="account-dashboard-stats mb-8 grid grid-cols-2 gap-4 md:grid-cols-4">
  {{-- Total Orders --}}
  <div class="account-stat-card rounded-xl border border-secondary-200 bg-white p-5 transition-all hover:border-primary-300 hover:shadow-md">
    <div class="account-stat-icon mb-3 flex h-10 w-10 items-center justify-center rounded-lg bg-primary-100">
      <svg class="h-5 w-5 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
      </svg>
    </div>
    <div class="account-stat-value text-2xl font-bold text-secondary-900">{{ $orders_count }}</div>
    <div class="account-stat-label mt-1 text-xs font-medium uppercase tracking-wider text-secondary-500">
      {{ __('Total Orders', 'sage') }}
    </div>
  </div>

  {{-- Active Orders --}}
  <div class="account-stat-card rounded-xl border border-secondary-200 bg-white p-5 transition-all hover:border-blue-300 hover:shadow-md">
    <div class="account-stat-icon mb-3 flex h-10 w-10 items-center justify-center rounded-lg bg-blue-100">
      <svg class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
      </svg>
    </div>
    <div class="account-stat-value text-2xl font-bold text-secondary-900">{{ count($processing_orders) }}</div>
    <div class="account-stat-label mt-1 text-xs font-medium uppercase tracking-wider text-secondary-500">
      {{ __('Active Orders', 'sage') }}
    </div>
  </div>

  {{-- Total Spent --}}
  <div class="account-stat-card rounded-xl border border-secondary-200 bg-white p-5 transition-all hover:border-green-300 hover:shadow-md">
    <div class="account-stat-icon mb-3 flex h-10 w-10 items-center justify-center rounded-lg bg-green-100">
      <svg class="h-5 w-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
      </svg>
    </div>
    <div class="account-stat-value text-2xl font-bold text-secondary-900">{!! wc_price($total_spent) !!}</div>
    <div class="account-stat-label mt-1 text-xs font-medium uppercase tracking-wider text-secondary-500">
      {{ __('Total Spent', 'sage') }}
    </div>
  </div>

  {{-- Downloads --}}
  <div class="account-stat-card rounded-xl border border-secondary-200 bg-white p-5 transition-all hover:border-purple-300 hover:shadow-md">
    <div class="account-stat-icon mb-3 flex h-10 w-10 items-center justify-center rounded-lg bg-purple-100">
      <svg class="h-5 w-5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
      </svg>
    </div>
    <div class="account-stat-value text-2xl font-bold text-secondary-900">{{ $downloads_count }}</div>
    <div class="account-stat-label mt-1 text-xs font-medium uppercase tracking-wider text-secondary-500">
      {{ __('Downloads', 'sage') }}
    </div>
  </div>
</div>

{{-- Recent Orders --}}
@if (!empty($recent_orders))
  <div class="account-recent-orders mb-8 overflow-hidden rounded-xl border border-secondary-200 bg-white">
    <div class="account-recent-orders-header flex items-center justify-between border-b border-secondary-200 bg-secondary-50 px-6 py-4">
      <h3 class="flex items-center gap-2 text-base font-semibold text-secondary-900">
        <svg class="h-5 w-5 text-secondary-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
        </svg>
        {{ __('Recent Orders', 'sage') }}
      </h3>
      <a
        href="{{ esc_url(wc_get_endpoint_url('orders')) }}"
        class="text-sm font-medium text-primary-600 transition-colors hover:text-primary-700"
      >
        {{ __('View All', 'sage') }}
      </a>
    </div>

    {{-- Desktop Table --}}
    <div class="hidden md:block">
      <table class="w-full">
        <thead class="border-b border-secondary-100 bg-secondary-50/50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-secondary-600">
              {{ __('Order', 'sage') }}
            </th>
            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-secondary-600">
              {{ __('Date', 'sage') }}
            </th>
            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-secondary-600">
              {{ __('Status', 'sage') }}
            </th>
            <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-secondary-600">
              {{ __('Total', 'sage') }}
            </th>
          </tr>
        </thead>
        <tbody class="divide-y divide-secondary-100">
          @foreach ($recent_orders as $order)
            @php
              $status = $order->get_status();
              $colors = $status_colors[$status] ?? ['bg' => 'bg-secondary-100', 'text' => 'text-secondary-800', 'dot' => 'bg-secondary-500'];
            @endphp
            <tr class="transition-colors hover:bg-secondary-50">
              <td class="whitespace-nowrap px-6 py-4">
                <a
                  href="{{ esc_url($order->get_view_order_url()) }}"
                  class="font-semibold text-primary-600 transition-colors hover:text-primary-700"
                >
                  #{{ esc_html($order->get_order_number()) }}
                </a>
              </td>
              <td class="px-6 py-4 text-sm text-secondary-600">
                <time datetime="{{ esc_attr($order->get_date_created()->date('c')) }}">
                  {{ esc_html(wc_format_datetime($order->get_date_created())) }}
                </time>
              </td>
              <td class="px-6 py-4">
                <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-medium {{ $colors['bg'] }} {{ $colors['text'] }}">
                  <span class="h-1.5 w-1.5 rounded-full {{ $colors['dot'] }}"></span>
                  {{ esc_html(wc_get_order_status_name($status)) }}
                </span>
              </td>
              <td class="whitespace-nowrap px-6 py-4 text-right font-semibold text-secondary-900">
                {!! $order->get_formatted_order_total() !!}
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    {{-- Mobile Cards --}}
    <div class="divide-y divide-secondary-100 md:hidden">
      @foreach ($recent_orders as $order)
        @php
          $status = $order->get_status();
          $colors = $status_colors[$status] ?? ['bg' => 'bg-secondary-100', 'text' => 'text-secondary-800', 'dot' => 'bg-secondary-500'];
        @endphp
        <a href="{{ esc_url($order->get_view_order_url()) }}" class="block p-4 transition-colors hover:bg-secondary-50">
          <div class="flex items-start justify-between">
            <div>
              <span class="font-semibold text-secondary-900">#{{ esc_html($order->get_order_number()) }}</span>
              <p class="mt-1 text-sm text-secondary-500">
                {{ esc_html(wc_format_datetime($order->get_date_created())) }}
              </p>
            </div>
            <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium {{ $colors['bg'] }} {{ $colors['text'] }}">
              <span class="h-1.5 w-1.5 rounded-full {{ $colors['dot'] }}"></span>
              {{ esc_html(wc_get_order_status_name($status)) }}
            </span>
          </div>
          <div class="mt-2 flex items-center justify-between">
            <span class="text-sm text-secondary-600">{{ __('Total:', 'sage') }}</span>
            <span class="font-semibold text-secondary-900">{!! $order->get_formatted_order_total() !!}</span>
          </div>
        </a>
      @endforeach
    </div>
  </div>
@else
  {{-- No Orders Yet --}}
  <div class="mb-8 rounded-xl border border-secondary-200 bg-white p-8 text-center">
    <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-secondary-100">
      <svg class="h-8 w-8 text-secondary-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
        <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
      </svg>
    </div>
    <h3 class="mb-2 text-lg font-semibold text-secondary-900">{{ __('No orders yet', 'sage') }}</h3>
    <p class="mb-6 text-secondary-600">{{ __('Start shopping to see your orders here.', 'sage') }}</p>
    <a
      href="{{ esc_url(wc_get_page_permalink('shop')) }}"
      class="inline-flex items-center gap-2 rounded-xl bg-primary-600 px-6 py-3 text-base font-semibold text-white shadow-lg shadow-primary-600/25 transition-all hover:bg-primary-700 hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
    >
      <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
      </svg>
      {{ __('Start Shopping', 'sage') }}
    </a>
  </div>
@endif

{{-- Address Cards --}}
@php
  $billing_address = $customer->get_billing();
  $shipping_address = $customer->get_shipping();
  $has_billing = !empty($billing_address['address_1']);
  $has_shipping = !empty($shipping_address['address_1']);
@endphp

@if ($has_billing || $has_shipping)
  <div class="mb-8 grid grid-cols-1 gap-4 md:grid-cols-2">
    {{-- Billing Address --}}
    <div class="account-address-card rounded-xl border border-secondary-200 bg-white p-5">
      <div class="mb-4 flex items-center justify-between">
        <h3 class="flex items-center gap-2 text-sm font-semibold uppercase tracking-wider text-secondary-500">
          <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
          </svg>
          {{ __('Billing Address', 'sage') }}
        </h3>
        <a
          href="{{ esc_url(wc_get_endpoint_url('edit-address', 'billing')) }}"
          class="text-xs font-medium text-primary-600 transition-colors hover:text-primary-700"
        >
          {{ __('Edit', 'sage') }}
        </a>
      </div>
      @if ($has_billing)
        <address class="text-sm not-italic text-secondary-600">
          {!! wc_get_account_formatted_address('billing') !!}
        </address>
      @else
        <p class="text-sm text-secondary-400">{{ __('No billing address set.', 'sage') }}</p>
      @endif
    </div>

    {{-- Shipping Address --}}
    <div class="account-address-card rounded-xl border border-secondary-200 bg-white p-5">
      <div class="mb-4 flex items-center justify-between">
        <h3 class="flex items-center gap-2 text-sm font-semibold uppercase tracking-wider text-secondary-500">
          <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
          </svg>
          {{ __('Shipping Address', 'sage') }}
        </h3>
        <a
          href="{{ esc_url(wc_get_endpoint_url('edit-address', 'shipping')) }}"
          class="text-xs font-medium text-primary-600 transition-colors hover:text-primary-700"
        >
          {{ __('Edit', 'sage') }}
        </a>
      </div>
      @if ($has_shipping)
        <address class="text-sm not-italic text-secondary-600">
          {!! wc_get_account_formatted_address('shipping') !!}
        </address>
      @else
        <p class="text-sm text-secondary-400">{{ __('No shipping address set.', 'sage') }}</p>
      @endif
    </div>
  </div>
@else
  {{-- No Addresses Set - Prompt to Add --}}
  <div class="mb-8 rounded-xl border border-dashed border-secondary-300 bg-secondary-50 p-6 text-center">
    <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-secondary-100">
      <svg class="h-6 w-6 text-secondary-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
        <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
        <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
      </svg>
    </div>
    <h3 class="mb-1 font-semibold text-secondary-900">{{ __('Complete your profile', 'sage') }}</h3>
    <p class="mb-4 text-sm text-secondary-600">{{ __('Add your addresses for faster checkout.', 'sage') }}</p>
    <a
      href="{{ esc_url(wc_get_endpoint_url('edit-address')) }}"
      class="inline-flex items-center gap-2 rounded-lg bg-primary-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-primary-700"
    >
      <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
      </svg>
      {{ __('Add Address', 'sage') }}
    </a>
  </div>
@endif

{{-- Quick Links --}}
<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
  {{-- Edit Account --}}
  <a
    href="{{ esc_url(wc_get_endpoint_url('edit-account')) }}"
    class="group flex items-center gap-4 rounded-xl border border-secondary-200 bg-white p-4 transition-all hover:border-primary-300 hover:shadow-md"
  >
    <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-secondary-100 transition-colors group-hover:bg-primary-100">
      <svg class="h-6 w-6 text-secondary-500 transition-colors group-hover:text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
      </svg>
    </div>
    <div>
      <h4 class="font-semibold text-secondary-900">{{ __('Account Details', 'sage') }}</h4>
      <p class="text-sm text-secondary-500">{{ __('Edit your profile and password', 'sage') }}</p>
    </div>
  </a>

  {{-- Addresses --}}
  <a
    href="{{ esc_url(wc_get_endpoint_url('edit-address')) }}"
    class="group flex items-center gap-4 rounded-xl border border-secondary-200 bg-white p-4 transition-all hover:border-primary-300 hover:shadow-md"
  >
    <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-secondary-100 transition-colors group-hover:bg-primary-100">
      <svg class="h-6 w-6 text-secondary-500 transition-colors group-hover:text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
        <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
      </svg>
    </div>
    <div>
      <h4 class="font-semibold text-secondary-900">{{ __('Addresses', 'sage') }}</h4>
      <p class="text-sm text-secondary-500">{{ __('Manage billing & shipping', 'sage') }}</p>
    </div>
  </a>

  {{-- Downloads (if any) --}}
  @if ($downloads_count > 0)
    <a
      href="{{ esc_url(wc_get_endpoint_url('downloads')) }}"
      class="group flex items-center gap-4 rounded-xl border border-secondary-200 bg-white p-4 transition-all hover:border-primary-300 hover:shadow-md"
    >
      <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-secondary-100 transition-colors group-hover:bg-primary-100">
        <svg class="h-6 w-6 text-secondary-500 transition-colors group-hover:text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
        </svg>
      </div>
      <div>
        <h4 class="font-semibold text-secondary-900">{{ __('Downloads', 'sage') }}</h4>
        <p class="text-sm text-secondary-500">{{ sprintf(_n('%s file available', '%s files available', $downloads_count, 'sage'), $downloads_count) }}</p>
      </div>
    </a>
  @endif

  {{-- Payment Methods --}}
  <a
    href="{{ esc_url(wc_get_endpoint_url('payment-methods')) }}"
    class="group flex items-center gap-4 rounded-xl border border-secondary-200 bg-white p-4 transition-all hover:border-primary-300 hover:shadow-md"
  >
    <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-secondary-100 transition-colors group-hover:bg-primary-100">
      <svg class="h-6 w-6 text-secondary-500 transition-colors group-hover:text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
      </svg>
    </div>
    <div>
      <h4 class="font-semibold text-secondary-900">{{ __('Payment Methods', 'sage') }}</h4>
      <p class="text-sm text-secondary-500">
        @if ($payment_methods_count > 0)
          {{ sprintf(_n('%s saved method', '%s saved methods', $payment_methods_count, 'sage'), $payment_methods_count) }}
        @else
          {{ __('Add a payment method', 'sage') }}
        @endif
      </p>
    </div>
  </a>

  {{-- Browse Shop --}}
  <a
    href="{{ esc_url(wc_get_page_permalink('shop')) }}"
    class="group flex items-center gap-4 rounded-xl border border-secondary-200 bg-white p-4 transition-all hover:border-primary-300 hover:shadow-md"
  >
    <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-secondary-100 transition-colors group-hover:bg-primary-100">
      <svg class="h-6 w-6 text-secondary-500 transition-colors group-hover:text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
      </svg>
    </div>
    <div>
      <h4 class="font-semibold text-secondary-900">{{ __('Browse Shop', 'sage') }}</h4>
      <p class="text-sm text-secondary-500">{{ __('Continue shopping', 'sage') }}</p>
    </div>
  </a>
</div>

@php do_action('woocommerce_account_dashboard'); @endphp
