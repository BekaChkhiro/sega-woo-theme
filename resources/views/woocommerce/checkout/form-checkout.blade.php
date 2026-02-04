@extends('layouts.app')

@section('breadcrumbs')
  <x-breadcrumbs :items="[
    ['label' => __('Home', 'sage'), 'url' => home_url('/')],
    ['label' => __('Shop', 'sage'), 'url' => wc_get_page_permalink('shop')],
    ['label' => __('Cart', 'sage'), 'url' => wc_get_cart_url()],
    ['label' => __('Checkout', 'sage'), 'url' => null],
  ]" />
@endsection

@section('page-header')
  <div class="mb-8">
    <h1 class="text-2xl font-bold text-secondary-900 lg:text-3xl">
      {{ __('Checkout', 'sage') }}
    </h1>
  </div>
@endsection

@section('content')
  @php
    $checkout = WC()->checkout();
    $cart = WC()->cart;
  @endphp

  {{-- Check if checkout is available --}}
  @if (!$checkout->is_registration_enabled() && $checkout->is_registration_required() && !is_user_logged_in())
    <div class="rounded-xl border border-amber-200 bg-amber-50 p-6 text-center">
      <div class="mb-4 flex justify-center">
        <svg class="h-12 w-12 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
        </svg>
      </div>
      <h2 class="mb-2 text-lg font-semibold text-amber-800">
        {{ __('Login Required', 'sage') }}
      </h2>
      <p class="mb-4 text-amber-700">
        {{ apply_filters('woocommerce_checkout_must_be_logged_in_message', __('You must be logged in to checkout.', 'woocommerce')) }}
      </p>
      <a
        href="{{ wc_get_page_permalink('myaccount') }}"
        class="inline-flex items-center gap-2 rounded-xl bg-amber-600 px-6 py-3 font-semibold text-white transition-all hover:bg-amber-700"
      >
        {{ __('Login / Register', 'sage') }}
      </a>
    </div>
  @else
    @php do_action('woocommerce_before_checkout_form', $checkout); @endphp

    {{-- Checkout Form --}}
    <form
      id="checkout-form"
      name="checkout"
      method="post"
      class="woocommerce-checkout checkout"
      action="{{ wc_get_checkout_url() }}"
      enctype="multipart/form-data"
      x-data="{
        shipToDifferentAddress: {{ !empty($checkout->get_value('ship_to_different_address')) ? 'true' : 'false' }},
        createAccount: false,
        isProcessing: false,
        activePayment: '{{ WC()->session->get('chosen_payment_method', '') }}'
      }"
    >
      @php do_action('woocommerce_checkout_before_customer_details'); @endphp

      <div class="grid grid-cols-1 gap-8 lg:grid-cols-3 lg:gap-12">
        {{-- Customer Details (Billing & Shipping) --}}
        <div class="space-y-8 lg:col-span-2">

          {{-- Coupon Form (if enabled) --}}
          @if (wc_coupons_enabled())
            <div class="checkout-coupon rounded-xl border border-secondary-200 bg-white p-6">
              <div
                x-data="{ showCoupon: false }"
                class="space-y-4"
              >
                <button
                  type="button"
                  @click="showCoupon = !showCoupon"
                  class="flex w-full items-center justify-between text-left"
                >
                  <span class="flex items-center gap-2 text-sm text-secondary-600">
                    <svg class="h-5 w-5 text-secondary-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2zM10 8.5a.5.5 0 11-1 0 .5.5 0 011 0zm5 5a.5.5 0 11-1 0 .5.5 0 011 0z" />
                    </svg>
                    {{ __('Have a coupon?', 'sage') }}
                  </span>
                  <svg
                    class="h-5 w-5 text-secondary-400 transition-transform"
                    :class="{ 'rotate-180': showCoupon }"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="2"
                  >
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                  </svg>
                </button>

                <div x-show="showCoupon" x-collapse class="pt-2">
                  <div class="flex gap-2">
                    <input
                      type="text"
                      name="coupon_code"
                      id="coupon_code"
                      class="w-full rounded-lg border border-secondary-300 bg-white px-4 py-2.5 text-sm text-secondary-900 placeholder-secondary-400 shadow-sm transition-colors focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500"
                      placeholder="{{ __('Enter coupon code', 'sage') }}"
                    />
                    <button
                      type="submit"
                      name="apply_coupon"
                      class="flex-shrink-0 rounded-lg border border-secondary-300 bg-white px-4 py-2.5 text-sm font-medium text-secondary-700 shadow-sm transition-colors hover:bg-secondary-50 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
                    >
                      {{ __('Apply', 'sage') }}
                    </button>
                  </div>
                </div>
              </div>
            </div>
          @endif

          {{-- Billing Details --}}
          <div id="customer_details" class="rounded-xl border border-secondary-200 bg-white p-6">
            @include('woocommerce.checkout.form-billing')
          </div>

          {{-- Shipping Details --}}
          @if (WC()->cart->needs_shipping() && WC()->cart->show_shipping())
            <div class="rounded-xl border border-secondary-200 bg-white p-6">
              @include('woocommerce.checkout.form-shipping')
            </div>
          @endif

          {{-- Additional Information / Order Notes --}}
          @if (apply_filters('woocommerce_enable_order_notes_field', 'yes' === get_option('woocommerce_enable_order_comments', 'yes')))
            <div class="woocommerce-additional-fields rounded-xl border border-secondary-200 bg-white p-6">
              <h2 class="mb-6 flex items-center gap-3 text-lg font-semibold text-secondary-900">
                <span class="flex h-8 w-8 items-center justify-center rounded-full bg-secondary-100 text-sm font-bold text-secondary-600">
                  <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                  </svg>
                </span>
                {{ __('Additional information', 'sage') }}
              </h2>

              @php do_action('woocommerce_before_order_notes', $checkout); @endphp

              @if (!empty($checkout->get_checkout_fields('order')))
                <div class="woocommerce-additional-fields__field-wrapper space-y-4">
                  @foreach ($checkout->get_checkout_fields('order') as $key => $field)
                    @php
                      woocommerce_form_field($key, $field, $checkout->get_value($key));
                    @endphp
                  @endforeach
                </div>
              @endif

              @php do_action('woocommerce_after_order_notes', $checkout); @endphp
            </div>
          @endif

          @php do_action('woocommerce_checkout_after_customer_details'); @endphp

        </div>

        {{-- Order Review Sidebar --}}
        <div class="lg:col-span-1">
          <div class="sticky top-8 space-y-6">
            {{-- Order Summary --}}
            <div id="order_review" class="woocommerce-checkout-review-order rounded-xl border border-secondary-200 bg-secondary-50/50 p-6">
              <h2 class="mb-6 text-lg font-semibold text-secondary-900">
                {{ __('Your order', 'sage') }}
              </h2>

              @php do_action('woocommerce_checkout_before_order_review'); @endphp

              {{-- Order Review Table (loaded from separate template for AJAX compatibility) --}}
              @include('woocommerce.checkout.review-order')

              @php do_action('woocommerce_checkout_after_order_review'); @endphp
            </div>

            {{-- Payment Methods --}}
            <div id="payment" class="woocommerce-checkout-payment rounded-xl border border-secondary-200 bg-white p-6">
              <h2 class="mb-6 flex items-center gap-3 text-lg font-semibold text-secondary-900">
                <span class="flex h-8 w-8 items-center justify-center rounded-full bg-primary-100 text-sm font-bold text-primary-600">
                  <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                  </svg>
                </span>
                {{ __('Payment', 'sage') }}
              </h2>

              @if (WC()->cart->needs_payment())
                <ul class="wc_payment_methods payment_methods methods space-y-3">
                  @php
                    $available_gateways = WC()->payment_gateways()->get_available_payment_gateways();
                    $current_gateway = WC()->session->get('chosen_payment_method');

                    if (!$current_gateway && !empty($available_gateways)) {
                      $current_gateway = current(array_keys($available_gateways));
                    }
                  @endphp

                  @foreach ($available_gateways as $gateway)
                    <li class="wc_payment_method payment_method_{{ $gateway->id }}">
                      <label
                        for="payment_method_{{ $gateway->id }}"
                        class="flex cursor-pointer items-start gap-3 rounded-lg border border-secondary-200 p-4 transition-all"
                        :class="activePayment === '{{ $gateway->id }}' ? 'border-primary-500 bg-primary-50 ring-1 ring-primary-500' : 'hover:border-secondary-300 hover:bg-secondary-50'"
                      >
                        <input
                          type="radio"
                          id="payment_method_{{ $gateway->id }}"
                          class="mt-0.5 h-4 w-4 border-secondary-300 text-primary-600 focus:ring-primary-500"
                          name="payment_method"
                          value="{{ $gateway->id }}"
                          data-order_button_text="{{ $gateway->order_button_text }}"
                          x-model="activePayment"
                          {{ $gateway->chosen ? 'checked' : '' }}
                        />
                        <div class="flex-1">
                          <span class="block text-sm font-medium text-secondary-900">
                            {!! $gateway->get_title() !!}
                            {!! $gateway->get_icon() !!}
                          </span>
                          @if ($gateway->has_fields() || $gateway->get_description())
                            <div
                              class="payment_box payment_method_{{ $gateway->id }} mt-3 text-sm text-secondary-600"
                              x-show="activePayment === '{{ $gateway->id }}'"
                              x-collapse
                            >
                              @if ($gateway->has_fields())
                                @php $gateway->payment_fields(); @endphp
                              @else
                                <p>{!! wp_kses_post(wpautop(wptexturize($gateway->get_description()))) !!}</p>
                              @endif
                            </div>
                          @endif
                        </div>
                      </label>
                    </li>
                  @endforeach
                </ul>
              @else
                <div class="rounded-lg bg-secondary-100 p-4 text-center text-sm text-secondary-600">
                  {{ __('No payment required for this order.', 'sage') }}
                </div>
              @endif

              {{-- Terms & Conditions --}}
              <div class="woocommerce-terms-and-conditions-wrapper mt-6">
                @php do_action('woocommerce_checkout_terms_and_conditions'); @endphp

                @if (apply_filters('woocommerce_checkout_show_terms', true) && function_exists('wc_terms_and_conditions_checkbox_enabled') && wc_terms_and_conditions_checkbox_enabled())
                  <div class="woocommerce-terms-and-conditions-checkbox-text">
                    <label class="flex cursor-pointer items-start gap-3">
                      <input
                        type="checkbox"
                        id="terms"
                        name="terms"
                        class="mt-0.5 h-5 w-5 rounded border-secondary-300 text-primary-600 focus:ring-primary-500"
                        required
                      />
                      <span class="text-sm text-secondary-600">
                        {!! wc_get_terms_and_conditions_checkbox_text() !!}
                      </span>
                    </label>
                  </div>
                @endif
              </div>

              {{-- Place Order Button --}}
              <div class="form-row place-order mt-6">
                @php do_action('woocommerce_review_order_before_submit'); @endphp

                @php wp_nonce_field('woocommerce-process_checkout', 'woocommerce-process-checkout-nonce'); @endphp

                <button
                  type="submit"
                  id="place_order"
                  name="woocommerce_checkout_place_order"
                  class="button alt w-full rounded-xl bg-primary-600 px-6 py-4 text-base font-semibold text-white shadow-lg shadow-primary-600/25 transition-all hover:bg-primary-700 hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 active:scale-[0.98] disabled:cursor-not-allowed disabled:opacity-50"
                  value="{{ __('Place order', 'sage') }}"
                  data-value="{{ __('Place order', 'sage') }}"
                  :disabled="isProcessing"
                >
                  <span x-show="!isProcessing" class="flex items-center justify-center gap-2">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ __('Place order', 'sage') }}
                  </span>
                  <span x-show="isProcessing" class="flex items-center justify-center gap-2">
                    <svg class="h-5 w-5 animate-spin" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    {{ __('Processing...', 'sage') }}
                  </span>
                </button>

                @php do_action('woocommerce_review_order_after_submit'); @endphp
              </div>
            </div>

            {{-- Secure Checkout Notice --}}
            <div class="flex items-center justify-center gap-2 text-sm text-secondary-500">
              <svg class="h-4 w-4 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
              </svg>
              <span>{{ __('Secure checkout', 'sage') }}</span>
            </div>

            {{-- Back to Cart Link --}}
            <div class="text-center">
              <a
                href="{{ wc_get_cart_url() }}"
                class="inline-flex items-center gap-1 text-sm text-secondary-600 transition-colors hover:text-primary-600"
              >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M7 16l-4-4m0 0l4-4m-4 4h18" />
                </svg>
                {{ __('Return to cart', 'sage') }}
              </a>
            </div>
          </div>
        </div>
      </div>
    </form>

    @php do_action('woocommerce_after_checkout_form', $checkout); @endphp

    {{-- Checkout JavaScript --}}
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        const checkoutForm = document.getElementById('checkout-form');
        if (!checkoutForm) return;

        // Form validation and submit handling
        checkoutForm.addEventListener('submit', function(e) {
          // Set processing state
          const alpineData = Alpine.$data(checkoutForm);
          if (alpineData) {
            alpineData.isProcessing = true;
          }

          // Let WooCommerce handle AJAX checkout
        });

        // Listen for WooCommerce checkout events
        if (typeof jQuery !== 'undefined') {
          jQuery(document.body).on('checkout_error', function() {
            // Reset processing state on error
            const alpineData = Alpine.$data(checkoutForm);
            if (alpineData) {
              alpineData.isProcessing = false;
            }
          });

          // Update order review on change
          jQuery(document.body).on('update_checkout', function() {
            // Could add loading state for order review
          });

          jQuery(document.body).on('updated_checkout', function() {
            // Re-initialize Alpine after checkout updates
          });
        }

        // Style WooCommerce-generated form fields
        styleWooCommerceFields();

        // Re-style after updates
        if (typeof jQuery !== 'undefined') {
          jQuery(document.body).on('updated_checkout', function() {
            styleWooCommerceFields();
          });
        }
      });

      // Apply Tailwind styles to WooCommerce-generated form fields
      function styleWooCommerceFields() {
        // Style text inputs
        document.querySelectorAll('.woocommerce-input-wrapper input[type="text"], .woocommerce-input-wrapper input[type="email"], .woocommerce-input-wrapper input[type="tel"], .woocommerce-input-wrapper input[type="password"]').forEach(function(input) {
          if (!input.classList.contains('styled')) {
            input.classList.add('styled', 'w-full', 'rounded-lg', 'border', 'border-secondary-300', 'bg-white', 'px-4', 'py-2.5', 'text-sm', 'text-secondary-900', 'placeholder-secondary-400', 'shadow-sm', 'transition-colors', 'focus:border-primary-500', 'focus:outline-none', 'focus:ring-2', 'focus:ring-primary-500');
          }
        });

        // Style textareas
        document.querySelectorAll('.woocommerce-input-wrapper textarea').forEach(function(textarea) {
          if (!textarea.classList.contains('styled')) {
            textarea.classList.add('styled', 'w-full', 'rounded-lg', 'border', 'border-secondary-300', 'bg-white', 'px-4', 'py-2.5', 'text-sm', 'text-secondary-900', 'placeholder-secondary-400', 'shadow-sm', 'transition-colors', 'focus:border-primary-500', 'focus:outline-none', 'focus:ring-2', 'focus:ring-primary-500');
          }
        });

        // Style select elements
        document.querySelectorAll('.woocommerce-input-wrapper select').forEach(function(select) {
          if (!select.classList.contains('styled')) {
            select.classList.add('styled', 'w-full', 'rounded-lg', 'border', 'border-secondary-300', 'bg-white', 'px-4', 'py-2.5', 'text-sm', 'text-secondary-900', 'shadow-sm', 'transition-colors', 'focus:border-primary-500', 'focus:outline-none', 'focus:ring-2', 'focus:ring-primary-500');
          }
        });

        // Style labels
        document.querySelectorAll('.woocommerce-billing-fields label, .woocommerce-shipping-fields label, .woocommerce-additional-fields label').forEach(function(label) {
          if (!label.classList.contains('styled') && !label.closest('.create-account') && !label.closest('.ship-to-different-address')) {
            label.classList.add('styled', 'block', 'mb-1.5', 'text-sm', 'font-medium', 'text-secondary-700');
          }
        });

        // Style required asterisks
        document.querySelectorAll('.required').forEach(function(asterisk) {
          if (!asterisk.classList.contains('styled')) {
            asterisk.classList.add('styled', 'text-red-500', 'ml-0.5');
          }
        });

        // Style form rows
        document.querySelectorAll('.form-row').forEach(function(row) {
          if (!row.classList.contains('styled') && !row.classList.contains('place-order')) {
            row.classList.add('styled', 'mb-4');
          }
        });

        // Style shipping methods
        document.querySelectorAll('#shipping_method li').forEach(function(method) {
          if (!method.classList.contains('styled')) {
            method.classList.add('styled', 'flex', 'items-center', 'gap-2', 'py-2');
          }
        });

        document.querySelectorAll('#shipping_method input[type="radio"]').forEach(function(radio) {
          if (!radio.classList.contains('styled')) {
            radio.classList.add('styled', 'h-4', 'w-4', 'border-secondary-300', 'text-primary-600', 'focus:ring-primary-500');
          }
        });

        document.querySelectorAll('#shipping_method label').forEach(function(label) {
          if (!label.classList.contains('styled')) {
            label.classList.add('styled', 'text-sm', 'text-secondary-700', 'cursor-pointer');
          }
        });
      }
    </script>
  @endif
@endsection
