{{--
  Button Swatch Component

  Displays attribute options as clickable buttons with a hidden select for form submission.

  @param string $attributeName    - The attribute name (e.g., 'pa_size')
  @param string $attributeLabel   - The display label (e.g., 'Size')
  @param string $sanitizedName    - Sanitized name for form fields
  @param array  $options          - Array of options with slug, name, selected
  @param int    $productId        - The product ID
--}}

<div class="variation-row button-swatch-row" data-attribute="{{ $sanitizedName }}">
  <label
    for="{{ $attributeSlug() }}-{{ $productId }}"
    class="mb-3 flex items-center gap-1.5 text-sm font-semibold text-secondary-800"
  >
    {{ $attributeLabel }}
    <span class="text-red-500">*</span>
  </label>

  {{-- Hidden select for form submission (WooCommerce compatibility) --}}
  <select
    id="{{ $attributeSlug() }}-{{ $productId }}"
    name="{{ $attributeSlug() }}"
    class="variation-select sr-only"
    data-attribute_name="{{ $attributeSlug() }}"
    data-show_option_none="yes"
    aria-required="true"
  >
    <option value="">{{ sprintf(__('Choose %s', 'sega-woo-theme'), $attributeLabel) }}</option>
    @foreach ($options as $option)
      <option
        value="{{ esc_attr($option['slug']) }}"
        {{ !empty($option['selected']) ? 'selected' : '' }}
      >
        {{ $option['name'] }}
      </option>
    @endforeach
  </select>

  {{-- Visual button swatches --}}
  <div class="button-swatch-options flex flex-wrap gap-2.5">
    @foreach ($options as $option)
      <button
        type="button"
        class="button-swatch-option group relative min-w-[3.25rem] rounded-xl border-2 px-4 py-3 text-sm font-semibold shadow-sm transition-all duration-200 ease-out focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 active:scale-95 {{ !empty($option['selected']) ? 'border-primary-500 bg-primary-50 text-primary-700 shadow-md shadow-primary-500/10' : 'border-secondary-200 bg-white text-secondary-700 hover:border-primary-300 hover:bg-primary-50/50 hover:text-primary-600 hover:shadow-md' }}"
        data-value="{{ $option['slug'] }}"
        title="{{ $option['name'] }}"
        aria-label="{{ sprintf(__('Select %s', 'sega-woo-theme'), $option['name']) }}"
      >
        <span class="option-text relative z-10">{{ $option['name'] }}</span>

        {{-- Unavailable indicator (line-through) --}}
        <span class="unavailable-indicator pointer-events-none absolute inset-0 hidden items-center justify-center rounded-xl overflow-hidden">
          <span class="absolute left-0 right-0 top-1/2 h-[2px] -translate-y-1/2 rotate-[-8deg] bg-red-400/60"></span>
        </span>
      </button>
    @endforeach
  </div>
</div>
