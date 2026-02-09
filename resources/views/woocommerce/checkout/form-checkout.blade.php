{{--
  Template: Checkout Form (Redesigned)
  Description: Modern checkout page with improved UX and design
  @see woocommerce/templates/checkout/form-checkout.php
--}}

@extends('layouts.app')

@section('breadcrumbs')
  <x-breadcrumbs :items="[
    ['label' => __('Home', 'sega-woo-theme'), 'url' => home_url('/')],
    ['label' => __('Shop', 'sega-woo-theme'), 'url' => wc_get_page_permalink('shop')],
    ['label' => __('Cart', 'sega-woo-theme'), 'url' => wc_get_cart_url()],
    ['label' => __('Checkout', 'sega-woo-theme'), 'url' => null],
  ]" />
@endsection

@section('content')
  @php
    $checkout = WC()->checkout();
    $cart = WC()->cart;
    $cartCount = $cart->get_cart_contents_count();
  @endphp

  {{-- Check if checkout is available --}}
  @if (!$checkout->is_registration_enabled() && $checkout->is_registration_required() && !is_user_logged_in())
    {{-- Login Required State --}}
    <div class="mx-auto max-w-lg py-16">
      <div class="flex flex-col items-center justify-center text-center">
        <div class="mb-8 flex h-24 w-24 items-center justify-center rounded-full bg-gradient-to-br from-amber-100 to-amber-50">
          <svg class="h-12 w-12 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
          </svg>
        </div>

        <h1 class="mb-3 text-2xl font-bold text-secondary-900">
          {{ __('Login Required', 'sega-woo-theme') }}
        </h1>

        <p class="mb-8 text-secondary-500">
          {{ apply_filters('woocommerce_checkout_must_be_logged_in_message', __('You must be logged in to checkout.', 'woocommerce')) }}
        </p>

        <a
          href="{{ wc_get_page_permalink('myaccount') }}"
          class="inline-flex items-center gap-2 rounded-full bg-primary-600 px-8 py-4 text-base font-semibold text-white shadow-lg shadow-primary-600/20 transition-all hover:bg-primary-700 hover:shadow-xl hover:shadow-primary-600/30 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
        >
          <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
          </svg>
          {{ __('Login / Register', 'sega-woo-theme') }}
        </a>
      </div>
    </div>
  @else
    @php do_action('woocommerce_before_checkout_form', $checkout); @endphp

    {{-- Page Header --}}
    <div class="mb-8">
      <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
          <h1 class="text-2xl font-bold text-secondary-900 lg:text-3xl">
            {{ __('Checkout', 'sega-woo-theme') }}
          </h1>
          <p class="mt-1 text-sm text-secondary-500">
            {{ sprintf(_n('%d item in your order', '%d items in your order', $cartCount, 'sega-woo-theme'), $cartCount) }}
          </p>
        </div>
        <a
          href="{{ wc_get_cart_url() }}"
          class="inline-flex items-center gap-1.5 text-sm font-medium text-secondary-600 transition-colors hover:text-primary-600"
        >
          <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
          </svg>
          {{ __('Back to Cart', 'sega-woo-theme') }}
        </a>
      </div>
    </div>

    {{-- Checkout Progress Steps --}}
    <div class="checkout-progress mb-10 overflow-hidden rounded-2xl border border-secondary-200 bg-gradient-to-r from-white via-white to-secondary-50/30 p-5 shadow-sm sm:p-6">
      <div class="flex items-center justify-between">
        {{-- Step 1: Cart (Completed) --}}
        <div class="flex items-center">
          <div class="flex h-10 w-10 items-center justify-center rounded-full bg-green-500 text-white shadow-md shadow-green-500/25">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
            </svg>
          </div>
          <span class="ml-3 hidden text-sm font-semibold text-green-600 sm:block">{{ __('Cart', 'sega-woo-theme') }}</span>
        </div>

        {{-- Connector (Completed) --}}
        <div class="mx-2 h-1 flex-1 rounded-full bg-green-400 sm:mx-4"></div>

        {{-- Step 2: Checkout (Current) --}}
        <div class="flex items-center">
          <div class="relative flex h-10 w-10 items-center justify-center rounded-full bg-primary-600 text-white shadow-lg shadow-primary-600/40">
            <span class="text-sm font-bold">2</span>
            {{-- Pulse animation for current step --}}
            <span class="absolute inset-0 animate-ping rounded-full bg-primary-400 opacity-30"></span>
          </div>
          <span class="ml-3 hidden text-sm font-bold text-primary-600 sm:block">{{ __('Checkout', 'sega-woo-theme') }}</span>
        </div>

        {{-- Connector (Pending) --}}
        <div class="mx-2 h-1 flex-1 rounded-full bg-secondary-200 sm:mx-4"></div>

        {{-- Step 3: Payment --}}
        <div class="flex items-center">
          <div class="flex h-10 w-10 items-center justify-center rounded-full border-2 border-secondary-300 bg-white text-secondary-400">
            <span class="text-sm font-bold">3</span>
          </div>
          <span class="ml-3 hidden text-sm font-medium text-secondary-400 sm:block">{{ __('Payment', 'sega-woo-theme') }}</span>
        </div>

        {{-- Connector (Pending) --}}
        <div class="mx-2 h-1 flex-1 rounded-full bg-secondary-200 sm:mx-4"></div>

        {{-- Step 4: Complete --}}
        <div class="flex items-center">
          <div class="flex h-10 w-10 items-center justify-center rounded-full border-2 border-secondary-300 bg-white text-secondary-400">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
            </svg>
          </div>
          <span class="ml-3 hidden text-sm font-medium text-secondary-400 sm:block">{{ __('Complete', 'sega-woo-theme') }}</span>
        </div>
      </div>
    </div>

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
        activePayment: '{{ WC()->session->get('chosen_payment_method', '') }}',
        showCoupon: false
      }"
    >
      @php do_action('woocommerce_checkout_before_customer_details'); @endphp

      <div class="checkout-columns grid grid-cols-1 gap-8 lg:grid-cols-12 lg:gap-10 xl:gap-12">
        {{-- Left Column: Customer Details & Forms --}}
        <div class="checkout-left-column space-y-6 lg:col-span-7">

          {{-- Express Checkout (if payment methods support it) --}}
          @php
            $show_express_checkout = apply_filters('woocommerce_checkout_show_express_checkout', false);
          @endphp
          @if ($show_express_checkout)
            <div class="overflow-hidden rounded-2xl border border-secondary-200 bg-white shadow-sm">
              <div class="border-b border-secondary-100 bg-secondary-50/50 px-6 py-4">
                <h2 class="flex items-center gap-2 text-base font-semibold text-secondary-900">
                  <svg class="h-5 w-5 text-secondary-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                  </svg>
                  {{ __('Express Checkout', 'sega-woo-theme') }}
                </h2>
              </div>
              <div class="p-6">
                @php do_action('woocommerce_checkout_before_customer_details'); @endphp
              </div>
            </div>
          @endif

          {{-- Contact Information (for guests) --}}
          @if (!is_user_logged_in())
            <div class="checkout-section overflow-hidden rounded-2xl border border-secondary-200 bg-white shadow-sm">
              <div class="section-header border-b border-secondary-100 bg-gradient-to-r from-secondary-50 to-transparent px-6 py-5">
                <div class="flex items-center justify-between">
                  <div>
                    <h2 class="flex items-center gap-3 text-base font-bold text-secondary-900">
                      <span class="flex h-8 w-8 items-center justify-center rounded-full bg-primary-600 text-sm font-bold text-white shadow-md shadow-primary-600/25">
                        1
                      </span>
                      {{ __('Contact Information', 'sega-woo-theme') }}
                    </h2>
                    <p class="mt-1 ml-11 text-xs text-secondary-500">{{ __('We\'ll use this for order updates', 'sega-woo-theme') }}</p>
                  </div>
                  <a href="{{ wc_get_page_permalink('myaccount') }}" class="flex items-center gap-1.5 rounded-full bg-secondary-100 px-4 py-2 text-xs font-medium text-secondary-700 transition-colors hover:bg-secondary-200">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    {{ __('Log in', 'sega-woo-theme') }}
                  </a>
                </div>
              </div>
              <div class="p-6">
                <div class="form-field-wrapper">
                  @php
                    $email_field = $checkout->get_checkout_fields('billing')['billing_email'] ?? null;
                    if ($email_field) {
                      woocommerce_form_field('billing_email', $email_field, $checkout->get_value('billing_email'));
                    }
                  @endphp
                </div>
              </div>
            </div>
          @endif

          {{-- Billing Details --}}
          <div id="customer_details" class="checkout-section overflow-hidden rounded-2xl border border-secondary-200 bg-white shadow-sm">
            <div class="section-header border-b border-secondary-100 bg-gradient-to-r from-secondary-50 to-transparent px-6 py-5">
              <h2 class="flex items-center gap-3 text-base font-bold text-secondary-900">
                <span class="flex h-8 w-8 items-center justify-center rounded-full bg-primary-600 text-sm font-bold text-white shadow-md shadow-primary-600/25">
                  {{ is_user_logged_in() ? '1' : '2' }}
                </span>
                {{ __('Billing Address', 'sega-woo-theme') }}
              </h2>
              <p class="mt-1 ml-11 text-xs text-secondary-500">{{ __('Enter your billing details', 'sega-woo-theme') }}</p>
            </div>
            <div class="p-6">
              @include('woocommerce.checkout.form-billing')
            </div>
          </div>

          {{-- Shipping Details --}}
          @if (WC()->cart->needs_shipping() && WC()->cart->show_shipping())
            <div class="checkout-section overflow-hidden rounded-2xl border border-secondary-200 bg-white shadow-sm">
              <div class="section-header border-b border-secondary-100 bg-gradient-to-r from-secondary-50 to-transparent px-6 py-5">
                <h2 class="flex items-center gap-3 text-base font-bold text-secondary-900">
                  <span class="flex h-8 w-8 items-center justify-center rounded-full bg-primary-600 text-sm font-bold text-white shadow-md shadow-primary-600/25">
                    {{ is_user_logged_in() ? '2' : '3' }}
                  </span>
                  {{ __('Shipping Address', 'sega-woo-theme') }}
                </h2>
                <p class="mt-1 ml-11 text-xs text-secondary-500">{{ __('Where should we deliver your order?', 'sega-woo-theme') }}</p>
              </div>
              <div class="p-6">
                @include('woocommerce.checkout.form-shipping')
              </div>
            </div>
          @endif

          {{-- Additional Information / Order Notes --}}
          @if (apply_filters('woocommerce_enable_order_notes_field', 'yes' === get_option('woocommerce_enable_order_comments', 'yes')))
            <div class="checkout-section woocommerce-additional-fields overflow-hidden rounded-2xl border border-dashed border-secondary-300 bg-secondary-50/30 shadow-sm">
              <div class="section-header border-b border-dashed border-secondary-200 bg-gradient-to-r from-secondary-100/50 to-transparent px-6 py-5">
                <h2 class="flex items-center gap-3 text-base font-bold text-secondary-700">
                  <span class="flex h-8 w-8 items-center justify-center rounded-full bg-secondary-200 text-secondary-500">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                    </svg>
                  </span>
                  {{ __('Additional Information', 'sega-woo-theme') }}
                  <span class="rounded-full bg-secondary-200 px-2 py-0.5 text-xs font-medium text-secondary-500">{{ __('Optional', 'sega-woo-theme') }}</span>
                </h2>
                <p class="mt-1 ml-11 text-xs text-secondary-500">{{ __('Add special instructions for your order', 'sega-woo-theme') }}</p>
              </div>
              <div class="p-6">
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
            </div>
          @endif

          @php do_action('woocommerce_checkout_after_customer_details'); @endphp

        </div>

        {{-- Right Column: Order Summary & Payment --}}
        <div class="checkout-right-column lg:col-span-5">
          <div class="space-y-6 lg:pl-2">

            {{-- Order Summary --}}
            <div id="order_review" class="woocommerce-checkout-review-order overflow-hidden rounded-2xl border border-secondary-200 bg-white shadow-lg shadow-secondary-200/50">
              <div class="border-b border-secondary-100 bg-gradient-to-r from-secondary-50 to-secondary-100/50 px-6 py-5">
                <h2 class="flex items-center justify-between text-lg font-bold text-secondary-900">
                  <span class="flex items-center gap-2">
                    <svg class="h-5 w-5 text-primary-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                    {{ __('Order Summary', 'sega-woo-theme') }}
                  </span>
                  <a href="{{ wc_get_cart_url() }}" class="flex items-center gap-1 text-xs font-medium text-primary-600 transition-colors hover:text-primary-700">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                    </svg>
                    {{ __('Edit', 'sega-woo-theme') }}
                  </a>
                </h2>
              </div>

              <div class="p-6">
                @php do_action('woocommerce_checkout_before_order_review'); @endphp

                {{-- Order Review Table --}}
                @include('woocommerce.checkout.review-order')

                @php do_action('woocommerce_checkout_after_order_review'); @endphp
              </div>

              {{-- Coupon Code Section --}}
              @if (wc_coupons_enabled())
                <div class="border-t border-secondary-100 px-6 py-4">
                  <button
                    type="button"
                    @click="showCoupon = !showCoupon"
                    class="flex w-full items-center justify-between text-left"
                  >
                    <span class="flex items-center gap-2 text-sm font-medium text-secondary-700">
                      <svg class="h-4 w-4 text-secondary-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                      </svg>
                      {{ __('Have a coupon?', 'sega-woo-theme') }}
                    </span>
                    <svg
                      class="h-5 w-5 text-secondary-400 transition-transform duration-200"
                      :class="{ 'rotate-180': showCoupon }"
                      fill="none"
                      viewBox="0 0 24 24"
                      stroke="currentColor"
                      stroke-width="2"
                    >
                      <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                    </svg>
                  </button>

                  <div
                    x-show="showCoupon"
                    x-collapse
                    class="mt-4"
                  >
                    <div class="flex gap-2">
                      <input
                        type="text"
                        name="coupon_code"
                        id="coupon_code"
                        class="h-10 w-full rounded-lg border border-secondary-300 bg-white px-3 text-sm text-secondary-900 placeholder-secondary-400 shadow-sm transition-colors focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                        placeholder="{{ __('Enter code', 'sega-woo-theme') }}"
                      />
                      <button
                        type="submit"
                        name="apply_coupon"
                        class="h-10 flex-shrink-0 rounded-lg bg-secondary-900 px-4 text-sm font-medium text-white shadow-sm transition-colors hover:bg-secondary-800 focus:outline-none focus:ring-2 focus:ring-secondary-500 focus:ring-offset-2"
                      >
                        {{ __('Apply', 'sega-woo-theme') }}
                      </button>
                    </div>
                  </div>
                </div>
              @endif
            </div>

            {{-- Payment Methods --}}
            <div id="payment" class="woocommerce-checkout-payment overflow-hidden rounded-2xl border border-secondary-200 bg-white shadow-lg shadow-secondary-200/50">
              <div class="border-b border-secondary-100 bg-gradient-to-r from-secondary-50 to-secondary-100/50 px-6 py-5">
                <h2 class="flex items-center gap-3 text-lg font-bold text-secondary-900">
                  <span class="flex h-8 w-8 items-center justify-center rounded-full bg-primary-100 text-primary-600">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3-3v8a3 3 0 003 3z" />
                    </svg>
                  </span>
                  {{ __('Payment Method', 'sega-woo-theme') }}
                </h2>
              </div>

              <div class="payment-section-wrapper p-6">
                {{-- WooCommerce renders payment methods, terms, and place order button --}}
                @php do_action('woocommerce_checkout_payment'); @endphp
              </div>
            </div>

          </div>
        </div>
      </div>
    </form>

    @php do_action('woocommerce_after_checkout_form', $checkout); @endphp

    {{-- Checkout JavaScript --}}
    <script>
      // Translatable strings for JavaScript
      const checkoutI18n = {
        togglePasswordVisibility: '{{ esc_js(__('Toggle password visibility', 'sega-woo-theme')) }}'
      };

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

            // Scroll to error message
            const errorMessage = document.querySelector('.woocommerce-error');
            if (errorMessage) {
              errorMessage.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
          });

          // Update order review on change
          jQuery(document.body).on('update_checkout', function() {
            // Add loading state to order review
            const orderReview = document.getElementById('order_review');
            if (orderReview) {
              orderReview.classList.add('opacity-50');
            }
          });

          jQuery(document.body).on('updated_checkout', function() {
            // Remove loading state
            const orderReview = document.getElementById('order_review');
            if (orderReview) {
              orderReview.classList.remove('opacity-50');
            }

            // Re-style WooCommerce fields
            styleWooCommerceFields();
          });
        }

        // Style WooCommerce-generated form fields
        styleWooCommerceFields();
      });

      // Apply Tailwind styles to WooCommerce-generated form fields
      function styleWooCommerceFields() {
        // Style text inputs with enhanced classes
        document.querySelectorAll('.woocommerce-input-wrapper input[type="text"], .woocommerce-input-wrapper input[type="email"], .woocommerce-input-wrapper input[type="tel"], .woocommerce-input-wrapper input[type="password"], .woocommerce-input-wrapper input[type="number"]').forEach(function(input) {
          if (!input.classList.contains('styled')) {
            input.classList.add('styled');

            // Add placeholder if missing
            if (!input.placeholder && input.id) {
              const label = document.querySelector(`label[for="${input.id}"]`);
              if (label) {
                const labelText = label.textContent.replace(/[*\s]+$/, '').trim();
                input.placeholder = labelText;
              }
            }
          }
        });

        // Style textareas
        document.querySelectorAll('.woocommerce-input-wrapper textarea').forEach(function(textarea) {
          if (!textarea.classList.contains('styled')) {
            textarea.classList.add('styled');

            // Add placeholder if missing
            if (!textarea.placeholder && textarea.id) {
              const label = document.querySelector(`label[for="${textarea.id}"]`);
              if (label) {
                const labelText = label.textContent.replace(/[*\s]+$/, '').trim();
                textarea.placeholder = labelText;
              }
            }
          }
        });

        // Style select elements
        document.querySelectorAll('.woocommerce-input-wrapper select').forEach(function(select) {
          if (!select.classList.contains('styled')) {
            select.classList.add('styled');
          }
        });

        // Style labels with enhanced typography
        document.querySelectorAll('.woocommerce-billing-fields label, .woocommerce-shipping-fields label, .woocommerce-additional-fields label').forEach(function(label) {
          if (!label.classList.contains('styled') && !label.closest('.create-account') && !label.closest('.ship-to-different-address') && !label.closest('.woocommerce-terms-and-conditions-checkbox-text')) {
            label.classList.add('styled');
          }
        });

        // Style required asterisks
        document.querySelectorAll('.required').forEach(function(asterisk) {
          if (!asterisk.classList.contains('styled')) {
            asterisk.classList.add('styled');
            asterisk.setAttribute('aria-hidden', 'true');
          }
        });

        // Style optional labels
        document.querySelectorAll('.optional').forEach(function(optional) {
          if (!optional.classList.contains('styled')) {
            optional.classList.add('styled');
          }
        });

        // Style form rows
        document.querySelectorAll('.form-row').forEach(function(row) {
          if (!row.classList.contains('styled') && !row.classList.contains('place-order')) {
            row.classList.add('styled');
          }
        });

        // Style validation states
        styleValidationStates();

        // Style shipping methods with enhanced design
        styleShippingMethods();

        // Enhance Select2 dropdowns
        enhanceSelect2();

        // Add password visibility toggles
        addPasswordToggles();

        // Add field icons where applicable
        addFieldIcons();
      }

      // Style validation states
      function styleValidationStates() {
        // Invalid fields
        document.querySelectorAll('.woocommerce-invalid').forEach(function(field) {
          const wrapper = field.closest('.form-field-wrapper');
          if (wrapper) {
            wrapper.classList.add('woocommerce-invalid');
            wrapper.classList.remove('woocommerce-validated');
          }
        });

        // Valid fields
        document.querySelectorAll('.woocommerce-validated').forEach(function(field) {
          const wrapper = field.closest('.form-field-wrapper');
          if (wrapper) {
            wrapper.classList.add('woocommerce-validated');
            wrapper.classList.remove('woocommerce-invalid');
          }
        });
      }

      // Style shipping methods
      function styleShippingMethods() {
        document.querySelectorAll('#shipping_method li').forEach(function(method) {
          if (!method.classList.contains('styled')) {
            method.classList.add('styled', 'flex', 'items-center', 'gap-3', 'rounded-xl', 'border-2', 'border-secondary-200', 'p-4', 'transition-all', 'hover:border-secondary-300', 'hover:bg-secondary-50/50', 'cursor-pointer');

            const radio = method.querySelector('input[type="radio"]');
            if (radio) {
              radio.classList.add('h-4', 'w-4', 'border-secondary-300', 'text-primary-600', 'focus:ring-primary-500');

              // Highlight selected method
              if (radio.checked) {
                method.classList.add('border-primary-500', 'bg-primary-50/50');
                method.classList.remove('border-secondary-200');
              }

              // Listen for changes
              radio.addEventListener('change', function() {
                document.querySelectorAll('#shipping_method li').forEach(function(m) {
                  m.classList.remove('border-primary-500', 'bg-primary-50/50');
                  m.classList.add('border-secondary-200');
                });
                if (this.checked) {
                  method.classList.add('border-primary-500', 'bg-primary-50/50');
                  method.classList.remove('border-secondary-200');
                }
              });
            }
          }
        });

        document.querySelectorAll('#shipping_method label').forEach(function(label) {
          if (!label.classList.contains('styled')) {
            label.classList.add('styled', 'flex-1', 'flex', 'items-center', 'justify-between', 'text-sm', 'font-medium', 'text-secondary-700', 'cursor-pointer');
          }
        });
      }

      // Enhance Select2 dropdowns
      function enhanceSelect2() {
        document.querySelectorAll('.select2-container').forEach(function(container) {
          if (!container.classList.contains('styled')) {
            container.classList.add('styled', 'w-full');
          }
        });

        // Note: Select2 styles are primarily handled via CSS
        // This ensures the containers get the right width
      }

      // Add password visibility toggles
      function addPasswordToggles() {
        document.querySelectorAll('input[type="password"]').forEach(function(input) {
          if (input.dataset.passwordToggle) return;
          input.dataset.passwordToggle = 'true';

          const wrapper = input.parentElement;
          if (!wrapper) return;

          wrapper.style.position = 'relative';

          const toggleBtn = document.createElement('button');
          toggleBtn.type = 'button';
          toggleBtn.className = 'password-toggle-btn';
          toggleBtn.setAttribute('aria-label', checkoutI18n.togglePasswordVisibility);
          toggleBtn.innerHTML = `
            <svg class="eye-open" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
              <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            <svg class="eye-closed hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
            </svg>
          `;

          toggleBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';

            toggleBtn.querySelector('.eye-open').classList.toggle('hidden', !isPassword);
            toggleBtn.querySelector('.eye-closed').classList.toggle('hidden', isPassword);
          });

          wrapper.appendChild(toggleBtn);
        });
      }

      // Add field icons for certain fields
      function addFieldIcons() {
        const iconMap = {
          'billing_email': '<path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />',
          'billing_phone': '<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" />',
        };

        // Only add icons to main contact fields, not all fields
        // This can be expanded later if needed
      }

      // Initialize field styling with mutation observer for dynamic content
      function initFieldStyling() {
        styleWooCommerceFields();

        // Watch for dynamically added fields (e.g., when state field updates)
        const observer = new MutationObserver(function(mutations) {
          let shouldRestyle = false;
          mutations.forEach(function(mutation) {
            if (mutation.addedNodes.length > 0) {
              shouldRestyle = true;
            }
          });
          if (shouldRestyle) {
            requestAnimationFrame(styleWooCommerceFields);
          }
        });

        const billingFields = document.querySelector('.woocommerce-billing-fields');
        const shippingFields = document.querySelector('.woocommerce-shipping-fields');
        const orderReview = document.querySelector('#order_review');

        const observerConfig = { childList: true, subtree: true };

        if (billingFields) observer.observe(billingFields, observerConfig);
        if (shippingFields) observer.observe(shippingFields, observerConfig);
        if (orderReview) observer.observe(orderReview, observerConfig);
      }

      // Run on DOM ready
      initFieldStyling();
    </script>

    {{-- Additional Styles for WooCommerce Elements --}}
    <style>
      /* Two-Column Checkout Layout */
      .checkout-columns {
        @apply relative;
      }

      /* Left column - form fields */
      .checkout-left-column {
        @apply relative;
      }

      /* Right column - order summary sidebar */
      .checkout-right-column {
        @apply relative;
      }

      /* On large screens, add visual separator between columns */
      @media (min-width: 1024px) {
        .checkout-right-column::before {
          content: '';
          position: absolute;
          left: -1.25rem;
          top: 0;
          bottom: 0;
          width: 1px;
          background: linear-gradient(to bottom, transparent, rgb(229 231 235) 10%, rgb(229 231 235) 90%, transparent);
        }

        /* Add subtle background to right column content area */
        .checkout-right-column > div {
          @apply rounded-3xl;
        }
      }

      /* WooCommerce notices styling */
      .woocommerce-error,
      .woocommerce-message,
      .woocommerce-info {
        @apply mb-6 rounded-xl border p-4 text-sm;
      }

      .woocommerce-error {
        @apply border-red-200 bg-red-50 text-red-700;
      }

      .woocommerce-error li {
        @apply flex items-start gap-2;
      }

      .woocommerce-error li::before {
        content: none;
      }

      .woocommerce-message {
        @apply border-green-200 bg-green-50 text-green-700;
      }

      .woocommerce-info {
        @apply border-blue-200 bg-blue-50 text-blue-700;
      }

      /* Hide default WooCommerce styling */
      .woocommerce form .form-row label.checkbox {
        @apply inline-flex items-start gap-2;
      }

      /* Shipping methods container */
      #shipping_method {
        @apply list-none space-y-2 p-0 m-0;
      }

      /* Payment method icons */
      .payment_box {
        @apply [&_fieldset]:border-0 [&_fieldset]:p-0 [&_fieldset]:m-0;
      }

      .payment_box p:last-child {
        @apply mb-0;
      }

      /* Hide default WooCommerce list styles */
      .woocommerce-checkout-review-order-table tbody,
      .woocommerce-checkout-review-order-table tfoot {
        @apply border-0;
      }

      /* Ensure proper form field spacing in grid */
      .form-field-wrapper .form-row {
        @apply mb-0;
      }

      /* Select2 focus states */
      .select2-container--default .select2-selection--single:focus,
      .select2-container--default.select2-container--focus .select2-selection--single {
        border-color: rgb(59 130 246);
        outline: none;
        box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2);
      }

      /* Loading overlay for order review */
      #order_review {
        transition: opacity 0.2s ease;
      }

      /* Improve payment icons display */
      .payment-icons img {
        @apply inline-block;
      }

      /* Checkout progress step animations */
      .checkout-progress .animate-ping {
        animation-duration: 2s;
      }

      /* Responsive adjustments for two-column layout */
      @media (max-width: 1023px) {
        .checkout-right-column {
          order: -1;
          margin-bottom: 1.5rem;
        }
      }

      /* Order summary enhancement on larger screens */
      @media (min-width: 1024px) {
        #order_review,
        #payment {
          @apply transition-shadow duration-300;
        }

        #order_review:hover,
        #payment:hover {
          @apply shadow-xl shadow-secondary-200/70;
        }
      }

      /* Trust indicators styling */
      .checkout-right-column .space-y-4.rounded-2xl {
        @apply bg-gradient-to-br from-secondary-50/80 to-white;
      }

      /* Place order button enhancement */
      #place_order {
        @apply relative overflow-hidden;
      }

      #place_order::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transition: left 0.5s ease;
      }

      #place_order:hover::before {
        left: 100%;
      }

      /* Section cards in left column */
      .checkout-left-column > div {
        @apply transition-all duration-200;
      }

      .checkout-left-column > div:hover {
        @apply shadow-md;
      }

      /* Form section headers consistent styling */
      .checkout-left-column .border-b.bg-secondary-50\/50 {
        @apply bg-gradient-to-r from-secondary-50 to-transparent;
      }

      /* ============================================
         Password Toggle Button Inline Styles
         ============================================ */

      .password-toggle-btn {
        position: absolute;
        right: 0.875rem;
        top: 50%;
        transform: translateY(-50%);
        width: 1.75rem;
        height: 1.75rem;
        padding: 0.25rem;
        background: none;
        border: none;
        color: var(--color-secondary-400, #9ca3af);
        cursor: pointer;
        transition: color 0.15s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 0.375rem;
        z-index: 10;
      }

      .password-toggle-btn:hover {
        color: var(--color-secondary-600, #4b5563);
        background-color: var(--color-secondary-100, #f3f4f6);
      }

      .password-toggle-btn:focus {
        outline: none;
        box-shadow: 0 0 0 2px rgba(234, 179, 8, 0.3);
      }

      .password-toggle-btn svg {
        width: 1.25rem;
        height: 1.25rem;
      }

      .password-toggle-btn .hidden {
        display: none;
      }

      /* Ensure password inputs have padding for toggle button */
      input[type="password"][data-password-toggle="true"],
      input[type="text"][data-password-toggle="true"] {
        padding-right: 3rem !important;
      }

      /* ============================================
         Enhanced Form Field Focus Effects
         ============================================ */

      .form-field-wrapper input:focus,
      .form-field-wrapper select:focus,
      .form-field-wrapper textarea:focus {
        position: relative;
        z-index: 1;
      }

      /* Subtle label animation on focus */
      .form-field-wrapper:focus-within label {
        color: var(--color-primary-700, #b89206);
      }

      /* ============================================
         Field Group Visual Enhancements
         ============================================ */

      .billing-fields-grid,
      .shipping-fields-grid {
        @apply gap-x-4 gap-y-5;
      }

      /* Better field wrapper spacing */
      .form-field-wrapper .woocommerce-input-wrapper {
        width: 100%;
      }

      /* Account fields nested in billing section */
      .woocommerce-account-fields .space-y-4 .account-field {
        @apply transition-all duration-200;
      }

      /* ============================================
         Validation Feedback Enhancements
         ============================================ */

      /* Error state glow */
      .form-field-wrapper.woocommerce-invalid input,
      .form-field-wrapper.woocommerce-invalid select {
        animation: errorGlow 0.5s ease-out;
      }

      @keyframes errorGlow {
        0% { box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.3); }
        100% { box-shadow: none; }
      }

      /* Success state subtle pulse */
      .form-field-wrapper.woocommerce-validated input,
      .form-field-wrapper.woocommerce-validated select {
        animation: successPulse 0.3s ease-out;
      }

      @keyframes successPulse {
        0% { border-color: rgba(34, 197, 94, 0.5); }
        100% { border-color: rgb(34, 197, 94); }
      }
    </style>
  @endif
@endsection
