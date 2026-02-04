{{--
  Template: Checkout Shipping Fields
  Description: Renders the shipping address form fields for WooCommerce checkout
  @see woocommerce/templates/checkout/form-shipping.php
--}}

@php
  $checkout = WC()->checkout();
@endphp

<div class="woocommerce-shipping-fields">
  {{-- Section Header --}}
  <h2 class="mb-6 flex items-center gap-3 text-lg font-semibold text-secondary-900">
    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-primary-100 text-sm font-bold text-primary-600">
      2
    </span>
    {{ __('Shipping details', 'sage') }}
  </h2>

  {{-- Ship to Different Address Toggle --}}
  <div class="ship-to-different-address mb-6">
    <label class="flex cursor-pointer items-start gap-3 rounded-lg border border-secondary-200 bg-secondary-50 p-4 transition-all hover:border-secondary-300">
      <input
        type="checkbox"
        name="ship_to_different_address"
        id="ship_to_different_address"
        class="mt-0.5 h-5 w-5 rounded border-secondary-300 text-primary-600 focus:ring-primary-500"
        value="1"
        x-model="shipToDifferentAddress"
        {{ !empty($checkout->get_value('ship_to_different_address')) ? 'checked' : '' }}
      />
      <div>
        <span class="text-sm font-medium text-secondary-900">
          {{ __('Ship to a different address?', 'sage') }}
        </span>
        <p class="mt-0.5 text-xs text-secondary-500">
          {{ __('Check this if you want your order delivered to a different address than your billing address.', 'sage') }}
        </p>
      </div>
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

      {{-- Grid Layout for Fields --}}
      <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        @foreach ($shipping_fields as $key => $field)
          @php
            // Determine if field should be full width
            $full_width_fields = [
              'shipping_company',
              'shipping_address_1',
              'shipping_address_2',
            ];
            $is_full_width = in_array($key, $full_width_fields);

            // Add custom classes to field
            $field['class'][] = 'form-row-wide';
            if (!$is_full_width) {
              $field['class'][] = 'sm:col-span-1';
            }
          @endphp

          <div @class([
            'form-field-wrapper',
            'sm:col-span-2' => $is_full_width,
            'sm:col-span-1' => !$is_full_width,
          ])>
            @php
              woocommerce_form_field($key, $field, $checkout->get_value($key));
            @endphp
          </div>
        @endforeach
      </div>
    </div>

    @php do_action('woocommerce_after_checkout_shipping_form', $checkout); @endphp
  </div>

  {{-- Shipping Notes (optional info shown when not shipping to different address) --}}
  <div
    x-show="!shipToDifferentAddress"
    x-collapse
    class="shipping-same-as-billing"
  >
    <div class="flex items-start gap-3 rounded-lg border border-secondary-200 bg-white p-4">
      <svg class="mt-0.5 h-5 w-5 flex-shrink-0 text-secondary-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
      </svg>
      <div>
        <p class="text-sm text-secondary-600">
          {{ __('Your order will be shipped to your billing address.', 'sage') }}
        </p>
        <button
          type="button"
          @click="shipToDifferentAddress = true"
          class="mt-1 text-sm font-medium text-primary-600 hover:text-primary-700"
        >
          {{ __('Use a different shipping address', 'sage') }} &rarr;
        </button>
      </div>
    </div>
  </div>
</div>

{{-- Shipping Fields Styling --}}
<style>
  /* Custom styles for shipping form fields */
  .woocommerce-shipping-fields .form-row {
    margin-bottom: 0;
  }

  .woocommerce-shipping-fields .woocommerce-input-wrapper {
    width: 100%;
  }

  .woocommerce-shipping-fields label:not(.ship-to-different-address label) {
    display: block;
    margin-bottom: 0.375rem;
    font-size: 0.875rem;
    font-weight: 500;
    color: rgb(var(--color-secondary-700));
  }

  .woocommerce-shipping-fields label .required {
    color: rgb(var(--color-red-500, 239 68 68));
    margin-left: 0.125rem;
  }

  .woocommerce-shipping-fields .shipping_address input[type="text"],
  .woocommerce-shipping-fields .shipping_address input[type="email"],
  .woocommerce-shipping-fields .shipping_address input[type="tel"],
  .woocommerce-shipping-fields .shipping_address select,
  .woocommerce-shipping-fields .shipping_address textarea {
    width: 100%;
    padding: 0.625rem 1rem;
    font-size: 0.875rem;
    line-height: 1.25rem;
    color: rgb(var(--color-secondary-900));
    background-color: white;
    border: 1px solid rgb(var(--color-secondary-300));
    border-radius: 0.5rem;
    box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05);
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
  }

  .woocommerce-shipping-fields .shipping_address input:focus,
  .woocommerce-shipping-fields .shipping_address select:focus,
  .woocommerce-shipping-fields .shipping_address textarea:focus {
    outline: none;
    border-color: rgb(var(--color-primary-500));
    box-shadow: 0 0 0 2px rgb(var(--color-primary-500) / 0.2);
  }

  .woocommerce-shipping-fields .shipping_address input::placeholder {
    color: rgb(var(--color-secondary-400));
  }

  /* Select2 styling for enhanced selects */
  .woocommerce-shipping-fields .select2-container {
    width: 100% !important;
  }

  .woocommerce-shipping-fields .select2-container--default .select2-selection--single {
    height: auto;
    padding: 0.625rem 1rem;
    font-size: 0.875rem;
    border: 1px solid rgb(var(--color-secondary-300));
    border-radius: 0.5rem;
  }

  .woocommerce-shipping-fields .select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 1.25rem;
    padding: 0;
    color: rgb(var(--color-secondary-900));
  }

  .woocommerce-shipping-fields .select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 100%;
    right: 0.75rem;
  }

  .woocommerce-shipping-fields .select2-container--default.select2-container--focus .select2-selection--single,
  .woocommerce-shipping-fields .select2-container--default.select2-container--open .select2-selection--single {
    border-color: rgb(var(--color-primary-500));
    box-shadow: 0 0 0 2px rgb(var(--color-primary-500) / 0.2);
  }

  /* Optional field indicator */
  .woocommerce-shipping-fields .optional {
    font-size: 0.75rem;
    font-weight: 400;
    color: rgb(var(--color-secondary-500));
    margin-left: 0.25rem;
  }

  /* Validation states */
  .woocommerce-shipping-fields .woocommerce-invalid input,
  .woocommerce-shipping-fields .woocommerce-invalid select {
    border-color: rgb(var(--color-red-500, 239 68 68));
  }

  .woocommerce-shipping-fields .woocommerce-validated input,
  .woocommerce-shipping-fields .woocommerce-validated select {
    border-color: rgb(var(--color-green-500, 34 197 94));
  }

  /* Animation for collapse */
  .shipping_address,
  .shipping-same-as-billing {
    overflow: hidden;
  }
</style>
