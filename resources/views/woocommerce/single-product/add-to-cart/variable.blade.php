{{--
  Variable Product Add to Cart Form

  This template handles the add-to-cart functionality for variable products.
  It includes variation selection, quantity input, price updates, and form submission.

  @param int    $productId           - The product ID
  @param string $cartUrl             - The add to cart URL
  @param string $cartText            - The add to cart button text
  @param array  $quantityData        - Quantity input data (min, max, step, value)
  @param bool   $inStock             - Whether product is in stock
  @param bool   $purchasable         - Whether product is purchasable
  @param array  $variationAttributes - Variation attributes with options
  @param array  $defaultAttributes   - Default selected attributes
  @param string $variationsJson      - JSON encoded variations data
  @param array  $priceRange          - Min/max price range
--}}

@php
  // Default values for variables passed via @include
  $productId = $productId ?? 0;
  $cartUrl = $cartUrl ?? '';
  $cartText = $cartText ?? __('Add to cart', 'sage');
  $quantityData = $quantityData ?? ['min' => 1, 'max' => '', 'step' => 1, 'value' => 1];
  $inStock = $inStock ?? true;
  $purchasable = $purchasable ?? true;
  $variationAttributes = $variationAttributes ?? [];
  $defaultAttributes = $defaultAttributes ?? [];
  $variationsJson = $variationsJson ?? '[]';
  $priceRange = $priceRange ?? [];
  $variationAttributesWithDisplay = $variationAttributesWithDisplay ?? [];
@endphp

