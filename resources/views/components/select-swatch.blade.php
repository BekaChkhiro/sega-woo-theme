{{--
  Select Swatch Component

  Displays attribute options as a styled dropdown select.

  @param string $attributeName    - The attribute name (e.g., 'pa_material')
  @param string $attributeLabel   - The display label (e.g., 'Material')
  @param string $sanitizedName    - Sanitized name for form fields
  @param array  $options          - Array of options with slug, name, selected
  @param int    $productId        - The product ID
--}}

<div class="variation-row select-swatch-row" data-attribute="{{ $sanitizedName }}">
  <label
    for="{{ $attributeSlug() }}-{{ $productId }}"
    class="mb-3 flex items-center gap-1.5 text-sm font-semibold text-secondary-800"
  >
    {{ $attributeLabel }}
    <span class="text-red-500">*</span>
  </label>

  <div class="relative group">
    <select
      id="{{ $attributeSlug() }}-{{ $productId }}"
      name="{{ $attributeSlug() }}"
      class="variation-select block w-full appearance-none cursor-pointer rounded-xl border-2 border-secondary-200 bg-white px-4 py-3.5 pr-12 text-sm font-medium text-secondary-900 shadow-sm transition-all duration-200 hover:border-secondary-300 hover:shadow-md focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-1"
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

    {{-- Dropdown Arrow --}}
    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-4 transition-transform duration-200 group-focus-within:rotate-180">
      <svg class="h-5 w-5 text-secondary-500 transition-colors duration-200 group-hover:text-primary-500 group-focus-within:text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
      </svg>
    </div>
  </div>
</div>
