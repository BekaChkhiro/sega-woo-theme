{{--
  Template: Lost Password Form
  Description: Form to request a password reset
  @see woocommerce/templates/myaccount/form-lost-password.php
  @version 9.2.0
--}}

@php
  do_action('woocommerce_before_lost_password_form');
@endphp

<div class="mx-auto max-w-md">
  <div class="rounded-xl border border-secondary-200 bg-white p-6 lg:p-8">
    {{-- Header --}}
    <div class="mb-6 text-center">
      <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-primary-100">
        <svg class="h-8 w-8 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
        </svg>
      </div>
      <h2 class="text-xl font-semibold text-secondary-900">
        {{ __('Lost your password?', 'sage') }}
      </h2>
      <p class="mt-2 text-sm text-secondary-600">
        {{ __('Enter your email address or username below and we\'ll send you a link to reset your password.', 'sage') }}
      </p>
    </div>

    <form
      class="woocommerce-ResetPassword lost_reset_password space-y-4"
      method="post"
    >
      {{-- Email/Username Field --}}
      <div class="woocommerce-form-row woocommerce-form-row--first form-row form-row-first">
        <label for="user_login" class="mb-1.5 block text-sm font-medium text-secondary-700">
          {{ __('Username or email', 'sage') }}
          <span class="required text-red-500" aria-hidden="true">*</span>
        </label>
        <input
          type="text"
          id="user_login"
          name="user_login"
          class="woocommerce-Input woocommerce-Input--text input-text w-full rounded-lg border border-secondary-300 bg-white px-4 py-3 text-secondary-900 placeholder-secondary-400 shadow-sm transition-colors focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20"
          autocomplete="username"
          required
          aria-required="true"
        />
      </div>

      @php do_action('woocommerce_lostpassword_form'); @endphp

      {{-- Submit Button --}}
      <div class="woocommerce-form-row form-row pt-2">
        <input type="hidden" name="wc_reset_password" value="true" />

        <button
          type="submit"
          value="{{ __('Reset password', 'sage') }}"
          class="woocommerce-Button button w-full rounded-xl bg-primary-600 px-6 py-3 text-base font-semibold text-white shadow-lg shadow-primary-600/25 transition-all hover:bg-primary-700 hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 active:scale-[0.98] {{ wc_wp_theme_get_element_class_name('button') }}"
        >
          {{ __('Reset password', 'sage') }}
        </button>

        @php wp_nonce_field('lost_password', 'woocommerce-lost-password-nonce'); @endphp
      </div>
    </form>

    {{-- Back to Login --}}
    <div class="mt-6 border-t border-secondary-200 pt-6 text-center">
      <a
        href="{{ esc_url(wc_get_page_permalink('myaccount')) }}"
        class="inline-flex items-center gap-2 text-sm font-medium text-secondary-600 transition-colors hover:text-secondary-900"
      >
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        {{ __('Back to login', 'sage') }}
      </a>
    </div>
  </div>
</div>

@php do_action('woocommerce_after_lost_password_form'); @endphp
