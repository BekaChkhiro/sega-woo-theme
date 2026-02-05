{{--
  Template: My Account Addresses
  Description: Displays billing and shipping addresses on the My Account page
  @see woocommerce/templates/myaccount/my-address.php
  @version 8.7.0
--}}

@php
  // Variables passed by WooCommerce:
  // None - we fetch addresses directly

  $customer_id = get_current_user_id();
  $customer = new WC_Customer($customer_id);

  $get_addresses = apply_filters('woocommerce_my_account_get_addresses', [
    'billing' => __('Billing address', 'sage'),
    'shipping' => __('Shipping address', 'sage'),
  ], $customer_id);
@endphp

{{-- Header --}}
<div class="mb-6">
  <h2 class="text-xl font-semibold text-secondary-900">
    {{ __('Your Addresses', 'sage') }}
  </h2>
  <p class="mt-1 text-sm text-secondary-600">
    {{ __('The following addresses will be used on the checkout page by default.', 'sage') }}
  </p>
</div>

@php do_action('woocommerce_before_my_account_addresses', $get_addresses, $customer_id); @endphp

{{-- Address Cards Grid --}}
<div class="woocommerce-Addresses addresses grid grid-cols-1 gap-6 md:grid-cols-2">
  @foreach ($get_addresses as $name => $title)
    @php
      $address = wc_get_account_formatted_address($name);
      $col_class = ($name === 'billing') ? 'u-column1 col-1' : 'u-column2 col-2';
      $icon_bg = ($name === 'billing') ? 'bg-primary-100' : 'bg-blue-100';
      $icon_color = ($name === 'billing') ? 'text-primary-600' : 'text-blue-600';
    @endphp

    <div class="woocommerce-Address {{ $col_class }} overflow-hidden rounded-xl border border-secondary-200 bg-white transition-all hover:border-secondary-300 hover:shadow-md">
      {{-- Address Header --}}
      <div class="woocommerce-Address-title title flex items-center justify-between border-b border-secondary-200 bg-secondary-50 px-6 py-4">
        <h3 class="flex items-center gap-2 text-base font-semibold text-secondary-900">
          <div class="flex h-8 w-8 items-center justify-center rounded-lg {{ $icon_bg }}">
            @if ($name === 'billing')
              <svg class="h-4 w-4 {{ $icon_color }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
              </svg>
            @else
              <svg class="h-4 w-4 {{ $icon_color }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
              </svg>
            @endif
          </div>
          {{ esc_html($title) }}
        </h3>

        <a
          href="{{ esc_url(wc_get_endpoint_url('edit-address', $name)) }}"
          class="edit inline-flex items-center gap-1 text-sm font-medium text-primary-600 transition-colors hover:text-primary-700"
        >
          <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
          </svg>
          {{ $address ? __('Edit', 'sage') : __('Add', 'sage') }}
        </a>
      </div>

      {{-- Address Content --}}
      <div class="p-6">
        @if ($address)
          <address class="not-italic text-secondary-600">
            {!! wp_kses_post($address) !!}
          </address>
        @else
          {{-- Empty State --}}
          <div class="flex flex-col items-center py-4 text-center">
            <div class="mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-secondary-100">
              <svg class="h-6 w-6 text-secondary-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
            </div>
            <p class="text-sm text-secondary-500">
              {{ __('You have not set up this address yet.', 'sage') }}
            </p>
            <a
              href="{{ esc_url(wc_get_endpoint_url('edit-address', $name)) }}"
              class="mt-3 inline-flex items-center gap-1 text-sm font-medium text-primary-600 transition-colors hover:text-primary-700"
            >
              <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
              </svg>
              {{ __('Add address', 'sage') }}
            </a>
          </div>
        @endif
      </div>
    </div>
  @endforeach
</div>

@php do_action('woocommerce_after_my_account_addresses', $get_addresses, $customer_id); @endphp
