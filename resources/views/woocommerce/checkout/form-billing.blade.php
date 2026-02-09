{{--
  Template: Checkout Billing Fields (Redesigned)
  Description: Renders the billing address form fields for WooCommerce checkout
  @see woocommerce/templates/checkout/form-billing.php
--}}

@php
  $checkout = WC()->checkout();
  $billing_fields = $checkout->get_checkout_fields('billing');

  // Remove email field if it's being shown separately in the contact section
  if (!is_user_logged_in() && isset($billing_fields['billing_email'])) {
    unset($billing_fields['billing_email']);
  }
@endphp

<div class="woocommerce-billing-fields">
  @php do_action('woocommerce_before_checkout_billing_form', $checkout); @endphp

  {{-- Billing Fields Container --}}
  <div class="woocommerce-billing-fields__field-wrapper">
    {{-- Let WooCommerce render all fields naturally, then style with CSS --}}
    @php
      foreach ($billing_fields as $key => $field) {
        // Get width from Customizer setting
        $width = (string) \App\get_checkout_field_width($key, '100');

        // Add width class to field
        $width_class_map = [
          '25'  => 'field-width-25',
          '33'  => 'field-width-33',
          '50'  => 'field-width-50',
          '66'  => 'field-width-66',
          '75'  => 'field-width-75',
          '100' => 'field-width-100',
        ];
        $width_class = $width_class_map[$width] ?? 'field-width-100';

        // Add our width class to the field's class array
        if (!isset($billing_fields[$key]['class'])) {
          $billing_fields[$key]['class'] = [];
        }
        $billing_fields[$key]['class'][] = $width_class;
      }
    @endphp

    <div class="billing-fields-grid" style="display: flex !important; flex-wrap: wrap !important; gap: 1rem !important;">
      @foreach ($billing_fields as $key => $field)
        @php
          woocommerce_form_field($key, $field, $checkout->get_value($key));
        @endphp
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
        <label class="flex cursor-pointer items-start gap-3 rounded-xl border-2 border-secondary-200 p-4 transition-all hover:border-secondary-300 hover:bg-secondary-50/50"
          :class="createAccount ? 'border-primary-500 bg-primary-50/50' : ''"
        >
          <input
            type="checkbox"
            name="createaccount"
            id="createaccount"
            class="mt-0.5 h-5 w-5 rounded border-secondary-300 text-primary-600 focus:ring-primary-500"
            value="1"
            x-model="createAccount"
            {{ !empty($checkout->get_value('createaccount')) ? 'checked' : '' }}
          />
          <div class="flex-1">
            <span class="block text-sm font-medium text-secondary-900">
              {{ __('Create an account?', 'sega-woo-theme') }}
            </span>
            <p class="mt-1 text-xs text-secondary-500">
              {{ __('Create an account for faster checkout, order tracking, and exclusive offers.', 'sega-woo-theme') }}
            </p>
          </div>
          <span class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full bg-secondary-100 text-secondary-400"
            :class="createAccount ? 'bg-primary-100 text-primary-600' : ''"
          >
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
          </span>
        </label>

        {{-- Account Fields (shown when checkbox is checked) --}}
        <div
          x-show="createAccount"
          x-collapse
          class="mt-4"
        >
          @php do_action('woocommerce_before_checkout_account_fields', $checkout); @endphp

          @if (!empty($checkout->get_checkout_fields('account')))
            <div class="space-y-4 rounded-xl border border-secondary-200 bg-secondary-50/50 p-4">
              <div class="flex items-center gap-2 text-sm text-secondary-600">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
                {{ __('Create your secure password below:', 'sega-woo-theme') }}
              </div>

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
