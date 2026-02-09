{{--
  Simple Product Add to Cart Form

  This template handles the add-to-cart functionality for simple products.
  It includes quantity input, stock validation, and form submission.

  @param int    $productId      - The product ID
  @param string $cartUrl        - The add to cart URL
  @param string $cartText       - The add to cart button text
  @param array  $quantityData   - Quantity input data (min, max, step, value)
  @param bool   $inStock        - Whether product is in stock
  @param bool   $purchasable    - Whether product is purchasable
  @param int    $stockQty       - Stock quantity (if managing stock)
  @param bool   $managingStock  - Whether stock is being managed
--}}

@php
  // Default values for variables passed via @include
  $productId = $productId ?? 0;
  $cartUrl = $cartUrl ?? '';
  $cartText = $cartText ?? __('Add to cart', 'sega-woo-theme');
  $quantityData = $quantityData ?? ['min' => 1, 'max' => '', 'step' => 1, 'value' => 1];
  $inStock = $inStock ?? true;
  $purchasable = $purchasable ?? true;
  $stockQty = $stockQty ?? null;
  $managingStock = $managingStock ?? false;
@endphp

@if ($purchasable && $inStock)
  <form
    action="{{ $cartUrl }}"
    method="post"
    class="add-to-cart-form simple-add-to-cart"
    data-product-id="{{ $productId }}"
    data-product-type="simple"
    enctype="multipart/form-data"
  >
    @php
      // Apply WooCommerce filters for extensibility
      do_action('woocommerce_before_add_to_cart_form');
    @endphp

    <div class="flex flex-col gap-4 sm:flex-row sm:flex-wrap sm:items-center">
      {{-- Quantity Input --}}
      @php
        do_action('woocommerce_before_add_to_cart_quantity');
      @endphp

      <div class="quantity-wrapper">
        <label for="quantity-{{ $productId }}" class="sr-only">
          {{ __('Quantity', 'sega-woo-theme') }}
        </label>

        <div class="group/qty inline-flex items-center gap-1.5 rounded-full bg-secondary-100/80 p-1.5 transition-all duration-200 hover:bg-secondary-100 hover:shadow-md">
          {{-- Decrease Button --}}
          <button
            type="button"
            class="quantity-btn quantity-minus flex h-10 w-10 items-center justify-center rounded-full bg-white text-secondary-500 shadow-sm ring-1 ring-secondary-200/50 transition-all duration-200 hover:bg-primary-50 hover:text-primary-600 hover:ring-primary-200 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-1 active:scale-95 disabled:cursor-not-allowed disabled:opacity-40 disabled:hover:bg-white disabled:hover:text-secondary-500 disabled:hover:ring-secondary-200/50 disabled:active:scale-100 sm:h-11 sm:w-11"
            aria-label="{{ __('Decrease quantity', 'sega-woo-theme') }}"
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
            class="quantity-input h-10 w-12 border-0 bg-transparent text-center text-base font-bold text-secondary-900 transition-colors focus:outline-none focus:ring-0 sm:h-11 sm:w-14 sm:text-lg [appearance:textfield] [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:appearance-none"
            aria-label="{{ __('Product quantity', 'sega-woo-theme') }}"
            aria-describedby="quantity-help-{{ $productId }}"
          />

          {{-- Increase Button --}}
          <button
            type="button"
            class="quantity-btn quantity-plus flex h-10 w-10 items-center justify-center rounded-full bg-white text-secondary-500 shadow-sm ring-1 ring-secondary-200/50 transition-all duration-200 hover:bg-primary-50 hover:text-primary-600 hover:ring-primary-200 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-1 active:scale-95 disabled:cursor-not-allowed disabled:opacity-40 disabled:hover:bg-white disabled:hover:text-secondary-500 disabled:hover:ring-secondary-200/50 disabled:active:scale-100 sm:h-11 sm:w-11"
            aria-label="{{ __('Increase quantity', 'sega-woo-theme') }}"
            data-action="increase"
          >
            <svg class="h-4 w-4 sm:h-5 sm:w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
            </svg>
          </button>
        </div>

        {{-- Quantity Help Text (Stock Info) --}}
        @if ($managingStock && $stockQty)
          <p id="quantity-help-{{ $productId }}" class="mt-2 text-xs text-secondary-500">
            {{ sprintf(__('Max: %d available', 'sega-woo-theme'), $stockQty) }}
          </p>
        @endif
      </div>

      @php
        do_action('woocommerce_after_add_to_cart_quantity');
      @endphp

      {{-- Hidden Product ID Field --}}
      <input type="hidden" name="add-to-cart" value="{{ $productId }}" />

      {{-- Add to Cart Button --}}
      @php
        do_action('woocommerce_before_add_to_cart_button');
      @endphp

      <button
        type="submit"
        name="add-to-cart"
        value="{{ $productId }}"
        class="add-to-cart-button single_add_to_cart_button button alt group relative flex flex-1 items-center justify-center gap-2.5 overflow-hidden rounded-xl bg-primary-600 px-10 py-4 text-base font-semibold text-white shadow-lg shadow-primary-600/25 transition-all duration-200 hover:bg-primary-700 hover:shadow-xl hover:shadow-primary-600/30 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 active:scale-[0.98] disabled:cursor-not-allowed disabled:opacity-50 disabled:shadow-none sm:flex-none"
      >
        {{-- Default State --}}
        <span class="button-content flex items-center gap-2">
          <svg class="h-5 w-5 transition-transform group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
          </svg>
          <span>{{ $cartText }}</span>
        </span>

        {{-- Loading State --}}
        <span class="button-loading absolute inset-0 hidden items-center justify-center bg-primary-600">
          <svg class="h-5 w-5 animate-spin text-white" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          <span class="ml-2">{{ __('Adding...', 'sega-woo-theme') }}</span>
        </span>

        {{-- Success State --}}
        <span class="button-success absolute inset-0 hidden items-center justify-center bg-green-600">
          <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
          </svg>
          <span class="ml-2">{{ __('Added!', 'sega-woo-theme') }}</span>
        </span>
      </button>

      @php
        do_action('woocommerce_after_add_to_cart_button');
      @endphp
    </div>

    @php
      do_action('woocommerce_after_add_to_cart_form');
    @endphp
  </form>

  {{-- Quantity Control Script --}}
  <script>
    (function() {
      const form = document.querySelector('[data-product-id="{{ $productId }}"].simple-add-to-cart');
      if (!form) return;

      const quantityInput = form.querySelector('.quantity-input');
      const minusBtn = form.querySelector('.quantity-minus');
      const plusBtn = form.querySelector('.quantity-plus');
      const submitBtn = form.querySelector('.add-to-cart-button');

      const min = parseInt(quantityInput.min) || 1;
      const max = parseInt(quantityInput.max) || 9999;
      const step = parseInt(quantityInput.step) || 1;

      // Update button states based on quantity
      function updateButtonStates() {
        const value = parseInt(quantityInput.value) || min;
        minusBtn.disabled = value <= min;
        plusBtn.disabled = max && value >= max;
      }

      // Handle quantity button clicks
      function handleQuantityChange(action) {
        let value = parseInt(quantityInput.value) || min;

        if (action === 'decrease') {
          value = Math.max(min, value - step);
        } else if (action === 'increase') {
          value = Math.min(max || 9999, value + step);
        }

        quantityInput.value = value;
        updateButtonStates();

        // Trigger change event for any listeners
        quantityInput.dispatchEvent(new Event('change', { bubbles: true }));
      }

      // Validate input on change
      function validateInput() {
        let value = parseInt(quantityInput.value);

        if (isNaN(value) || value < min) {
          value = min;
        } else if (max && value > max) {
          value = max;
        }

        // Round to nearest step
        value = Math.round((value - min) / step) * step + min;

        quantityInput.value = value;
        updateButtonStates();
      }

      // Event listeners
      minusBtn.addEventListener('click', () => handleQuantityChange('decrease'));
      plusBtn.addEventListener('click', () => handleQuantityChange('increase'));
      quantityInput.addEventListener('change', validateInput);
      quantityInput.addEventListener('blur', validateInput);

      // Keyboard support for quantity buttons
      [minusBtn, plusBtn].forEach(btn => {
        btn.addEventListener('keydown', (e) => {
          if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            btn.click();
          }
        });
      });

      // Form submission handling with AJAX support
      form.addEventListener('submit', function(e) {
        e.preventDefault();

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

        const productId = form.querySelector('input[name="add-to-cart"]')?.value || '{{ $productId }}';
        const quantity = quantityInput?.value || 1;

        fetch(wcAjaxUrl.replace('%%endpoint%%', 'add_to_cart'), {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
          },
          body: new URLSearchParams({
            product_id: productId,
            quantity: quantity,
          }),
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
              detail: { productId, quantity, fragments: data.fragments },
            }));

            // Show success toast
            document.body.dispatchEvent(new CustomEvent('show-toast', {
              detail: { message: '{{ __("Product added to cart", "sega-woo-theme") }}', type: 'success' },
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
            detail: { message: '{{ __("Could not add to cart. Please try again.", "sega-woo-theme") }}', type: 'error' },
          }));

          // Reset button
          if (buttonContent && buttonLoading) {
            buttonContent.classList.remove('invisible');
            buttonLoading.classList.add('hidden');
            buttonLoading.classList.remove('flex');
            submitBtn.disabled = false;
          }
        });
      });

      // Initialize button states
      updateButtonStates();
    })();
  </script>
@endif
