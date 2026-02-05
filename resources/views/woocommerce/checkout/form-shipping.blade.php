{{--
  Template: Checkout Shipping Fields (Redesigned)
  Description: Renders the shipping address form fields for WooCommerce checkout
  @see woocommerce/templates/checkout/form-shipping.php
--}}

@php
  $checkout = WC()->checkout();
@endphp

<div class="woocommerce-shipping-fields">
  {{-- Ship to Different Address Toggle --}}
  <div class="ship-to-different-address mb-6">
    <label class="flex cursor-pointer items-start gap-3 rounded-xl border-2 p-4 transition-all"
      :class="shipToDifferentAddress
        ? 'border-primary-500 bg-primary-50/50'
        : 'border-secondary-200 hover:border-secondary-300 hover:bg-secondary-50/50'"
    >
      <input
        type="checkbox"
        name="ship_to_different_address"
        id="ship_to_different_address"
        class="mt-0.5 h-5 w-5 rounded border-secondary-300 text-primary-600 focus:ring-primary-500"
        value="1"
        x-model="shipToDifferentAddress"
        {{ !empty($checkout->get_value('ship_to_different_address')) ? 'checked' : '' }}
      />
      <div class="flex-1">
        <span class="block text-sm font-medium text-secondary-900">
          {{ __('Ship to a different address?', 'sage') }}
        </span>
        <p class="mt-1 text-xs text-secondary-500">
          {{ __('Check this if you want your order delivered to a different address than your billing address.', 'sage') }}
        </p>
      </div>
      <span class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full transition-colors"
        :class="shipToDifferentAddress ? 'bg-primary-100 text-primary-600' : 'bg-secondary-100 text-secondary-400'"
      >
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
          <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
        </svg>
      </span>
    </label>
  </div>

  {{-- Shipping Address Fields --}}
  <div
    class="shipping_address"
    x-show="shipToDifferentAddress"
    x-collapse
  >
    @php do_action('woocommerce_before_checkout_shipping_form', $checkout); @endphp

    {{-- Shipping Fields Container --}}
    <div class="woocommerce-shipping-fields__field-wrapper">
      @php
        $shipping_fields = $checkout->get_checkout_fields('shipping');
      @endphp

      {{-- Optimized Grid Layout for Two-Column Checkout --}}
      <div class="shipping-fields-grid grid grid-cols-1 gap-x-4 gap-y-5 sm:grid-cols-12">
        @foreach ($shipping_fields as $key => $field)
          @php
            // Get width from Customizer setting (cast to string for array lookup)
            $width = (string) \App\get_checkout_field_width($key, '100');

            // Convert width percentage to Tailwind grid column spans (12 column grid)
            $width_to_span = [
              '25'  => 'sm:col-span-3',   // 3/12 = 25%
              '33'  => 'sm:col-span-4',   // 4/12 = 33%
              '50'  => 'sm:col-span-6',   // 6/12 = 50%
              '66'  => 'sm:col-span-8',   // 8/12 = 66%
              '75'  => 'sm:col-span-9',   // 9/12 = 75%
              '100' => 'sm:col-span-12',  // 12/12 = 100%
            ];

            $span_class = $width_to_span[$width] ?? 'sm:col-span-12';

            // Add custom classes to field
            $field['class'][] = 'form-row-wide';
          @endphp

          <div class="form-field-wrapper {{ $span_class }}">
            @php
              woocommerce_form_field($key, $field, $checkout->get_value($key));
            @endphp
          </div>
        @endforeach
      </div>
    </div>

    @php do_action('woocommerce_after_checkout_shipping_form', $checkout); @endphp
  </div>

  {{-- Shipping Info (shown when shipping to billing address) --}}
  <div
    x-show="!shipToDifferentAddress"
    x-collapse
    class="shipping-same-as-billing"
  >
    <div class="flex items-start gap-3 rounded-xl border border-secondary-200 bg-white p-4">
      <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-green-100">
        <svg class="h-5 w-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
        </svg>
      </div>
      <div class="flex-1">
        <p class="text-sm font-medium text-secondary-900">
          {{ __('Shipping to billing address', 'sage') }}
        </p>
        <p class="mt-0.5 text-xs text-secondary-500">
          {{ __('Your order will be delivered to the same address as your billing details.', 'sage') }}
        </p>
      </div>
    </div>
  </div>
</div>
