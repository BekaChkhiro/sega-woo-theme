{{--
  Template: My Account Edit Address Form
  Description: Form for editing billing or shipping address
  @see woocommerce/templates/myaccount/form-edit-address.php
  @version 8.5.0
--}}

@php
  // Variables passed by WooCommerce:
  // $load_address - 'billing' or 'shipping'

  $page_title = ('billing' === $load_address) ? __('Billing address', 'sega-woo-theme') : __('Shipping address', 'sega-woo-theme');
  $page_description = ('billing' === $load_address)
    ? __('This address will be used on the checkout page by default.', 'sega-woo-theme')
    : __('This address will be used as the default shipping address.', 'sega-woo-theme');

  // Get address fields
  $address_fields = WC()->countries->get_address_fields(get_user_meta(get_current_user_id(), $load_address . '_country', true), $load_address . '_');

  do_action('woocommerce_before_edit_account_address_form');
@endphp

{{-- Back to Addresses --}}
<div class="mb-6">
  <a
    href="{{ esc_url(wc_get_account_endpoint_url('edit-address')) }}"
    class="inline-flex items-center gap-2 text-sm font-medium text-secondary-600 transition-colors hover:text-secondary-900"
  >
    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
      <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
    </svg>
    {{ __('Back to Addresses', 'sega-woo-theme') }}
  </a>
</div>

{{-- Page Header --}}
<div class="mb-6">
  <div class="flex items-center gap-3">
    <div class="flex h-10 w-10 items-center justify-center rounded-lg {{ 'billing' === $load_address ? 'bg-primary-100' : 'bg-blue-100' }}">
      @if ('billing' === $load_address)
        <svg class="h-5 w-5 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
        </svg>
      @else
        <svg class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
          <path stroke-linecap="round" stroke-linejoin="round" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
        </svg>
      @endif
    </div>
    <div>
      <h2 class="text-xl font-semibold text-secondary-900">{{ $page_title }}</h2>
      <p class="mt-1 text-sm text-secondary-600">{{ $page_description }}</p>
    </div>
  </div>
</div>

@if (!$load_address)
  {{-- No address type specified - show error --}}
  <div class="rounded-xl border border-red-200 bg-red-50 p-6 text-center">
    <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-red-100">
      <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
      </svg>
    </div>
    <p class="text-red-700">{{ __('Invalid address type.', 'sega-woo-theme') }}</p>
    <a
      href="{{ esc_url(wc_get_account_endpoint_url('edit-address')) }}"
      class="mt-4 inline-flex items-center gap-2 text-sm font-medium text-red-600 hover:text-red-700"
    >
      {{ __('Return to addresses', 'sega-woo-theme') }}
    </a>
  </div>
