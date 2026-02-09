/**
 * Price Range Slider Component
 *
 * Dual-handle slider for filtering products by price range.
 * Uses Alpine.js for reactivity and vanilla JS for touch/mouse handling.
 * Supports AJAX filtering without page reload.
 */
export default function priceRangeSlider(config) {
  return {
    // Configuration
    absoluteMin: config.min || 0,
    absoluteMax: config.max || 1000,
    step: config.step || 1,
    currencySymbol: config.currency || '$',
    shopUrl: config.shopUrl || '',

    // Current values
    minValue: config.currentMin || config.min || 0,
    maxValue: config.currentMax || config.max || 1000,

    // UI State
    isDragging: null, // 'min', 'max', or null
    sliderWidth: 0,
    isLoading: false,
    // AJAX config
    ajaxUrl: config.ajaxUrl || (window.sageShopAjax?.ajaxUrl) || '',
    nonce: config.nonce || (window.sageShopAjax?.nonce) || '',
    useAjax: !!(config.ajaxUrl || window.sageShopAjax),

    // Computed percentages for positioning
    get minPercent() {
      return ((this.minValue - this.absoluteMin) / (this.absoluteMax - this.absoluteMin)) * 100;
    },

    get maxPercent() {
      return ((this.maxValue - this.absoluteMin) / (this.absoluteMax - this.absoluteMin)) * 100;
    },

    // Check if filter is active (different from absolute range)
    get isFiltered() {
      return this.minValue > this.absoluteMin || this.maxValue < this.absoluteMax;
    },

    init() {
      // Set initial values from config
      this.minValue = config.currentMin !== null ? config.currentMin : this.absoluteMin;
      this.maxValue = config.currentMax !== null ? config.currentMax : this.absoluteMax;

      // Update slider width on resize
      this.$nextTick(() => {
        this.updateSliderWidth();
      });

      window.addEventListener('resize', () => this.updateSliderWidth());
    },

    updateSliderWidth() {
      const track = this.$refs.track;
      if (track) {
        this.sliderWidth = track.offsetWidth;
      }
    },

    // Convert pixel position to value
    positionToValue(clientX) {
      const track = this.$refs.track;
      if (!track) return this.absoluteMin;

      const rect = track.getBoundingClientRect();
      const percent = Math.max(0, Math.min(1, (clientX - rect.left) / rect.width));
      const rawValue = this.absoluteMin + percent * (this.absoluteMax - this.absoluteMin);

      // Round to step
      return Math.round(rawValue / this.step) * this.step;
    },

    // Handle mouse/touch start on handle
    startDrag(handle, event) {
      event.preventDefault();
      this.isDragging = handle;

      const moveHandler = (e) => this.onDrag(e);
      const upHandler = () => {
        this.isDragging = null;
        document.removeEventListener('mousemove', moveHandler);
        document.removeEventListener('mouseup', upHandler);
        document.removeEventListener('touchmove', moveHandler);
        document.removeEventListener('touchend', upHandler);
      };

      document.addEventListener('mousemove', moveHandler);
      document.addEventListener('mouseup', upHandler);
      document.addEventListener('touchmove', moveHandler, { passive: false });
      document.addEventListener('touchend', upHandler);
    },

    // Handle drag movement
    onDrag(event) {
      if (!this.isDragging) return;

      event.preventDefault();
      const clientX = event.type.includes('touch') ? event.touches[0].clientX : event.clientX;
      const newValue = this.positionToValue(clientX);

      if (this.isDragging === 'min') {
        // Min handle: can't go above max - step
        this.minValue = Math.min(newValue, this.maxValue - this.step);
        this.minValue = Math.max(this.minValue, this.absoluteMin);
      } else {
        // Max handle: can't go below min + step
        this.maxValue = Math.max(newValue, this.minValue + this.step);
        this.maxValue = Math.min(this.maxValue, this.absoluteMax);
      }
    },

    // Handle click on track (jump to position)
    onTrackClick(event) {
      // Don't handle if clicking on a handle
      if (event.target.closest('[data-handle]')) return;

      const newValue = this.positionToValue(event.clientX);
      const distToMin = Math.abs(newValue - this.minValue);
      const distToMax = Math.abs(newValue - this.maxValue);

      // Move the closest handle
      if (distToMin <= distToMax) {
        this.minValue = Math.min(newValue, this.maxValue - this.step);
      } else {
        this.maxValue = Math.max(newValue, this.minValue + this.step);
      }
    },

    // Format price for display
    formatPrice(value) {
      return this.currencySymbol + value.toLocaleString();
    },

    // Apply filter (AJAX or redirect)
    applyFilter() {
      if (this.useAjax) {
        this.applyFilterAjax();
      } else {
        this.applyFilterRedirect();
      }
    },

    // Apply filter via AJAX
    async applyFilterAjax() {
      if (this.isLoading) return;

      this.isLoading = true;
      this.showLoading();

      try {
        const currentParams = new URLSearchParams(window.location.search);
        const formData = new FormData();

        formData.append('action', 'filter_products');
        formData.append('nonce', window.sageShopAjax.nonce);

        // Add price filter
        if (this.minValue > this.absoluteMin) {
          formData.append('min_price', this.minValue);
        }
        if (this.maxValue < this.absoluteMax) {
          formData.append('max_price', this.maxValue);
        }

        // Preserve categories
        if (currentParams.has('cat_ids')) {
          const cats = currentParams.get('cat_ids').split(',');
          cats.forEach(cat => formData.append('categories[]', cat.trim()));
        }

        // Preserve other filters
        if (currentParams.has('orderby')) {
          formData.append('orderby', currentParams.get('orderby'));
        }
        if (currentParams.has('per_page')) {
          formData.append('per_page', currentParams.get('per_page'));
        }
        if (currentParams.has('on_sale')) {
          formData.append('on_sale', currentParams.get('on_sale'));
        }
        if (currentParams.has('in_stock')) {
          formData.append('in_stock', currentParams.get('in_stock'));
        }

        // Reset to page 1
        formData.append('paged', '1');

        const response = await fetch(window.sageShopAjax.ajaxUrl, {
          method: 'POST',
          body: formData,
        });

        const data = await response.json();

        if (data.success) {
          this.updateProductsGrid(data.data);
          this.updateUrl();
          this.scrollToProducts();
        } else {
          this.applyFilterRedirect();
        }
      } catch (error) {
        console.error('Price filter AJAX error:', error);
        this.applyFilterRedirect();
      } finally {
        this.isLoading = false;
        this.hideLoading();
      }
    },

    // Apply filter via page redirect (fallback)
    applyFilterRedirect() {
      let url = new URL(this.shopUrl, window.location.origin);

      // Preserve existing query parameters
      const currentParams = new URLSearchParams(window.location.search);
      currentParams.forEach((value, key) => {
        if (key !== 'min_price' && key !== 'max_price') {
          url.searchParams.set(key, value);
        }
      });

      // Add price filter params
      if (this.minValue > this.absoluteMin) {
        url.searchParams.set('min_price', this.minValue);
      }
      if (this.maxValue < this.absoluteMax) {
        url.searchParams.set('max_price', this.maxValue);
      }

      window.location.href = url.toString();
    },

    // Clear filter (AJAX or redirect)
    clearFilter() {
      this.minValue = this.absoluteMin;
      this.maxValue = this.absoluteMax;

      if (this.useAjax) {
        this.applyFilterAjax();
      } else {
        let url = new URL(window.location.href);
        url.searchParams.delete('min_price');
        url.searchParams.delete('max_price');
        window.location.href = url.toString();
      }
    },

    // Update products grid with AJAX response
    updateProductsGrid(data) {
      const productsContainer = document.getElementById('shop-products-grid');
      if (productsContainer && data.products) {
        productsContainer.innerHTML = data.products;
      }

      const resultCount = document.getElementById('shop-result-count');
      if (resultCount && data.result_count) {
        resultCount.innerHTML = `<p class="text-sm text-secondary-600">${data.result_count}</p>`;
      }

      const pagination = document.getElementById('shop-pagination');
      if (pagination) {
        pagination.innerHTML = data.pagination || '';
      }
    },

    // Update browser URL
    updateUrl() {
      let url = new URL(this.shopUrl, window.location.origin);
      const currentParams = new URLSearchParams(window.location.search);

      currentParams.forEach((value, key) => {
        if (key !== 'min_price' && key !== 'max_price' && key !== 'paged') {
          url.searchParams.set(key, value);
        }
      });

      if (this.minValue > this.absoluteMin) {
        url.searchParams.set('min_price', this.minValue);
      }
      if (this.maxValue < this.absoluteMax) {
        url.searchParams.set('max_price', this.maxValue);
      }

      window.history.pushState({}, '', url.toString());
    },

    // Show loading overlay
    showLoading() {
      const overlay = document.getElementById('shop-loading-overlay');
      if (overlay) overlay.classList.remove('hidden');
    },

    // Hide loading overlay
    hideLoading() {
      const overlay = document.getElementById('shop-loading-overlay');
      if (overlay) overlay.classList.add('hidden');
    },

    // Scroll to products
    scrollToProducts() {
      const productsGrid = document.getElementById('shop-products-grid');
      if (productsGrid) {
        const offset = 100;
        const top = productsGrid.getBoundingClientRect().top + window.pageYOffset - offset;
        window.scrollTo({ top, behavior: 'smooth' });
      }
    },

    // Reset to full range (without navigating)
    resetRange() {
      this.minValue = this.absoluteMin;
      this.maxValue = this.absoluteMax;
    }
  };
}
