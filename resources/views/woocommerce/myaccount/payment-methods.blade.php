{{--
  Template: My Account Payment Methods
  Description: Displays saved payment methods
  @see woocommerce/templates/myaccount/payment-methods.php
  @version 8.9.0
--}}

@php
  // Variables passed by WooCommerce:
  // $saved_methods - array of saved payment methods

  $has_methods = !empty($saved_methods);
@endphp

{{-- Header --}}
<div class="mb-6">
  <h2 class="text-xl font-semibold text-secondary-900">
    {{ __('Payment Methods', 'sega-woo-theme') }}
  </h2>
  <p class="mt-1 text-sm text-secondary-600">
    {{ __('Manage your saved payment methods for faster checkout.', 'sega-woo-theme') }}
  </p>
</div>

@php do_action('woocommerce_before_account_payment_methods', $has_methods); @endphp

@if ($has_methods)
  {{-- Desktop Table View --}}
  <div class="hidden overflow-hidden rounded-xl border border-secondary-200 bg-white md:block">
    <table class="woocommerce-PaymentMethods woocommerce-MyAccount-paymentMethods shop_table shop_table_responsive account-payment-methods-table w-full">
      <thead>
        <tr class="border-b border-secondary-200 bg-secondary-50">
          @foreach (wc_get_account_payment_methods_columns() as $column_id => $column_name)
            <th
              scope="col"
              class="woocommerce-PaymentMethod woocommerce-PaymentMethod--{{ esc_attr($column_id) }} payment-method-{{ esc_attr($column_id) }} px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-secondary-600 {{ $column_id === 'method-actions' ? 'text-right' : '' }}"
            >
              <span class="nobr">{{ esc_html($column_name) }}</span>
            </th>
          @endforeach
        </tr>
      </thead>

      <tbody class="divide-y divide-secondary-100">
        @foreach ($saved_methods as $type => $methods)
          @foreach ($methods as $method)
            <tr class="woocommerce-PaymentMethod transition-colors hover:bg-secondary-50">
              @foreach (wc_get_account_payment_methods_columns() as $column_id => $column_name)
                <td
                  class="woocommerce-PaymentMethod woocommerce-PaymentMethod--{{ esc_attr($column_id) }} payment-method-{{ esc_attr($column_id) }} px-6 py-4 {{ $column_id === 'method-actions' ? 'text-right' : '' }}"
                  data-title="{{ esc_attr($column_name) }}"
                >
                  @if (has_action('woocommerce_account_payment_methods_column_' . $column_id))
                    @php do_action('woocommerce_account_payment_methods_column_' . $column_id, $method); @endphp

                  @elseif ('method-title' === $column_id)
                    <div class="flex items-center gap-3">
                      {{-- Payment Method Icon --}}
                      <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-secondary-100">
                        @if (isset($method['method']['brand']))
                          @php $brand = strtolower($method['method']['brand']); @endphp
                          @if (in_array($brand, ['visa', 'mastercard', 'amex', 'discover']))
                            <svg class="h-6 w-6 text-secondary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                              <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                          @else
                            <svg class="h-6 w-6 text-secondary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                              <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                          @endif
                        @else
                          <svg class="h-6 w-6 text-secondary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                          </svg>
                        @endif
                      </div>

                      <div>
                        @if (!empty($method['method']['last4']))
                          <span class="font-medium text-secondary-900">
                            {{ esc_html(sprintf(__('%s ending in %s', 'sega-woo-theme'), wc_get_credit_card_type_label($method['method']['brand'] ?? ''), $method['method']['last4'])) }}
                          </span>
                        @else
                          <span class="font-medium text-secondary-900">
                            {!! wp_kses_post($method['method']['gateway']) !!}
                          </span>
                        @endif

                        @if (!empty($method['is_default']))
                          <span class="ml-2 inline-flex items-center rounded-full bg-primary-100 px-2 py-0.5 text-xs font-medium text-primary-700">
                            {{ __('Default', 'sega-woo-theme') }}
                          </span>
                        @endif
                      </div>
                    </div>

                  @elseif ('method-expires' === $column_id)
                    @if (!empty($method['expires']))
                      <span class="text-sm text-secondary-600">{{ esc_html($method['expires']) }}</span>
                    @else
                      <span class="text-sm text-secondary-400">{{ __('N/A', 'sega-woo-theme') }}</span>
                    @endif

                  @elseif ('method-actions' === $column_id)
                    <div class="flex justify-end gap-2">
                      @foreach ($method['actions'] as $key => $action)
                        @php
                          $action_class = ($key === 'delete')
                            ? 'inline-flex items-center rounded-lg border border-red-200 bg-red-50 px-3 py-1.5 text-xs font-medium text-red-600 transition-all hover:bg-red-100 hover:border-red-300'
                            : 'inline-flex items-center rounded-lg border border-secondary-300 bg-white px-3 py-1.5 text-xs font-medium text-secondary-700 shadow-sm transition-all hover:bg-secondary-50';
                        @endphp

                        <a
                          href="{{ esc_url($action['url']) }}"
                          class="button {{ sanitize_html_class($key) }} {{ $action_class }}"
                        >
                          @if ($key === 'delete')
                            <svg class="mr-1 h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                              <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                          @endif
                          {{ esc_html($action['name']) }}
                        </a>
                      @endforeach
                    </div>
                  @endif
                </td>
              @endforeach
            </tr>
          @endforeach
        @endforeach
      </tbody>
    </table>
  </div>

  {{-- Mobile Card View --}}
  <div class="space-y-4 md:hidden">
    @foreach ($saved_methods as $type => $methods)
      @foreach ($methods as $method)
        <div class="rounded-xl border border-secondary-200 bg-white p-4 {{ !empty($method['is_default']) ? 'border-primary-300 bg-primary-50/30' : '' }}">
          <div class="mb-4 flex items-start justify-between">
            <div class="flex items-center gap-3">
              <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-secondary-100">
                <svg class="h-6 w-6 text-secondary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                </svg>
              </div>
              <div>
                @if (!empty($method['method']['last4']))
                  <span class="font-medium text-secondary-900">
                    {{ esc_html(sprintf(__('%s ending in %s', 'sega-woo-theme'), wc_get_credit_card_type_label($method['method']['brand'] ?? ''), $method['method']['last4'])) }}
                  </span>
                @else
                  <span class="font-medium text-secondary-900">
                    {!! wp_kses_post($method['method']['gateway']) !!}
                  </span>
                @endif

                @if (!empty($method['expires']))
                  <p class="mt-0.5 text-sm text-secondary-500">
                    {{ __('Expires:', 'sega-woo-theme') }} {{ esc_html($method['expires']) }}
                  </p>
                @endif
              </div>
            </div>

            @if (!empty($method['is_default']))
              <span class="inline-flex items-center rounded-full bg-primary-100 px-2.5 py-1 text-xs font-medium text-primary-700">
                {{ __('Default', 'sega-woo-theme') }}
              </span>
            @endif
          </div>

          {{-- Actions --}}
          <div class="flex gap-2 border-t border-secondary-100 pt-4">
            @foreach ($method['actions'] as $key => $action)
              @php
                $action_class = ($key === 'delete')
                  ? 'flex-1 justify-center rounded-lg border border-red-200 bg-red-50 px-4 py-2 text-sm font-medium text-red-600 transition-all hover:bg-red-100'
                  : 'flex-1 justify-center rounded-lg border border-secondary-300 bg-white px-4 py-2 text-sm font-medium text-secondary-700 shadow-sm transition-all hover:bg-secondary-50';
              @endphp

              <a
                href="{{ esc_url($action['url']) }}"
                class="inline-flex items-center {{ $action_class }}"
              >
                {{ esc_html($action['name']) }}
              </a>
            @endforeach
          </div>
        </div>
      @endforeach
    @endforeach
  </div>

