{{--
  Template: My Account Edit Account Form
  Description: Form for editing account details (name, email, password)
  @see woocommerce/templates/myaccount/form-edit-account.php
  @version 8.7.0
--}}

@php
  // Variables passed by WooCommerce:
  // $user - WP_User object

  do_action('woocommerce_before_edit_account_form');
@endphp

<div class="mb-6">
  <h2 class="text-xl font-semibold text-secondary-900">
    {{ __('Account Details', 'sage') }}
  </h2>
  <p class="mt-1 text-sm text-secondary-600">
    {{ __('Update your personal information and password.', 'sage') }}
  </p>
</div>

<form
  class="woocommerce-EditAccountForm edit-account"
  action=""
  method="post"
  {{ apply_filters('woocommerce_edit_account_form_attributes', '') }}
>
  @php do_action('woocommerce_edit_account_form_start'); @endphp

  {{-- Name Fields --}}
  <div class="mb-8">
    <h3 class="mb-4 flex items-center gap-2 text-sm font-semibold uppercase tracking-wider text-secondary-500">
      <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
      </svg>
      {{ __('Personal Information', 'sage') }}
    </h3>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
      {{-- First Name --}}
      <div class="woocommerce-form-row woocommerce-form-row--first form-row form-row-first">
        <label for="account_first_name" class="mb-1.5 block text-sm font-medium text-secondary-700">
          {{ __('First name', 'sage') }}
          <span class="required text-red-500" aria-hidden="true">*</span>
        </label>
        <input
          type="text"
          id="account_first_name"
          name="account_first_name"
          class="woocommerce-Input woocommerce-Input--text input-text w-full rounded-lg border border-secondary-300 bg-white px-4 py-3 text-secondary-900 placeholder-secondary-400 shadow-sm transition-colors focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20"
          autocomplete="given-name"
          value="{{ esc_attr($user->first_name) }}"
          required
          aria-required="true"
        />
      </div>

      {{-- Last Name --}}
      <div class="woocommerce-form-row woocommerce-form-row--last form-row form-row-last">
        <label for="account_last_name" class="mb-1.5 block text-sm font-medium text-secondary-700">
          {{ __('Last name', 'sage') }}
          <span class="required text-red-500" aria-hidden="true">*</span>
        </label>
        <input
          type="text"
          id="account_last_name"
          name="account_last_name"
          class="woocommerce-Input woocommerce-Input--text input-text w-full rounded-lg border border-secondary-300 bg-white px-4 py-3 text-secondary-900 placeholder-secondary-400 shadow-sm transition-colors focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20"
          autocomplete="family-name"
          value="{{ esc_attr($user->last_name) }}"
          required
          aria-required="true"
        />
      </div>
    </div>

    {{-- Display Name --}}
    <div class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide mt-4">
      <label for="account_display_name" class="mb-1.5 block text-sm font-medium text-secondary-700">
        {{ __('Display name', 'sage') }}
        <span class="required text-red-500" aria-hidden="true">*</span>
      </label>
      <input
        type="text"
        id="account_display_name"
        name="account_display_name"
        class="woocommerce-Input woocommerce-Input--text input-text w-full rounded-lg border border-secondary-300 bg-white px-4 py-3 text-secondary-900 placeholder-secondary-400 shadow-sm transition-colors focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20"
        value="{{ esc_attr($user->display_name) }}"
        required
        aria-required="true"
      />
      <p class="mt-1.5 text-xs text-secondary-500">
        <em>{{ __('This will be how your name will be displayed in the account section and in reviews.', 'sage') }}</em>
      </p>
    </div>

    {{-- Email --}}
    <div class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide mt-4">
      <label for="account_email" class="mb-1.5 block text-sm font-medium text-secondary-700">
        {{ __('Email address', 'sage') }}
        <span class="required text-red-500" aria-hidden="true">*</span>
      </label>
      <input
        type="email"
        id="account_email"
        name="account_email"
        class="woocommerce-Input woocommerce-Input--email input-text w-full rounded-lg border border-secondary-300 bg-white px-4 py-3 text-secondary-900 placeholder-secondary-400 shadow-sm transition-colors focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20"
        autocomplete="email"
        value="{{ esc_attr($user->user_email) }}"
        required
        aria-required="true"
      />
    </div>
  </div>

  {{-- Password Change Section --}}
  <fieldset class="mb-8 rounded-xl border border-secondary-200 bg-secondary-50/50 p-6">
    <legend class="mb-4 flex items-center gap-2 text-sm font-semibold uppercase tracking-wider text-secondary-500">
      <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
      </svg>
      {{ __('Password Change', 'sage') }}
    </legend>

    <p class="mb-4 text-sm text-secondary-600">
      {{ __('Leave blank to keep your current password.', 'sage') }}
    </p>

    {{-- Current Password --}}
    <div class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide mb-4">
      <label for="password_current" class="mb-1.5 block text-sm font-medium text-secondary-700">
        {{ __('Current password', 'sage') }}
      </label>
      <div class="relative" x-data="{ show: false }">
        <input
          :type="show ? 'text' : 'password'"
          id="password_current"
          name="password_current"
          class="woocommerce-Input woocommerce-Input--password input-text w-full rounded-lg border border-secondary-300 bg-white px-4 py-3 pr-12 text-secondary-900 shadow-sm transition-colors focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20"
          autocomplete="current-password"
        />
        <button
          type="button"
          @click="show = !show"
          class="absolute right-3 top-1/2 -translate-y-1/2 text-secondary-400 transition-colors hover:text-secondary-600"
          :aria-label="show ? '{{ __('Hide password', 'sage') }}' : '{{ __('Show password', 'sage') }}'"
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

    {{-- New Password --}}
    <div class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide mb-4">
      <label for="password_1" class="mb-1.5 block text-sm font-medium text-secondary-700">
        {{ __('New password', 'sage') }}
      </label>
      <div class="relative" x-data="{ show: false }">
        <input
          :type="show ? 'text' : 'password'"
          id="password_1"
          name="password_1"
          class="woocommerce-Input woocommerce-Input--password input-text w-full rounded-lg border border-secondary-300 bg-white px-4 py-3 pr-12 text-secondary-900 shadow-sm transition-colors focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20"
          autocomplete="new-password"
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

    {{-- Confirm New Password --}}
    <div class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
      <label for="password_2" class="mb-1.5 block text-sm font-medium text-secondary-700">
        {{ __('Confirm new password', 'sage') }}
      </label>
      <div class="relative" x-data="{ show: false }">
        <input
          :type="show ? 'text' : 'password'"
          id="password_2"
          name="password_2"
          class="woocommerce-Input woocommerce-Input--password input-text w-full rounded-lg border border-secondary-300 bg-white px-4 py-3 pr-12 text-secondary-900 shadow-sm transition-colors focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20"
          autocomplete="new-password"
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
  </fieldset>

  @php do_action('woocommerce_edit_account_form'); @endphp

  {{-- Submit Button --}}
  <div class="woocommerce-form-row form-row">
    @php wp_nonce_field('save_account_details', 'save-account-details-nonce'); @endphp

    <button
      type="submit"
      name="save_account_details"
      value="{{ __('Save changes', 'sage') }}"
      class="woocommerce-Button button inline-flex items-center gap-2 rounded-xl bg-primary-600 px-8 py-3 text-base font-semibold text-white shadow-lg shadow-primary-600/25 transition-all hover:bg-primary-700 hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 active:scale-[0.98] {{ wc_wp_theme_get_element_class_name('button') }}"
    >
      <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
      </svg>
      {{ __('Save changes', 'sage') }}
    </button>

    <input type="hidden" name="action" value="save_account_details" />
  </div>

  @php do_action('woocommerce_edit_account_form_end'); @endphp
</form>

@php do_action('woocommerce_after_edit_account_form'); @endphp
