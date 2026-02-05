{{--
  Color Swatch Component

  Displays color options as clickable circles with a hidden select for form submission.

  @param string $attributeName    - The attribute name (e.g., 'pa_color')
  @param string $attributeLabel   - The display label (e.g., 'Color')
  @param string $sanitizedName    - Sanitized name for form fields
  @param array  $options          - Array of options with slug, name, color, selected
  @param int    $productId        - The product ID
--}}

<div class="variation-row color-swatch-row" data-attribute="{{ $sanitizedName }}">
  <label
    for="{{ $attributeSlug() }}-{{ $productId }}"
    class="mb-2 block text-sm font-medium text-secondary-700"
  >
    {{ $attributeLabel }}
    <span class="text-red-500">*</span>
    <span class="selected-value-label ml-2 text-sm font-normal text-secondary-500"></span>
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

  {{-- Visual color swatches --}}
  <div class="color-swatch-options flex flex-wrap gap-2">
    @foreach ($options as $option)
      <button
        type="button"
        class="color-swatch-option group relative h-10 w-10 rounded-full border-2 border-secondary-200 transition-all duration-200 hover:scale-110 hover:border-secondary-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 {{ !empty($option['selected']) ? 'ring-2 ring-primary-500 ring-offset-2 border-primary-500' : '' }}"
        data-value="{{ $option['slug'] }}"
        data-color="{{ $option['color'] ?? '#808080' }}"
        title="{{ $option['name'] }}"
        aria-label="{{ sprintf(__('Select %s', 'sage'), $option['name']) }}"
        style="background-color: {{ $option['color'] ?? '#808080' }};"
      >
        {{-- Selected checkmark indicator --}}
        <span class="selected-indicator absolute inset-0 flex items-center justify-center opacity-0 transition-opacity {{ !empty($option['selected']) ? 'opacity-100' : '' }}">
          <svg
            class="h-5 w-5 drop-shadow-md {{ $isLightColor($option['color'] ?? '#808080') ? 'text-secondary-800' : 'text-white' }}"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
            stroke-width="3"
          >
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
          </svg>
        </span>

        {{-- Unavailable indicator (strikethrough) --}}
        <span class="unavailable-indicator absolute inset-0 hidden items-center justify-center">
          <span class="h-0.5 w-full rotate-45 bg-secondary-600 opacity-60"></span>
        </span>
      </button>
    @endforeach
  </div>
</div>

