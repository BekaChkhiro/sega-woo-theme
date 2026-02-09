{{--
  Template: Reset Password Form
  Description: Form to set a new password after reset request
  @see woocommerce/templates/myaccount/form-reset-password.php
  @version 9.2.0
--}}

@php
  // Variables passed by WooCommerce:
  // $args - array with login, key

  do_action('woocommerce_before_reset_password_form');
@endphp

<div class="mx-auto max-w-md">
  <div class="rounded-xl border border-secondary-200 bg-white p-6 lg:p-8">
    {{-- Header --}}
    <div class="mb-6 text-center">
      <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-green-100">
        <svg class="h-8 w-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
        </svg>
      </div>
      <h2 class="text-xl font-semibold text-secondary-900">
        {{ __('Create new password', 'sega-woo-theme') }}
      </h2>
      <p class="mt-2 text-sm text-secondary-600">
        {{ __('Enter your new password below to complete the reset process.', 'sega-woo-theme') }}
      </p>
    </div>

    <form
      class="woocommerce-ResetPassword lost_reset_password space-y-4"
      method="post"
    >
      {{-- New Password --}}
      <div class="woocommerce-form-row woocommerce-form-row--first form-row form-row-first">
        <label for="password_1" class="mb-1.5 block text-sm font-medium text-secondary-700">
          {{ __('New password', 'sega-woo-theme') }}
          <span class="required text-red-500" aria-hidden="true">*</span>
        </label>
        <div class="relative" x-data="{ show: false }">
          <input
            :type="show ? 'text' : 'password'"
            id="password_1"
            name="password_1"
            class="woocommerce-Input woocommerce-Input--text input-text w-full rounded-lg border border-secondary-300 bg-white px-4 py-3 pr-12 text-secondary-900 shadow-sm transition-colors focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20"
            autocomplete="new-password"
            required
            aria-required="true"
          />
          <button
            type="button"
            @click="show = !show"
            class="absolute right-3 top-1/2 -translate-y-1/2 text-secondary-400 transition-colors hover:text-secondary-600"
            :aria-label="show ? '{{ __('Hide password', 'sega-woo-theme') }}' : '{{ __('Show password', 'sega-woo-theme') }}'"
          >
            <svg x-show="!show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
              <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>
            <svg x-show="show" x-cloak class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
            </svg>
          </button>
        </div>
      </div>

      {{-- Confirm Password --}}
      <div class="woocommerce-form-row woocommerce-form-row--last form-row form-row-last">
        <label for="password_2" class="mb-1.5 block text-sm font-medium text-secondary-700">
          {{ __('Confirm new password', 'sega-woo-theme') }}
          <span class="required text-red-500" aria-hidden="true">*</span>
        </label>
        <div class="relative" x-data="{ show: false }">
          <input
            :type="show ? 'text' : 'password'"
            id="password_2"
            name="password_2"
            class="woocommerce-Input woocommerce-Input--text input-text w-full rounded-lg border border-secondary-300 bg-white px-4 py-3 pr-12 text-secondary-900 shadow-sm transition-colors focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20"
            autocomplete="new-password"
            required
            aria-required="true"
          />
          <button
            type="button"
            @click="show = !show"
            class="absolute right-3 top-1/2 -translate-y-1/2 text-secondary-400 transition-colors hover:text-secondary-600"
          >
            <svg x-show="!show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
              <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>
            <svg x-show="show" x-cloak class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
            </svg>
          </button>
        </div>
      </div>

      @php do_action('woocommerce_resetpassword_form'); @endphp

      {{-- Submit Button --}}
      <div class="woocommerce-form-row form-row pt-2">
        <input type="hidden" name="reset_key" value="{{ isset($args['key']) ? esc_attr($args['key']) : '' }}" />
        <input type="hidden" name="reset_login" value="{{ isset($args['login']) ? esc_attr($args['login']) : '' }}" />

        <button
          type="submit"
          value="{{ __('Save', 'sega-woo-theme') }}"
          class="woocommerce-Button button w-full rounded-xl bg-green-600 px-6 py-3 text-base font-semibold text-white shadow-lg shadow-green-600/25 transition-all hover:bg-green-700 hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 active:scale-[0.98] {{ wc_wp_theme_get_element_class_name('button') }}"
        >
          {{ __('Save new password', 'sega-woo-theme') }}
        </button>

        @php wp_nonce_field('reset_password', 'woocommerce-reset-password-nonce'); @endphp
      </div>
    </form>
  </div>

  {{-- Security Note --}}
  <div class="mt-4 rounded-lg bg-secondary-50 p-4">
    <div class="flex gap-3">
      <svg class="h-5 w-5 flex-shrink-0 text-secondary-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
      </svg>
      <p class="text-sm text-secondary-600">
        {{ __('Choose a strong password with at least 8 characters, including uppercase, lowercase, numbers, and special characters.', 'sega-woo-theme') }}
      </p>
    </div>
  </div>
</div>

@php do_action('woocommerce_after_reset_password_form'); @endphp