@else
  {{-- Empty State --}}
  <div class="flex flex-col items-center justify-center rounded-xl border border-secondary-200 bg-white py-12 text-center">
    <div class="mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-secondary-100">
      <svg class="h-10 w-10 text-secondary-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
        <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
      </svg>
    </div>
    <h3 class="mb-2 text-lg font-semibold text-secondary-900">
      {{ __('No saved payment methods', 'sega-woo-theme') }}
    </h3>
    <p class="mb-6 max-w-sm text-secondary-600">
      {!! wp_kses_post(apply_filters('woocommerce_no_available_payment_methods_message', __('No saved methods found. Add a payment method during checkout and it will appear here.', 'sega-woo-theme'))) !!}
    </p>
  </div>
@endif

@php do_action('woocommerce_after_account_payment_methods', $has_methods); @endphp

@if (WC()->payment_gateways->get_available_payment_gateways())
  {{-- Add Payment Method Link (if any gateway supports tokenization) --}}
  @php
    $has_tokenizable_gateway = false;
    foreach (WC()->payment_gateways->get_available_payment_gateways() as $gateway) {
      if ($gateway->supports('add_payment_method') || $gateway->supports('tokenization')) {
        $has_tokenizable_gateway = true;
        break;
      }
    }
  @endphp

  @if ($has_tokenizable_gateway)
    <div class="mt-6">
      <a
        href="{{ esc_url(wc_get_endpoint_url('add-payment-method')) }}"
        class="inline-flex items-center gap-2 rounded-xl border border-secondary-300 bg-white px-6 py-3 text-base font-medium text-secondary-700 shadow-sm transition-all hover:bg-secondary-50 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
      >
        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
        </svg>
        {{ esc_html__('Add payment method', 'sega-woo-theme') }}
      </a>
    </div>
  @endif
@endif
