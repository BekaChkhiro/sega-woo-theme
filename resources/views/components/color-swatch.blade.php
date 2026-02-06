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
    class="mb-3 flex items-center gap-1.5 text-sm font-semibold text-secondary-800"
  >
    {{ $attributeLabel }}
    <span class="text-red-500">*</span>
    <span class="selected-value-label ml-1 text-sm font-medium text-primary-600"></span>
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
  <div class="color-swatch-options flex flex-wrap gap-3">
    @foreach ($options as $option)
      @php
        $color = $option['color'] ?? '#808080';
        $isLight = $isLightColor($color);
        $isWhiteish = in_array(strtolower($color), ['#fff', '#ffffff', '#fafafa', '#f5f5f5', '#fefefe']);
      @endphp
      <button
        type="button"
        class="color-swatch-option group relative h-11 w-11 rounded-full shadow-sm transition-all duration-200 ease-out hover:scale-110 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 {{ !empty($option['selected']) ? 'ring-2 ring-primary-500 ring-offset-2 scale-105' : '' }} {{ $isWhiteish ? 'border-2 border-secondary-300' : 'border-2 border-transparent' }}"
        data-value="{{ $option['slug'] }}"
        data-color="{{ $color }}"
        title="{{ $option['name'] }}"
        aria-label="{{ sprintf(__('Select %s', 'sage'), $option['name']) }}"
        style="background-color: {{ $color }};"
      >
        {{-- Inner shadow for depth --}}
        <span class="absolute inset-0 rounded-full shadow-inner pointer-events-none"></span>

        {{-- Selected checkmark indicator --}}
        <span class="selected-indicator absolute inset-0 flex items-center justify-center opacity-0 transition-opacity duration-200 {{ !empty($option['selected']) ? 'opacity-100' : '' }}">
          <svg
            class="h-5 w-5 drop-shadow-md {{ $isLight ? 'text-secondary-800' : 'text-white' }}"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
            stroke-width="3"
          >
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
          </svg>
        </span>

        {{-- Unavailable indicator (diagonal line) --}}
        <span class="unavailable-indicator absolute inset-0 hidden items-center justify-center rounded-full overflow-hidden">
          <span class="absolute h-[2px] w-[140%] rotate-45 bg-red-500/70"></span>
        </span>

        {{-- Hover tooltip --}}
        <span class="absolute -bottom-8 left-1/2 -translate-x-1/2 whitespace-nowrap rounded bg-secondary-800 px-2 py-1 text-xs font-medium text-white opacity-0 transition-opacity duration-200 group-hover:opacity-100 pointer-events-none z-10">
          {{ $option['name'] }}
        </span>
      </button>
    @endforeach
  </div>
</div>

