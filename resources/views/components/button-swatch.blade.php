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
    class="mb-2 block text-sm font-medium text-secondary-700"
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
    <option value="">{{ sprintf(__('Choose %s', 'sage'), $attributeLabel) }}</option>
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
  <div class="button-swatch-options flex flex-wrap gap-2">
    @foreach ($options as $option)
      <button
        type="button"
        class="button-swatch-option relative min-w-[3rem] rounded-lg border-2 px-4 py-2.5 text-sm font-medium transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 {{ !empty($option['selected']) ? 'border-primary-500 bg-primary-50 text-primary-700' : 'border-secondary-200 bg-white text-secondary-700 hover:border-secondary-300 hover:bg-secondary-50' }}"
        data-value="{{ $option['slug'] }}"
        title="{{ $option['name'] }}"
        aria-label="{{ sprintf(__('Select %s', 'sage'), $option['name']) }}"
      >
        <span class="option-text">{{ $option['name'] }}</span>

        {{-- Unavailable indicator (line-through) --}}
        <span class="unavailable-indicator pointer-events-none absolute inset-0 hidden items-center justify-center">
          <span class="absolute left-1 right-1 top-1/2 h-0.5 -translate-y-1/2 rotate-[-12deg] bg-secondary-400"></span>
        </span>
      </button>
    @endforeach
  </div>
</div>
