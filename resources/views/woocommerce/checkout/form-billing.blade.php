{{--
  Template: Checkout Billing Fields
  Description: Renders the billing address form fields for WooCommerce checkout
  @see woocommerce/templates/checkout/form-billing.php
--}}

@php
  $checkout = WC()->checkout();
@endphp

<div class="woocommerce-billing-fields">
  {{-- Section Header --}}
  <h2 class="mb-6 flex items-center gap-3 text-lg font-semibold text-secondary-900">
    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-primary-100 text-sm font-bold text-primary-600">
      1
    </span>
    {{ __('Billing details', 'sage') }}
  </h2>

  @php do_action('woocommerce_before_checkout_billing_form', $checkout); @endphp

  {{-- Billing Fields Container --}}
  <div class="woocommerce-billing-fields__field-wrapper">
    @php
      $billing_fields = $checkout->get_checkout_fields('billing');
    @endphp

    {{-- Grid Layout for Fields --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
      @foreach ($billing_fields as $key => $field)
        @php
          // Determine if field should be full width
          $full_width_fields = [
            'billing_company',
            'billing_address_1',
            'billing_address_2',
            'billing_email',
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

  @php do_action('woocommerce_after_checkout_billing_form', $checkout); @endphp
</div>

{{-- Create Account Section (for guests) --}}
@if (!is_user_logged_in() && $checkout->is_registration_enabled())
  <div class="woocommerce-account-fields mt-6 border-t border-secondary-200 pt-6">
    @php do_action('woocommerce_before_checkout_registration_form', $checkout); @endphp

    @if (!$checkout->is_registration_required())
      <div class="create-account">
        <label class="flex cursor-pointer items-start gap-3">
          <input
            type="checkbox"
            name="createaccount"
            id="createaccount"
            class="mt-0.5 h-5 w-5 rounded border-secondary-300 text-primary-600 focus:ring-primary-500"
            value="1"
            x-model="createAccount"
            {{ !empty($checkout->get_value('createaccount')) ? 'checked' : '' }}
          />
          <div>
            <span class="text-sm font-medium text-secondary-700">
              {{ __('Create an account?', 'sage') }}
            </span>
            <p class="mt-0.5 text-xs text-secondary-500">
              {{ __('Create an account for faster checkout and order tracking.', 'sage') }}
            </p>
          </div>
        </label>

        {{-- Account Fields (shown when checkbox is checked) --}}
        <div
          x-show="createAccount"
          x-collapse
          class="mt-4"
        >
          @php do_action('woocommerce_before_checkout_account_fields', $checkout); @endphp

          @if (!empty($checkout->get_checkout_fields('account')))
            <div class="space-y-4 rounded-lg border border-secondary-200 bg-secondary-50 p-4">
              <p class="text-sm text-secondary-600">
                {{ __('Create your account credentials below:', 'sage') }}
              </p>

              @foreach ($checkout->get_checkout_fields('account') as $key => $field)
                <div class="account-field">
                  @php
                    woocommerce_form_field($key, $field, $checkout->get_value($key));
                  @endphp
                </div>
              @endforeach
            </div>
          @endif

          @php do_action('woocommerce_after_checkout_account_fields', $checkout); @endphp
        </div>
      </div>
    @endif

    @php do_action('woocommerce_after_checkout_registration_form', $checkout); @endphp
  </div>
@endif

{{-- Billing Fields Styling --}}
<style>
  /* Custom styles for billing form fields */
  .woocommerce-billing-fields .form-row {
    margin-bottom: 0;
  }

  .woocommerce-billing-fields .woocommerce-input-wrapper {
    width: 100%;
  }

  .woocommerce-billing-fields label {
    display: block;
    margin-bottom: 0.375rem;
    font-size: 0.875rem;
    font-weight: 500;
    color: rgb(var(--color-secondary-700));
  }

  .woocommerce-billing-fields label .required {
    color: rgb(var(--color-red-500, 239 68 68));
    margin-left: 0.125rem;
  }

  .woocommerce-billing-fields input[type="text"],
  .woocommerce-billing-fields input[type="email"],
  .woocommerce-billing-fields input[type="tel"],
  .woocommerce-billing-fields input[type="password"],
  .woocommerce-billing-fields select,
  .woocommerce-billing-fields textarea {
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

  .woocommerce-billing-fields input:focus,
  .woocommerce-billing-fields select:focus,
  .woocommerce-billing-fields textarea:focus {
    outline: none;
    border-color: rgb(var(--color-primary-500));
    box-shadow: 0 0 0 2px rgb(var(--color-primary-500) / 0.2);
  }

  .woocommerce-billing-fields input::placeholder {
    color: rgb(var(--color-secondary-400));
  }

  /* Select2 styling for enhanced selects */
  .woocommerce-billing-fields .select2-container {
    width: 100% !important;
  }

  .woocommerce-billing-fields .select2-container--default .select2-selection--single {
    height: auto;
    padding: 0.625rem 1rem;
    font-size: 0.875rem;
    border: 1px solid rgb(var(--color-secondary-300));
    border-radius: 0.5rem;
  }

  .woocommerce-billing-fields .select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 1.25rem;
    padding: 0;
    color: rgb(var(--color-secondary-900));
  }

  .woocommerce-billing-fields .select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 100%;
    right: 0.75rem;
  }

  .woocommerce-billing-fields .select2-container--default.select2-container--focus .select2-selection--single,
  .woocommerce-billing-fields .select2-container--default.select2-container--open .select2-selection--single {
    border-color: rgb(var(--color-primary-500));
    box-shadow: 0 0 0 2px rgb(var(--color-primary-500) / 0.2);
  }

  /* Optional field indicator */
  .woocommerce-billing-fields .optional {
    font-size: 0.75rem;
    font-weight: 400;
    color: rgb(var(--color-secondary-500));
    margin-left: 0.25rem;
  }

  /* Validation states */
  .woocommerce-billing-fields .woocommerce-invalid input,
  .woocommerce-billing-fields .woocommerce-invalid select {
    border-color: rgb(var(--color-red-500, 239 68 68));
  }

  .woocommerce-billing-fields .woocommerce-validated input,
  .woocommerce-billing-fields .woocommerce-validated select {
    border-color: rgb(var(--color-green-500, 34 197 94));
  }
</style>
