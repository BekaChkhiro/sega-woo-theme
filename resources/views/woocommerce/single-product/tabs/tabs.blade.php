{{--
  Product Tabs Component

  Displays tabbed content for product description, additional information, and reviews.

  @param array $tabs - Array of tab data with keys: description, additional_information, reviews
  @param bool $hasDescription - Whether product has a description
  @param bool $hasAdditionalInfo - Whether product has additional info (attributes, weight, dimensions)
  @param bool $reviewsEnabled - Whether reviews are enabled for this product
  @param string $description - Product full description HTML
  @param array $visibleAttributes - Array of visible product attributes
  @param string $weight - Product weight with unit
  @param string $dimensions - Product dimensions formatted string
  @param bool $hasWeight - Whether product has weight
  @param bool $hasDimensions - Whether product has dimensions
  @param int $reviewCount - Number of reviews
--}}

@php
  $productTabs = [];

  if ($hasDescription) {
    $productTabs['description'] = [
      'title' => __('Description', 'sage'),
      'priority' => 10,
    ];
  }

  if ($hasAdditionalInfo) {
    $productTabs['additional_information'] = [
      'title' => __('Additional information', 'sage'),
      'priority' => 20,
    ];
  }

  if ($reviewsEnabled) {
    $productTabs['reviews'] = [
      'title' => sprintf(__('Reviews (%d)', 'sage'), $reviewCount),
      'priority' => 30,
    ];
  }

  // Sort tabs by priority
  uasort($productTabs, function($a, $b) {
    return ($a['priority'] ?? 10) <=> ($b['priority'] ?? 10);
  });
@endphp

@if (!empty($productTabs))
  <div class="product-tabs mt-16 pt-16" id="product-tabs">
    {{-- Section Title --}}
    <h2 class="sr-only">{{ __('Product Details', 'sage') }}</h2>

    {{-- Tab Navigation --}}
    <div class="border-b border-secondary-200">
      <nav class="-mb-px flex gap-1 overflow-x-auto scrollbar-hide sm:gap-2" aria-label="{{ __('Product information tabs', 'sage') }}">
        @foreach ($productTabs as $key => $tab)
          <button
            type="button"
            class="product-tab-button group relative whitespace-nowrap rounded-t-lg px-6 py-4 text-sm font-semibold transition-all duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:ring-offset-2 {{ $loop->first ? 'bg-white text-primary-600 shadow-sm' : 'text-secondary-500 hover:bg-secondary-50 hover:text-secondary-700' }}"
            data-tab="{{ $key }}"
            aria-selected="{{ $loop->first ? 'true' : 'false' }}"
            aria-controls="tab-{{ $key }}"
            role="tab"
            id="tab-button-{{ $key }}"
          >
            {{ $tab['title'] }}
            <span class="absolute inset-x-0 -bottom-px h-0.5 transition-colors {{ $loop->first ? 'bg-primary-500' : 'bg-transparent group-hover:bg-secondary-300' }}"></span>
          </button>
        @endforeach
      </nav>
    </div>

    {{-- Tab Panels --}}
    <div class="tab-panels rounded-b-xl border-x border-b border-secondary-200 bg-white p-6 shadow-sm sm:p-8">
      {{-- Description Tab --}}
      @if ($hasDescription)
        <div
          id="tab-description"
          class="product-tab-panel"
          role="tabpanel"
          aria-labelledby="tab-button-description"
          tabindex="0"
        >
          @include('woocommerce.single-product.tabs.description', ['description' => $description])
        </div>
      @endif

      {{-- Additional Information Tab --}}
      @if ($hasAdditionalInfo)
        <div
          id="tab-additional_information"
          class="product-tab-panel {{ $hasDescription ? 'hidden' : '' }}"
          role="tabpanel"
          aria-labelledby="tab-button-additional_information"
          tabindex="0"
        >
          @include('woocommerce.single-product.tabs.additional-information', [
            'attributes' => $visibleAttributes,
            'weight' => $weight,
            'dimensions' => $dimensions,
            'hasWeight' => $hasWeight,
            'hasDimensions' => $hasDimensions,
          ])
        </div>
      @endif

      {{-- Reviews Tab --}}
      @if ($reviewsEnabled)
        <div
          id="tab-reviews"
          class="product-tab-panel {{ $hasDescription || $hasAdditionalInfo ? 'hidden' : '' }}"
          role="tabpanel"
          aria-labelledby="tab-button-reviews"
          tabindex="0"
        >
          @include('woocommerce.single-product.tabs.reviews')
        </div>
      @endif
    </div>
  </div>

  {{-- Tab Switching JavaScript --}}
  <script>
    (function() {
      const tabContainer = document.getElementById('product-tabs');
      if (!tabContainer) return;

      const tabButtons = tabContainer.querySelectorAll('.product-tab-button');
      const tabPanels = tabContainer.querySelectorAll('.product-tab-panel');

      function activateTab(targetKey) {
        // Update button states
        tabButtons.forEach(function(btn) {
          const isActive = btn.dataset.tab === targetKey;
          const indicator = btn.querySelector('span');

          // Toggle active classes
          btn.classList.toggle('bg-white', isActive);
          btn.classList.toggle('text-primary-600', isActive);
          btn.classList.toggle('shadow-sm', isActive);
          btn.classList.toggle('text-secondary-500', !isActive);

          // Update indicator
          if (indicator) {
            indicator.classList.toggle('bg-primary-500', isActive);
            indicator.classList.toggle('bg-transparent', !isActive);
          }

          btn.setAttribute('aria-selected', isActive ? 'true' : 'false');
        });

        // Update panel visibility
        tabPanels.forEach(function(panel) {
          const isTarget = panel.id === 'tab-' + targetKey;
          panel.classList.toggle('hidden', !isTarget);
        });

        // Update URL hash without scrolling
        if (history.replaceState) {
          history.replaceState(null, null, '#' + targetKey);
        }
      }

      // Click handlers
      tabButtons.forEach(function(button) {
        button.addEventListener('click', function() {
          activateTab(this.dataset.tab);
        });

        // Keyboard navigation
        button.addEventListener('keydown', function(e) {
          const tabs = Array.from(tabButtons);
          const currentIndex = tabs.indexOf(this);
          let newIndex = currentIndex;

          if (e.key === 'ArrowLeft' || e.key === 'ArrowUp') {
            newIndex = currentIndex > 0 ? currentIndex - 1 : tabs.length - 1;
            e.preventDefault();
          } else if (e.key === 'ArrowRight' || e.key === 'ArrowDown') {
            newIndex = currentIndex < tabs.length - 1 ? currentIndex + 1 : 0;
            e.preventDefault();
          } else if (e.key === 'Home') {
            newIndex = 0;
            e.preventDefault();
          } else if (e.key === 'End') {
            newIndex = tabs.length - 1;
            e.preventDefault();
          }

          if (newIndex !== currentIndex) {
            tabs[newIndex].focus();
            activateTab(tabs[newIndex].dataset.tab);
          }
        });
      });

      // Handle URL hash on page load
      function handleHash() {
        const hash = window.location.hash.replace('#', '');
        if (hash) {
          const targetButton = tabContainer.querySelector('[data-tab="' + hash + '"]');
          if (targetButton) {
            activateTab(hash);
          }
        }
      }

      // Run on DOM ready
      if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', handleHash);
      } else {
        handleHash();
      }

      // Handle hash changes (e.g., clicking anchor links)
      window.addEventListener('hashchange', handleHash);
    })();
  </script>
@endif