@else
  <form
    class="woocommerce-address-fields"
    method="post"
  >
    @php do_action("woocommerce_before_edit_address_form_{$load_address}"); @endphp

    <div class="woocommerce-address-fields__field-wrapper space-y-4">
      @foreach ($address_fields as $key => $field)
        @php
          $field_key = $key;
          $field_value = !empty($_POST[$key]) ? wc_clean(wp_unslash($_POST[$key])) : $field['value'];
        @endphp

        {{-- Render different field types --}}
        @if (isset($field['type']) && $field['type'] === 'country')
          {{-- Country Select --}}
          <div class="{{ implode(' ', $field['class'] ?? ['form-row']) }}">
            <label for="{{ esc_attr($key) }}" class="mb-1.5 block text-sm font-medium text-secondary-700">
              {{ $field['label'] }}
              @if (!empty($field['required']))
                <span class="required text-red-500" aria-hidden="true">*</span>
              @endif
            </label>
            <select
              id="{{ esc_attr($key) }}"
              name="{{ esc_attr($key) }}"
              class="w-full rounded-lg border border-secondary-300 bg-white px-4 py-3 text-secondary-900 shadow-sm transition-colors focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20"
              autocomplete="{{ $field['autocomplete'] ?? '' }}"
              @if (!empty($field['required'])) required aria-required="true" @endif
            >
              <option value="">{{ __('Select a country / region&hellip;', 'sega-woo-theme') }}</option>
              @foreach (WC()->countries->get_countries() as $ckey => $cvalue)
                <option value="{{ esc_attr($ckey) }}" {{ selected($field_value, $ckey, false) }}>
                  {{ esc_html($cvalue) }}
                </option>
              @endforeach
            </select>
          </div>

        @elseif (isset($field['type']) && $field['type'] === 'state')
          {{-- State Select/Input --}}
          <div class="{{ implode(' ', $field['class'] ?? ['form-row']) }}">
            <label for="{{ esc_attr($key) }}" class="mb-1.5 block text-sm font-medium text-secondary-700">
              {{ $field['label'] }}
              @if (!empty($field['required']))
                <span class="required text-red-500" aria-hidden="true">*</span>
              @endif
            </label>
            <input
              type="text"
              id="{{ esc_attr($key) }}"
              name="{{ esc_attr($key) }}"
              class="w-full rounded-lg border border-secondary-300 bg-white px-4 py-3 text-secondary-900 placeholder-secondary-400 shadow-sm transition-colors focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20"
              value="{{ esc_attr($field_value) }}"
              placeholder="{{ $field['placeholder'] ?? '' }}"
              autocomplete="{{ $field['autocomplete'] ?? '' }}"
              @if (!empty($field['required'])) required aria-required="true" @endif
            />
          </div>

        @elseif (isset($field['type']) && $field['type'] === 'textarea')
          {{-- Textarea --}}
          <div class="{{ implode(' ', $field['class'] ?? ['form-row']) }}">
            <label for="{{ esc_attr($key) }}" class="mb-1.5 block text-sm font-medium text-secondary-700">
              {{ $field['label'] }}
              @if (!empty($field['required']))
                <span class="required text-red-500" aria-hidden="true">*</span>
              @endif
            </label>
            <textarea
              id="{{ esc_attr($key) }}"
              name="{{ esc_attr($key) }}"
              class="w-full rounded-lg border border-secondary-300 bg-white px-4 py-3 text-secondary-900 placeholder-secondary-400 shadow-sm transition-colors focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20"
              rows="3"
              placeholder="{{ $field['placeholder'] ?? '' }}"
              autocomplete="{{ $field['autocomplete'] ?? '' }}"
              @if (!empty($field['required'])) required aria-required="true" @endif
            >{{ esc_textarea($field_value) }}</textarea>
          </div>

        @elseif (isset($field['type']) && $field['type'] === 'select')
          {{-- Select --}}
          <div class="{{ implode(' ', $field['class'] ?? ['form-row']) }}">
            <label for="{{ esc_attr($key) }}" class="mb-1.5 block text-sm font-medium text-secondary-700">
              {{ $field['label'] }}
              @if (!empty($field['required']))
                <span class="required text-red-500" aria-hidden="true">*</span>
              @endif
            </label>
            <select
              id="{{ esc_attr($key) }}"
              name="{{ esc_attr($key) }}"
              class="w-full rounded-lg border border-secondary-300 bg-white px-4 py-3 text-secondary-900 shadow-sm transition-colors focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20"
              autocomplete="{{ $field['autocomplete'] ?? '' }}"
              @if (!empty($field['required'])) required aria-required="true" @endif
            >
              @foreach ($field['options'] as $okey => $ovalue)
                <option value="{{ esc_attr($okey) }}" {{ selected($field_value, $okey, false) }}>
                  {{ esc_html($ovalue) }}
                </option>
              @endforeach
            </select>
          </div>

        @else
          {{-- Default: Text Input --}}
          <div class="{{ implode(' ', $field['class'] ?? ['form-row']) }}">
            <label for="{{ esc_attr($key) }}" class="mb-1.5 block text-sm font-medium text-secondary-700">
              {{ $field['label'] }}
              @if (!empty($field['required']))
                <span class="required text-red-500" aria-hidden="true">*</span>
              @endif
            </label>
            <input
              type="{{ $field['type'] ?? 'text' }}"
              id="{{ esc_attr($key) }}"
              name="{{ esc_attr($key) }}"
              class="w-full rounded-lg border border-secondary-300 bg-white px-4 py-3 text-secondary-900 placeholder-secondary-400 shadow-sm transition-colors focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20"
              value="{{ esc_attr($field_value) }}"
              placeholder="{{ $field['placeholder'] ?? '' }}"
              autocomplete="{{ $field['autocomplete'] ?? '' }}"
              @if (!empty($field['required'])) required aria-required="true" @endif
              @if (!empty($field['maxlength'])) maxlength="{{ esc_attr($field['maxlength']) }}" @endif
            />
          </div>
        @endif
      @endforeach
    </div>

    @php do_action("woocommerce_after_edit_address_form_{$load_address}"); @endphp

    {{-- Submit Button --}}
    <div class="mt-8">
      <button
        type="submit"
        name="save_address"
        value="{{ __('Save address', 'sega-woo-theme') }}"
        class="woocommerce-Button button inline-flex items-center gap-2 rounded-xl bg-primary-600 px-8 py-3 text-base font-semibold text-white shadow-lg shadow-primary-600/25 transition-all hover:bg-primary-700 hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 active:scale-[0.98] {{ wc_wp_theme_get_element_class_name('button') }}"
      >
        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
        </svg>
        {{ __('Save address', 'sega-woo-theme') }}
      </button>

      @php wp_nonce_field('woocommerce-edit_address', 'woocommerce-edit-address-nonce'); @endphp

      <input type="hidden" name="action" value="edit_address" />
    </div>
  </form>
@endif

@php do_action('woocommerce_after_edit_account_address_form'); @endphp