@if ($purchasable && $inStock)
  <form
    action="{{ $cartUrl }}"
    method="post"
    class="variations_form cart add-to-cart-form variable-add-to-cart"
    data-product-id="{{ $productId }}"
    data-product_id="{{ $productId }}"
    data-product-type="variable"
    data-product_variations="{{ $variationsJson }}"
    enctype="multipart/form-data"
  >
    @php
      do_action('woocommerce_before_add_to_cart_form');
      do_action('woocommerce_before_variations_form');
    @endphp

    {{-- Variation Attributes --}}
    @if (!empty($variationAttributesWithDisplay))
      <div class="variations space-y-5">
        @php
          do_action('woocommerce_before_variations');
        @endphp

        @foreach ($variationAttributesWithDisplay as $attributeName => $attribute)
          @if ($attribute['display_type'] === 'color')
            {{-- Color Swatch --}}
            <x-color-swatch
              :attribute-name="$attributeName"
              :attribute-label="$attribute['label']"
              :sanitized-name="$attribute['sanitized_name']"
              :options="$attribute['options']"
              :product-id="$productId"
            />
          @elseif ($attribute['display_type'] === 'button')
            {{-- Button Swatch --}}
            <x-button-swatch
              :attribute-name="$attributeName"
              :attribute-label="$attribute['label']"
              :sanitized-name="$attribute['sanitized_name']"
              :options="$attribute['options']"
              :product-id="$productId"
            />
          @else
            {{-- Select Dropdown --}}
            <x-select-swatch
              :attribute-name="$attributeName"
              :attribute-label="$attribute['label']"
              :sanitized-name="$attribute['sanitized_name']"
              :options="$attribute['options']"
              :product-id="$productId"
            />
          @endif
        @endforeach

        @php
          do_action('woocommerce_after_variations');
        @endphp
      </div>

      {{-- Clear Selection Link --}}
      <a
        href="#"
        class="reset_variations mt-3 inline-flex items-center gap-1 text-sm text-primary-600 transition-colors hover:text-primary-700"
        style="display: none;"
      >
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
        </svg>
        {{ __('Clear selection', 'sage') }}
      </a>
    @elseif (!empty($variationAttributes))
      {{-- Fallback to original variationAttributes if variationAttributesWithDisplay is empty --}}
      <div class="variations space-y-5">
        @php
          do_action('woocommerce_before_variations');
        @endphp

        @foreach ($variationAttributes as $attributeName => $options)
          @php
            $attributeLabel = wc_attribute_label($attributeName);
            $sanitizedName = sanitize_title($attributeName);
            $selectedValue = $defaultAttributes[$sanitizedName] ?? '';
            $attributeSlug = 'attribute_' . $sanitizedName;
          @endphp

          <div class="variation-row" data-attribute="{{ $sanitizedName }}">
            <label
              for="{{ $attributeSlug }}-{{ $productId }}"
              class="mb-2 block text-sm font-medium text-secondary-700"
            >
              {{ $attributeLabel }}
              <span class="text-red-500">*</span>
            </label>

            <div class="relative">
              <select
                id="{{ $attributeSlug }}-{{ $productId }}"
                name="{{ $attributeSlug }}"
                class="variation-select block w-full appearance-none rounded-xl border border-secondary-200 bg-white px-4 py-3.5 pr-10 text-secondary-900 shadow-sm ring-1 ring-secondary-900/5 transition-all duration-200 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500"
                data-attribute_name="{{ $attributeSlug }}"
                data-show_option_none="yes"
                aria-required="true"
              >
                <option value="">
                  {{ sprintf(__('Choose %s', 'sage'), $attributeLabel) }}
                </option>
                @foreach ($options as $option)
                  @php
                    $optionName = $option;
                    // Get term name if it's a taxonomy attribute
                    if (taxonomy_exists($attributeName)) {
                      $term = get_term_by('slug', $option, $attributeName);
                      if ($term) {
                        $optionName = $term->name;
                      }
                    }
                  @endphp
                  <option
                    value="{{ esc_attr($option) }}"
                    {{ selected($selectedValue, $option, false) }}
                  >
                    {{ $optionName }}
                  </option>
                @endforeach
              </select>

              {{-- Dropdown Arrow --}}
              <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                <svg class="h-5 w-5 text-secondary-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                </svg>
              </div>
            </div>
          </div>
        @endforeach

        @php
          do_action('woocommerce_after_variations');
        @endphp
      </div>

      {{-- Clear Selection Link --}}
      <a
        href="#"
        class="reset_variations mt-3 inline-flex items-center gap-1 text-sm text-primary-600 transition-colors hover:text-primary-700"
        style="display: none;"
      >
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
        </svg>
        {{ __('Clear selection', 'sage') }}
      </a>
    @endif

    {{-- Single Variation Wrap (price, availability, description) --}}
    <div class="single_variation_wrap mt-6">
      {{-- Variation Description & Price --}}
      <div class="woocommerce-variation single_variation">
        {{-- This will be populated by JavaScript when variation is selected --}}
        <div class="woocommerce-variation-description"></div>
        <div class="woocommerce-variation-price">
          {{-- Price Range Display (shown before variation selected) --}}
          @if (!empty($priceRange) && ($priceRange['min'] !== $priceRange['max']))
            <div class="price-range text-lg font-semibold text-secondary-900">
              <span class="woocommerce-Price-amount">
                {!! wc_price($priceRange['min']) !!}
              </span>
              <span class="price-separator mx-1">&ndash;</span>
              <span class="woocommerce-Price-amount">
                {!! wc_price($priceRange['max']) !!}
              </span>
            </div>
          @endif
        </div>
        <div class="woocommerce-variation-availability"></div>
      </div>

      {{-- Quantity and Add to Cart Section --}}
      <div class="woocommerce-variation-add-to-cart variations_button mt-6">
        @php
          do_action('woocommerce_before_add_to_cart_quantity');
        @endphp

        <div class="flex flex-col gap-4 sm:flex-row sm:flex-wrap sm:items-center">
          {{-- Quantity Input --}}
          <div class="quantity-wrapper">
            <label for="quantity-{{ $productId }}" class="sr-only">
              {{ __('Quantity', 'sage') }}
            </label>

            <div class="group/qty inline-flex items-center gap-1.5 rounded-full bg-secondary-100/80 p-1.5 transition-all duration-200 hover:bg-secondary-100 hover:shadow-md">
              {{-- Decrease Button --}}
              <button
                type="button"
                class="quantity-btn quantity-minus flex h-10 w-10 items-center justify-center rounded-full bg-white text-secondary-500 shadow-sm ring-1 ring-secondary-200/50 transition-all duration-200 hover:bg-primary-50 hover:text-primary-600 hover:ring-primary-200 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-1 active:scale-95 disabled:cursor-not-allowed disabled:opacity-40 disabled:hover:bg-white disabled:hover:text-secondary-500 disabled:hover:ring-secondary-200/50 disabled:active:scale-100 sm:h-11 sm:w-11"
                aria-label="{{ __('Decrease quantity', 'sage') }}"
                data-action="decrease"
              >
                <svg class="h-4 w-4 sm:h-5 sm:w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4" />
                </svg>
              </button>

              {{-- Quantity Input Field --}}
              <input
                type="number"
                id="quantity-{{ $productId }}"
                name="quantity"
                value="{{ $quantityData['value'] }}"
                min="{{ $quantityData['min'] }}"
                @if (!empty($quantityData['max']))
                  max="{{ $quantityData['max'] }}"
                @endif
                step="{{ $quantityData['step'] }}"
                inputmode="numeric"
                pattern="[0-9]*"
                class="quantity-input input-text qty text h-10 w-12 border-0 bg-transparent text-center text-base font-bold text-secondary-900 transition-colors focus:outline-none focus:ring-0 sm:h-11 sm:w-14 sm:text-lg [appearance:textfield] [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:appearance-none"
                aria-label="{{ __('Product quantity', 'sage') }}"
              />

              {{-- Increase Button --}}
              <button
                type="button"
                class="quantity-btn quantity-plus flex h-10 w-10 items-center justify-center rounded-full bg-white text-secondary-500 shadow-sm ring-1 ring-secondary-200/50 transition-all duration-200 hover:bg-primary-50 hover:text-primary-600 hover:ring-primary-200 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-1 active:scale-95 disabled:cursor-not-allowed disabled:opacity-40 disabled:hover:bg-white disabled:hover:text-secondary-500 disabled:hover:ring-secondary-200/50 disabled:active:scale-100 sm:h-11 sm:w-11"
                aria-label="{{ __('Increase quantity', 'sage') }}"
                data-action="increase"
              >
                <svg class="h-4 w-4 sm:h-5 sm:w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
              </button>
            </div>
          </div>

          @php
            do_action('woocommerce_after_add_to_cart_quantity');
          @endphp

          {{-- Hidden Fields --}}
          <input type="hidden" name="add-to-cart" value="{{ $productId }}" />
          <input type="hidden" name="product_id" value="{{ $productId }}" />
          <input type="hidden" name="variation_id" class="variation_id" value="0" />

          {{-- Add to Cart Button --}}
          @php
            do_action('woocommerce_before_add_to_cart_button');
          @endphp

          <button
            type="submit"
            class="single_add_to_cart_button add-to-cart-button button alt group relative flex flex-1 items-center justify-center gap-2.5 overflow-hidden rounded-xl bg-primary-600 px-10 py-4 text-base font-semibold text-white shadow-lg shadow-primary-600/25 transition-all duration-200 hover:bg-primary-700 hover:shadow-xl hover:shadow-primary-600/30 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 active:scale-[0.98] disabled:cursor-not-allowed disabled:bg-secondary-400 disabled:opacity-50 disabled:shadow-none sm:flex-none"
            disabled
          >
            {{-- Default State --}}
            <span class="button-content flex items-center gap-2">
              <svg class="h-5 w-5 transition-transform group-hover:scale-110 group-disabled:scale-100" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
              </svg>
              <span class="button-text">{{ $cartText }}</span>
            </span>

            {{-- Loading State --}}
            <span class="button-loading absolute inset-0 hidden items-center justify-center bg-primary-600">
              <svg class="h-5 w-5 animate-spin text-white" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <span class="ml-2">{{ __('Adding...', 'sage') }}</span>
            </span>

            {{-- Success State --}}
            <span class="button-success absolute inset-0 hidden items-center justify-center bg-green-600">
              <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
              </svg>
              <span class="ml-2">{{ __('Added!', 'sage') }}</span>
            </span>
          </button>

          @php
            do_action('woocommerce_after_add_to_cart_button');
          @endphp
        </div>
      </div>
    </div>

    @php
      do_action('woocommerce_after_variations_form');
      do_action('woocommerce_after_add_to_cart_form');
    @endphp
  </form>

  {{-- Variable Product Script --}}
  <script>
    (function() {
      'use strict';

      const form = document.querySelector('[data-product-id="{{ $productId }}"].variable-add-to-cart');
      if (!form) return;

      // Elements
      const variationSelects = form.querySelectorAll('.variation-select');
      const quantityInput = form.querySelector('.quantity-input');
      const minusBtn = form.querySelector('.quantity-minus');
      const plusBtn = form.querySelector('.quantity-plus');
      const submitBtn = form.querySelector('.add-to-cart-button');
      const variationIdInput = form.querySelector('.variation_id');
      const resetLink = form.querySelector('.reset_variations');
      const priceContainer = form.querySelector('.woocommerce-variation-price');
      const availabilityContainer = form.querySelector('.woocommerce-variation-availability');
      const descriptionContainer = form.querySelector('.woocommerce-variation-description');

      // Get variations data
      let variations = [];
      try {
        variations = JSON.parse(form.dataset.product_variations || '[]');
      } catch (e) {
        console.error('Failed to parse variations:', e);
      }

      // Quantity settings
      let min = parseInt(quantityInput?.min) || 1;
      let max = parseInt(quantityInput?.max) || 9999;
      const step = parseInt(quantityInput?.step) || 1;

      // Current state
      let currentVariation = null;

      /**
       * Update quantity button states
       */
      function updateQuantityButtons() {
        if (!quantityInput) return;

        const value = parseInt(quantityInput.value) || min;
        if (minusBtn) minusBtn.disabled = value <= min;
        if (plusBtn) plusBtn.disabled = max && value >= max;
      }

      /**
       * Handle quantity change
       */
      function handleQuantityChange(action) {
        if (!quantityInput) return;

        let value = parseInt(quantityInput.value) || min;

        if (action === 'decrease') {
          value = Math.max(min, value - step);
        } else if (action === 'increase') {
          value = Math.min(max || 9999, value + step);
        }

        quantityInput.value = value;
        updateQuantityButtons();
        quantityInput.dispatchEvent(new Event('change', { bubbles: true }));
      }

      /**
       * Validate quantity input
       */
      function validateQuantity() {
        if (!quantityInput) return;

        let value = parseInt(quantityInput.value);

        if (isNaN(value) || value < min) {
          value = min;
        } else if (max && value > max) {
          value = max;
        }

        value = Math.round((value - min) / step) * step + min;
        quantityInput.value = value;
        updateQuantityButtons();
      }

      /**
       * Get current attribute selections
       */
      function getSelectedAttributes() {
        const selected = {};
        variationSelects.forEach(select => {
          const attrName = select.dataset.attribute_name;
          selected[attrName] = select.value;
        });
        return selected;
      }

      /**
       * Check if all attributes are selected
       */
      function allAttributesSelected() {
        return Array.from(variationSelects).every(select => select.value !== '');
      }

      /**
       * Find matching variation
       */
      function findMatchingVariation(selectedAttrs) {
        return variations.find(variation => {
          if (!variation.is_purchasable || !variation.is_in_stock) {
            return false;
          }

          return Object.keys(selectedAttrs).every(attrName => {
            const selectedValue = selectedAttrs[attrName];
            const variationValue = variation.attributes[attrName];

            // Empty variation attribute means "any"
            if (!variationValue || variationValue === '') {
              return true;
            }

            return selectedValue === variationValue;
          });
        });
      }

      /**
       * Update the display when variation changes
       */
      function updateVariationDisplay(variation) {
        currentVariation = variation;

        if (variation) {
          // Update variation ID
          variationIdInput.value = variation.variation_id;

          // Update price
          if (priceContainer && variation.price_html) {
            priceContainer.innerHTML = variation.price_html;
          }

          // Update availability
          if (availabilityContainer && variation.availability_html) {
            availabilityContainer.innerHTML = variation.availability_html;
          }

          // Update description
          if (descriptionContainer && variation.variation_description) {
            descriptionContainer.innerHTML = '<p class="text-sm text-secondary-600 mb-4">' + variation.variation_description + '</p>';
          } else if (descriptionContainer) {
            descriptionContainer.innerHTML = '';
          }

          // Update quantity limits based on variation stock
          if (variation.max_qty !== '' && variation.max_qty !== null) {
            max = parseInt(variation.max_qty);
            if (quantityInput) quantityInput.max = max;
          } else {
            max = 9999;
            if (quantityInput) quantityInput.removeAttribute('max');
          }

          if (variation.min_qty !== '' && variation.min_qty !== null) {
            min = parseInt(variation.min_qty);
            if (quantityInput) quantityInput.min = min;
          }

          // Ensure current quantity is within limits
          validateQuantity();

          // Enable the add to cart button
          submitBtn.disabled = false;
          submitBtn.classList.remove('disabled:bg-secondary-400');

          // Show reset link
          if (resetLink) resetLink.style.display = '';

          // Trigger WooCommerce event for gallery update
          if (typeof jQuery !== 'undefined') {
            jQuery(form).trigger('found_variation', [variation]);
          }

        } else {
          // Reset variation
          variationIdInput.value = 0;

          // Reset price to range
          if (priceContainer) {
            const minPrice = '{{ !empty($priceRange["min"]) ? wc_price($priceRange["min"]) : "" }}';
            const maxPrice = '{{ !empty($priceRange["max"]) ? wc_price($priceRange["max"]) : "" }}';

            if (minPrice && maxPrice && minPrice !== maxPrice) {
              priceContainer.innerHTML = '<div class="price-range text-lg font-semibold text-secondary-900">' +
                '<span class="woocommerce-Price-amount">' + minPrice + '</span>' +
                '<span class="price-separator mx-1">&ndash;</span>' +
                '<span class="woocommerce-Price-amount">' + maxPrice + '</span></div>';
            }
          }

          // Clear availability and description
          if (availabilityContainer) availabilityContainer.innerHTML = '';
          if (descriptionContainer) descriptionContainer.innerHTML = '';

          // Reset quantity limits
          min = {{ $quantityData['min'] }};
          max = {{ $quantityData['max'] ?: 9999 }};
          if (quantityInput) {
            quantityInput.min = min;
            if (max < 9999) {
              quantityInput.max = max;
            } else {
              quantityInput.removeAttribute('max');
            }
          }

          // Disable the add to cart button
          submitBtn.disabled = true;

          // Trigger WooCommerce reset event
          if (typeof jQuery !== 'undefined') {
            jQuery(form).trigger('reset_image');
            jQuery(form).trigger('reset_data');
          }
        }
      }

      /**
       * Handle attribute selection change
       */
      function handleAttributeChange() {
        const selected = getSelectedAttributes();
        const hasSelection = Object.values(selected).some(val => val !== '');

        // Show/hide reset link
        if (resetLink) {
          resetLink.style.display = hasSelection ? '' : 'none';
        }

        // Check if all attributes selected
        if (allAttributesSelected()) {
          const variation = findMatchingVariation(selected);
          updateVariationDisplay(variation);

          if (!variation) {
            // No matching variation - show message
            if (availabilityContainer) {
              availabilityContainer.innerHTML = '<p class="text-sm text-amber-600">' +
                '{{ __("This combination is unavailable.", "sage") }}' +
                '</p>';
            }
          }
        } else {
          updateVariationDisplay(null);
        }

        // Update available options based on current selection
        updateAvailableOptions(selected);
      }

      /**
       * Update available options based on current selection
       * Dims out options that won't result in a valid variation
       */
      function updateAvailableOptions(selected) {
        // Handle regular select dropdowns
        variationSelects.forEach(select => {
          const currentAttr = select.dataset.attribute_name;
          const options = select.querySelectorAll('option');

          options.forEach(option => {
            if (option.value === '') return; // Skip "Choose" option

            // Build test selection with this option
            const testSelection = { ...selected, [currentAttr]: option.value };

            // Check if any variation matches this selection
            const hasMatch = checkVariationMatch(testSelection);

            // Visual feedback for unavailable options
            option.disabled = !hasMatch;
            if (!hasMatch) {
              option.classList.add('text-secondary-400');
            } else {
              option.classList.remove('text-secondary-400');
            }
          });
        });

        // Handle color swatches
        form.querySelectorAll('.color-swatch-option').forEach(swatch => {
          const row = swatch.closest('.color-swatch-row');
          const attrName = row?.dataset.attribute;
          if (!attrName) return;

          const currentAttr = 'attribute_' + attrName;
          const testSelection = { ...selected, [currentAttr]: swatch.dataset.value };
          const hasMatch = checkVariationMatch(testSelection);

          // Update swatch availability visual state
          const unavailableIndicator = swatch.querySelector('.unavailable-indicator');
          if (!hasMatch) {
            swatch.classList.add('opacity-40', 'cursor-not-allowed');
            swatch.disabled = true;
            if (unavailableIndicator) unavailableIndicator.classList.remove('hidden');
            if (unavailableIndicator) unavailableIndicator.classList.add('flex');
          } else {
            swatch.classList.remove('opacity-40', 'cursor-not-allowed');
            swatch.disabled = false;
            if (unavailableIndicator) unavailableIndicator.classList.add('hidden');
            if (unavailableIndicator) unavailableIndicator.classList.remove('flex');
          }
        });

        // Handle button swatches
        form.querySelectorAll('.button-swatch-option').forEach(swatch => {
          const row = swatch.closest('.button-swatch-row');
          const attrName = row?.dataset.attribute;
          if (!attrName) return;

          const currentAttr = 'attribute_' + attrName;
          const testSelection = { ...selected, [currentAttr]: swatch.dataset.value };
          const hasMatch = checkVariationMatch(testSelection);

          // Update swatch availability visual state
          const unavailableIndicator = swatch.querySelector('.unavailable-indicator');
          if (!hasMatch) {
            swatch.classList.add('opacity-40', 'cursor-not-allowed');
            swatch.disabled = true;
            if (unavailableIndicator) unavailableIndicator.classList.remove('hidden');
            if (unavailableIndicator) unavailableIndicator.classList.add('flex');
          } else {
            swatch.classList.remove('opacity-40', 'cursor-not-allowed');
            swatch.disabled = false;
            if (unavailableIndicator) unavailableIndicator.classList.add('hidden');
            if (unavailableIndicator) unavailableIndicator.classList.remove('flex');
          }
        });
      }

      /**
       * Check if a selection matches any available variation
       */
      function checkVariationMatch(testSelection) {
        return variations.some(variation => {
          if (!variation.is_purchasable || !variation.is_in_stock) {
            return false;
          }

          return Object.keys(testSelection).every(attrName => {
            const testValue = testSelection[attrName];
            if (!testValue) return true; // Empty selection matches all

            const variationValue = variation.attributes[attrName];
            if (!variationValue || variationValue === '') return true; // "Any" matches all

            return testValue === variationValue;
          });
        });
      }

      /**
       * Reset all variations
       */
      function resetVariations(e) {
        if (e) e.preventDefault();

        variationSelects.forEach(select => {
          select.value = '';
        });

        updateVariationDisplay(null);

        // Reset all select options to enabled
        variationSelects.forEach(select => {
          select.querySelectorAll('option').forEach(option => {
            option.disabled = false;
            option.classList.remove('text-secondary-400');
          });
        });

        // Reset color swatches
        form.querySelectorAll('.color-swatch-option').forEach(swatch => {
          swatch.classList.remove('ring-2', 'ring-primary-500', 'ring-offset-2', 'border-primary-500', 'opacity-40', 'cursor-not-allowed');
          swatch.classList.add('border-secondary-200');
          swatch.disabled = false;
          const selectedIndicator = swatch.querySelector('.selected-indicator');
          const unavailableIndicator = swatch.querySelector('.unavailable-indicator');
          if (selectedIndicator) selectedIndicator.classList.remove('opacity-100');
          if (selectedIndicator) selectedIndicator.classList.add('opacity-0');
          if (unavailableIndicator) unavailableIndicator.classList.add('hidden');
          if (unavailableIndicator) unavailableIndicator.classList.remove('flex');
        });

        // Reset color swatch labels
        form.querySelectorAll('.color-swatch-row .selected-value-label').forEach(label => {
          label.textContent = '';
        });

        // Reset button swatches
        form.querySelectorAll('.button-swatch-option').forEach(swatch => {
          swatch.classList.remove('border-primary-500', 'bg-primary-50', 'text-primary-700', 'opacity-40', 'cursor-not-allowed');
          swatch.classList.add('border-secondary-200', 'bg-white', 'text-secondary-700');
          swatch.disabled = false;
          const unavailableIndicator = swatch.querySelector('.unavailable-indicator');
          if (unavailableIndicator) unavailableIndicator.classList.add('hidden');
          if (unavailableIndicator) unavailableIndicator.classList.remove('flex');
        });

        if (resetLink) resetLink.style.display = 'none';
      }

      /**
       * Handle form submission with AJAX support
       */
      function handleSubmit(e) {
        e.preventDefault();

        if (!currentVariation || submitBtn.disabled) {
          return;
        }

        const buttonContent = submitBtn.querySelector('.button-content');
        const buttonLoading = submitBtn.querySelector('.button-loading');
        const buttonSuccess = submitBtn.querySelector('.button-success');

        if (buttonContent && buttonLoading) {
          buttonContent.classList.add('invisible');
          buttonLoading.classList.remove('hidden');
          buttonLoading.classList.add('flex');
          submitBtn.disabled = true;
        }

        // Get AJAX URL
        const wcAjaxUrl = window.wc_add_to_cart_params?.wc_ajax_url || window.wc_cart_fragments_params?.wc_ajax_url;
        if (!wcAjaxUrl) {
          // Fallback to regular form submission
          form.submit();
          return;
        }

        const productId = '{{ $productId }}';
        const variationId = variationIdInput.value;
        const quantity = quantityInput?.value || 1;

        // Build form data including variation attributes
        const formData = new URLSearchParams({
          product_id: productId,
          variation_id: variationId,
          quantity: quantity,
        });

        // Add variation attributes
        variationSelects.forEach(select => {
          formData.append(select.name, select.value);
        });

        fetch(wcAjaxUrl.replace('%%endpoint%%', 'add_to_cart'), {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
          },
          body: formData,
        })
        .then(response => response.json())
        .then(data => {
          if (data.error) {
            // Show error
            document.body.dispatchEvent(new CustomEvent('show-toast', {
              detail: { message: data.error, type: 'error' },
            }));

            // Reset button
            if (buttonContent && buttonLoading) {
              buttonContent.classList.remove('invisible');
              buttonLoading.classList.add('hidden');
              buttonLoading.classList.remove('flex');
              submitBtn.disabled = false;
            }
          } else {
            // Update fragments
            if (data.fragments) {
              for (const [selector, html] of Object.entries(data.fragments)) {
                document.querySelectorAll(selector).forEach(el => {
                  el.outerHTML = html;
                });
              }
            }

            // Show success state
            if (buttonLoading && buttonSuccess) {
              buttonLoading.classList.add('hidden');
              buttonLoading.classList.remove('flex');
              buttonSuccess.classList.remove('hidden');
              buttonSuccess.classList.add('flex');
            }

            // Trigger added_to_cart event
            document.body.dispatchEvent(new CustomEvent('added_to_cart', {
              detail: { productId, variationId, quantity, fragments: data.fragments },
            }));

            // Show success toast
            document.body.dispatchEvent(new CustomEvent('show-toast', {
              detail: { message: '{{ __("Product added to cart", "sage") }}', type: 'success' },
            }));

            // Reset button after delay
            setTimeout(() => {
              if (buttonContent && buttonSuccess) {
                buttonSuccess.classList.add('hidden');
                buttonSuccess.classList.remove('flex');
                buttonContent.classList.remove('invisible');
                submitBtn.disabled = false;
              }
            }, 2000);
          }
        })
        .catch(error => {
          console.error('Error adding to cart:', error);
          document.body.dispatchEvent(new CustomEvent('show-toast', {
            detail: { message: '{{ __("Could not add to cart. Please try again.", "sage") }}', type: 'error' },
          }));

          // Reset button
          if (buttonContent && buttonLoading) {
            buttonContent.classList.remove('invisible');
            buttonLoading.classList.add('hidden');
            buttonLoading.classList.remove('flex');
            submitBtn.disabled = false;
          }
        });
      }

      // Event Listeners

      // Quantity buttons
      if (minusBtn) {
        minusBtn.addEventListener('click', () => handleQuantityChange('decrease'));
      }
      if (plusBtn) {
        plusBtn.addEventListener('click', () => handleQuantityChange('increase'));
      }
      if (quantityInput) {
        quantityInput.addEventListener('change', validateQuantity);
        quantityInput.addEventListener('blur', validateQuantity);
      }

      // Keyboard support for quantity buttons
      [minusBtn, plusBtn].forEach(btn => {
        if (btn) {
          btn.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') {
              e.preventDefault();
              btn.click();
            }
          });
        }
      });

      // Variation selects
      variationSelects.forEach(select => {
        select.addEventListener('change', handleAttributeChange);
      });

      // Color swatch click handlers
      form.querySelectorAll('.color-swatch-option').forEach(swatch => {
        swatch.addEventListener('click', function() {
          if (this.disabled) return;

          const value = this.dataset.value;
          const row = this.closest('.color-swatch-row');
          const attrName = row?.dataset.attribute;
          const select = row?.querySelector('.variation-select');
          const label = row?.querySelector('.selected-value-label');

          if (!select || !attrName) return;

          // Update hidden select
          select.value = value;

          // Update visual state - remove selected state from siblings
          row.querySelectorAll('.color-swatch-option').forEach(s => {
            s.classList.remove('ring-2', 'ring-primary-500', 'ring-offset-2', 'border-primary-500');
            s.classList.add('border-secondary-200');
            const indicator = s.querySelector('.selected-indicator');
            if (indicator) {
              indicator.classList.remove('opacity-100');
              indicator.classList.add('opacity-0');
            }
          });

          // Add selected state to clicked swatch
          this.classList.add('ring-2', 'ring-primary-500', 'ring-offset-2', 'border-primary-500');
          this.classList.remove('border-secondary-200');
          const indicator = this.querySelector('.selected-indicator');
          if (indicator) {
            indicator.classList.add('opacity-100');
            indicator.classList.remove('opacity-0');
          }

          // Update the selected value label
          if (label) {
            label.textContent = '- ' + this.title;
          }

          // Trigger variation change
          handleAttributeChange();
        });
      });

      // Button swatch click handlers
      form.querySelectorAll('.button-swatch-option').forEach(swatch => {
        swatch.addEventListener('click', function() {
          if (this.disabled) return;

          const value = this.dataset.value;
          const row = this.closest('.button-swatch-row');
          const attrName = row?.dataset.attribute;
          const select = row?.querySelector('.variation-select');

          if (!select || !attrName) return;

          // Update hidden select
          select.value = value;

          // Update visual state - remove selected state from siblings
          row.querySelectorAll('.button-swatch-option').forEach(s => {
            s.classList.remove('border-primary-500', 'bg-primary-50', 'text-primary-700');
            s.classList.add('border-secondary-200', 'bg-white', 'text-secondary-700');
          });

          // Add selected state to clicked swatch
          this.classList.add('border-primary-500', 'bg-primary-50', 'text-primary-700');
          this.classList.remove('border-secondary-200', 'bg-white', 'text-secondary-700');

          // Trigger variation change
          handleAttributeChange();
        });
      });

      // Reset link
      if (resetLink) {
        resetLink.addEventListener('click', resetVariations);
      }

      // Form submit
      form.addEventListener('submit', handleSubmit);

      // WooCommerce jQuery compatibility
      if (typeof jQuery !== 'undefined') {
        jQuery(form).on('reset_data', function() {
          resetVariations();
        });
      }

      // Initialize
      updateQuantityButtons();

      // Initialize swatch states from selected values
      function initializeSwatchStates() {
        // Initialize color swatches
        form.querySelectorAll('.color-swatch-row').forEach(row => {
          const select = row.querySelector('.variation-select');
          const label = row.querySelector('.selected-value-label');
          if (!select || !select.value) return;

          const selectedSwatch = row.querySelector(`.color-swatch-option[data-value="${select.value}"]`);
          if (selectedSwatch) {
            selectedSwatch.classList.add('ring-2', 'ring-primary-500', 'ring-offset-2', 'border-primary-500');
            selectedSwatch.classList.remove('border-secondary-200');
            const indicator = selectedSwatch.querySelector('.selected-indicator');
            if (indicator) {
              indicator.classList.add('opacity-100');
              indicator.classList.remove('opacity-0');
            }
            if (label) {
              label.textContent = '- ' + selectedSwatch.title;
            }
          }
        });

        // Initialize button swatches
        form.querySelectorAll('.button-swatch-row').forEach(row => {
          const select = row.querySelector('.variation-select');
          if (!select || !select.value) return;

          const selectedSwatch = row.querySelector(`.button-swatch-option[data-value="${select.value}"]`);
          if (selectedSwatch) {
            selectedSwatch.classList.add('border-primary-500', 'bg-primary-50', 'text-primary-700');
            selectedSwatch.classList.remove('border-secondary-200', 'bg-white', 'text-secondary-700');
          }
        });
      }

      initializeSwatchStates();

      // If there are default selections, trigger change
      const hasDefaults = Array.from(variationSelects).some(select => select.value !== '');
      if (hasDefaults) {
        handleAttributeChange();
      }
    })();
  </script>

@else
  {{-- Out of Stock or Not Purchasable --}}
  <div class="out-of-stock-message rounded-lg border border-red-200 bg-red-50 p-4">
    <div class="flex items-center gap-3">
      <svg class="h-5 w-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
      </svg>
      <p class="text-sm font-medium text-red-800">
        @if (!$inStock)
          {{ __('This product is currently out of stock.', 'sage') }}
        @else
          {{ __('This product cannot be purchased.', 'sage') }}
        @endif
      </p>
    </div>
  </div>
@endif
