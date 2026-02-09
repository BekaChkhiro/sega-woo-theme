/**
 * Unified Shop Filters Component
 *
 * Handles all shop filtering with AJAX:
 * - Category multi-select (staged, applied on button click)
 * - Price range (staged, applied on button click)
 * - On Sale filter
 * - In Stock filter
 * - Sorting (orderby)
 * - Products per page
 * - Pagination
 *
 * Changes are staged until user clicks "Apply" button.
 * Single source of truth for all filter state with unified AJAX requests.
 */
export default function shopFilters(config = {}) {
  return {
    // Configuration
    shopUrl: config.shopUrl || (window.sageShopAjax?.shopUrl) || '',
    ajaxUrl: config.ajaxUrl || (window.sageShopAjax?.ajaxUrl) || '',
    nonce: config.nonce || (window.sageShopAjax?.nonce) || '',

    // Category page context (with fallback to window.sageShopAjax)
    isCategoryPage: config.isCategoryPage ?? (window.sageShopAjax?.isCategoryPage) ?? false,
    parentCategoryId: config.parentCategoryId || (window.sageShopAjax?.categoryId) || null,
    parentCategoryUrl: config.parentCategoryUrl || (window.sageShopAjax?.categoryUrl) || '',

    // ==================== APPLIED STATE (current active filters) ====================
    // Category state
    categories: config.initialCategories || [],
    categoryTree: config.categoryTree || {},
    expandedParents: {},

    // Price state
    priceMin: config.priceMin || 0,
    priceMax: config.priceMax || 1000,
    currentMinPrice: config.currentMinPrice,
    currentMaxPrice: config.currentMaxPrice,
    priceStep: config.priceStep || 1,
    currencySymbol: config.currencySymbol || '$',

    // Availability state
    onSale: config.onSale || false,
    inStock: config.inStock || false,

    // Sorting & pagination state
    orderby: config.orderby || 'menu_order',
    perPage: config.perPage || 12,
    currentPage: 1,
    totalPages: config.totalPages || 1,

    // ==================== STAGED STATE (pending changes, not yet applied) ====================
    stagedCategories: [],
    stagedMinPrice: null,
    stagedMaxPrice: null,

    // UI state
    isLoading: false,
    mobileOpen: false,

    // Price slider drag state
    isDragging: null,

    /**
     * Initialize the component
     */
    init() {
      // Parse URL parameters on init
      this.parseUrlParams();

      // Set initial price values
      if (this.currentMinPrice === null || this.currentMinPrice === undefined) {
        this.currentMinPrice = this.priceMin;
      }
      if (this.currentMaxPrice === null || this.currentMaxPrice === undefined) {
        this.currentMaxPrice = this.priceMax;
      }

      // Initialize staged state from applied state FIRST
      this.resetStagedState();

      // THEN initialize expanded state (so it can use stagedCategories)
      this.initializeExpandedState();

      // Handle browser back/forward
      window.addEventListener('popstate', () => {
        this.parseUrlParams();
        this.resetStagedState();
        this.initializeExpandedState();
        this.applyFilters();
      });

      // Auto-expand sidebar on desktop
      if (window.innerWidth >= 1024) {
        this.mobileOpen = true;
      }

      // Listen for resize
      window.addEventListener('resize', () => {
        if (window.innerWidth >= 1024) {
          this.mobileOpen = true;
        }
      });

      // Expose methods globally for other components to use
      window.shopFiltersAPI = {
        changeOrderby: (value) => this.changeOrderby(value),
        changePerPage: (value) => this.changePerPage(value),
        goToPage: (page) => this.goToPage(page),
        getState: () => ({
          categories: this.categories,
          orderby: this.orderby,
          perPage: this.perPage,
          currentPage: this.currentPage,
          totalPages: this.totalPages
        })
      };
    },

    /**
     * Reset staged state to match applied state
     */
    resetStagedState() {
      // Copy and deduplicate (IDs are already clean integers)
      this.stagedCategories = [...new Set(this.categories)];
      this.stagedMinPrice = this.currentMinPrice;
      this.stagedMaxPrice = this.currentMaxPrice;
    },

    /**
     * Check if there are pending (staged) changes
     */
    get hasPendingChanges() {
      // Check categories
      const categoriesChanged =
        this.stagedCategories.length !== this.categories.length ||
        !this.stagedCategories.every(cat => this.categories.includes(cat));

      // Check price
      const priceChanged =
        this.stagedMinPrice !== this.currentMinPrice ||
        this.stagedMaxPrice !== this.currentMaxPrice;

      return categoriesChanged || priceChanged;
    },

    /**
     * Check if any filter is active (applied)
     */
    get hasActiveFilters() {
      return this.categories.length > 0 ||
        this.currentMinPrice > this.priceMin ||
        this.currentMaxPrice < this.priceMax ||
        this.onSale ||
        this.inStock;
    },

    /**
     * Check if any staged filter is set (for showing clear button)
     */
    get hasStagedFilters() {
      return this.stagedCategories.length > 0 ||
        this.stagedMinPrice > this.priceMin ||
        this.stagedMaxPrice < this.priceMax;
    },

    /**
     * Parse URL parameters into state
     */
    parseUrlParams() {
      const params = new URLSearchParams(window.location.search);

      // Categories - parse as IDs (integers)
      const productCat = params.get('cat_ids');
      if (productCat) {
        const ids = productCat.split(',')
          .map(s => parseInt(s.trim(), 10))
          .filter(id => id > 0);
        // Deduplicate using Set
        this.categories = [...new Set(ids)];
      } else {
        // Keep initial categories if no URL param
        // (don't reset to empty array)
      }

      // Price
      const minPrice = params.get('min_price');
      const maxPrice = params.get('max_price');
      this.currentMinPrice = minPrice !== null ? parseFloat(minPrice) : this.priceMin;
      this.currentMaxPrice = maxPrice !== null ? parseFloat(maxPrice) : this.priceMax;

      // Availability
      this.onSale = params.get('on_sale') === '1';
      this.inStock = params.get('in_stock') === '1';

      // Sorting
      this.orderby = params.get('orderby') || 'menu_order';
      this.perPage = parseInt(params.get('per_page') || '12', 10);
      this.currentPage = parseInt(params.get('paged') || '1', 10);
    },

    /**
     * Ensure value is a valid category ID
     */
    toValidId(val) {
      const id = parseInt(val, 10);
      return id > 0 ? id : null;
    },

    // ==================== STAGED CATEGORY METHODS ====================

    /**
     * Check if a category is staged (pending selection)
     */
    isCategoryStaged(slug) {
      return this.stagedCategories.includes(slug);
    },

    /**
     * Toggle category in staged state (doesn't apply immediately)
     */
    toggleStagedCategory(catId, checked, isParent = false) {
      const id = this.toValidId(catId);
      if (!id) return;

      if (checked) {
        if (!this.stagedCategories.includes(id)) {
          this.stagedCategories.push(id);
        }
        // Auto-expand parent
        if (isParent || this.categoryTree[id]) {
          this.expandedParents[id] = true;
        } else {
          const parentId = this.getParentId(id);
          if (parentId) {
            this.expandedParents[parentId] = true;
          }
        }
      } else {
        this.stagedCategories = this.stagedCategories.filter(c => c !== id);
        // Collapse if no children selected
        if (isParent || this.categoryTree[id]) {
          const childIds = this.categoryTree[id] || [];
          const hasSelectedChild = childIds.some(child => this.stagedCategories.includes(child));
          if (!hasSelectedChild) {
            this.expandedParents[id] = false;
          }
        }
      }
    },

    /**
     * Clear staged categories (select "All Products")
     */
    clearStagedCategories() {
      this.stagedCategories = [];
    },

    /**
     * Initialize expanded state based on selected categories
     */
    initializeExpandedState() {
      this.expandedParents = {};

      // Use staged categories if available, otherwise use applied categories
      const activeCategories = this.stagedCategories.length > 0 ? this.stagedCategories : this.categories;

      Object.keys(this.categoryTree).forEach(parentId => {
        const pid = parseInt(parentId, 10);
        const childIds = this.categoryTree[parentId] || [];

        // Check if parent is selected
        if (activeCategories.includes(pid)) {
          this.expandedParents[pid] = true;
          return;
        }

        // Check if any child is selected - if so, expand parent
        const hasSelectedChild = childIds.some(childId => activeCategories.includes(childId));

        if (hasSelectedChild) {
          this.expandedParents[pid] = true;
        }
      });
    },

    /**
     * Check if parent is expanded
     */
    isParentExpanded(parentSlug) {
      return this.expandedParents[parentSlug] === true;
    },

    /**
     * Toggle parent expansion
     */
    toggleExpanded(parentSlug) {
      this.expandedParents[parentSlug] = !this.expandedParents[parentSlug];
    },

    /**
     * Get parent ID for a child category
     */
    getParentId(childId) {
      for (const [parentId, children] of Object.entries(this.categoryTree)) {
        if (children.includes(childId)) {
          return parseInt(parentId, 10);
        }
      }
      return null;
    },

    // ==================== STAGED PRICE METHODS ====================

    /**
     * Price slider percentages (using staged values)
     */
    get minPercent() {
      return ((this.stagedMinPrice - this.priceMin) / (this.priceMax - this.priceMin)) * 100;
    },

    get maxPercent() {
      return ((this.stagedMaxPrice - this.priceMin) / (this.priceMax - this.priceMin)) * 100;
    },

    get isPriceFiltered() {
      return this.stagedMinPrice > this.priceMin || this.stagedMaxPrice < this.priceMax;
    },

    /**
     * Convert position to price value
     */
    positionToPrice(clientX, track) {
      if (!track) return this.priceMin;
      const rect = track.getBoundingClientRect();
      const percent = Math.max(0, Math.min(1, (clientX - rect.left) / rect.width));
      const rawValue = this.priceMin + percent * (this.priceMax - this.priceMin);
      return Math.round(rawValue / this.priceStep) * this.priceStep;
    },

    /**
     * Start dragging price handle
     */
    startPriceDrag(handle, event) {
      event.preventDefault();
      this.isDragging = handle;

      const moveHandler = (e) => this.onPriceDrag(e);
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

    /**
     * Handle price drag movement (updates staged values)
     */
    onPriceDrag(event) {
      if (!this.isDragging) return;
      event.preventDefault();

      const track = this.$refs.priceTrack;
      if (!track) return;

      const clientX = event.type.includes('touch') ? event.touches[0].clientX : event.clientX;
      const newValue = this.positionToPrice(clientX, track);

      if (this.isDragging === 'min') {
        this.stagedMinPrice = Math.min(newValue, this.stagedMaxPrice - this.priceStep);
        this.stagedMinPrice = Math.max(this.stagedMinPrice, this.priceMin);
      } else {
        this.stagedMaxPrice = Math.max(newValue, this.stagedMinPrice + this.priceStep);
        this.stagedMaxPrice = Math.min(this.stagedMaxPrice, this.priceMax);
      }
    },

    /**
     * Handle click on price track (updates staged values)
     */
    onPriceTrackClick(event) {
      if (event.target.closest('[data-handle]')) return;

      const track = this.$refs.priceTrack;
      if (!track) return;

      const newValue = this.positionToPrice(event.clientX, track);
      const distToMin = Math.abs(newValue - this.stagedMinPrice);
      const distToMax = Math.abs(newValue - this.stagedMaxPrice);

      if (distToMin <= distToMax) {
        this.stagedMinPrice = Math.min(newValue, this.stagedMaxPrice - this.priceStep);
      } else {
        this.stagedMaxPrice = Math.max(newValue, this.stagedMinPrice + this.priceStep);
      }
    },

    // ==================== AVAILABILITY METHODS (instant apply) ====================

    /**
     * Toggle on sale filter (applies immediately)
     */
    toggleOnSale(checked) {
      this.onSale = checked;
      this.currentPage = 1;
      this.applyFilters();
    },

    /**
     * Toggle in stock filter (applies immediately)
     */
    toggleInStock(checked) {
      this.inStock = checked;
      this.currentPage = 1;
      this.applyFilters();
    },

    // ==================== SORTING & PAGINATION METHODS ====================

    /**
     * Change sort order
     */
    changeOrderby(value) {
      this.orderby = value;
      this.currentPage = 1;
      this.applyFilters();
    },

    /**
     * Change products per page
     */
    changePerPage(value) {
      this.perPage = parseInt(value, 10);
      this.currentPage = 1;
      this.applyFilters();
    },

    /**
     * Go to specific page
     */
    goToPage(page) {
      this.currentPage = page;
      this.applyFilters();
    },

    // ==================== APPLY/CLEAR BUTTON METHODS ====================

    /**
     * Apply staged changes (called when user clicks "Apply" button)
     */
    applyStagedFilters() {
      // Move staged state to applied state (IDs are already clean integers)
      this.categories = [...new Set(this.stagedCategories)];
      this.currentMinPrice = this.stagedMinPrice;
      this.currentMaxPrice = this.stagedMaxPrice;
      this.currentPage = 1;

      // Re-initialize expanded state to show selected subcategories
      this.initializeExpandedState();

      // Apply via AJAX
      this.applyFilters();
    },

    /**
     * Clear all staged filters (reset to defaults)
     */
    clearStagedFilters() {
      this.stagedCategories = [];
      this.stagedMinPrice = this.priceMin;
      this.stagedMaxPrice = this.priceMax;
      this.expandedParents = {};
    },

    /**
     * Clear all filters and apply immediately
     * On category pages, this keeps user within the category
     */
    clearAllFilters() {
      this.categories = [];
      this.stagedCategories = [];
      this.currentMinPrice = this.priceMin;
      this.currentMaxPrice = this.priceMax;
      this.stagedMinPrice = this.priceMin;
      this.stagedMaxPrice = this.priceMax;
      this.onSale = false;
      this.inStock = false;
      this.orderby = 'menu_order';
      this.perPage = 12;
      this.currentPage = 1;
      this.expandedParents = {};

      // On category pages, apply filters to stay within category
      // On shop page, redirect to clean shop URL
      if (this.isCategoryPage) {
        this.applyFilters();
      } else {
        // Redirect to clean shop URL
        window.location.href = this.shopUrl || '/shop/';
      }
    },

    // ==================== AJAX METHODS ====================

    /**
     * Apply all filters via AJAX
     */
    async applyFilters() {
      if (this.isLoading) return;

      // Fallback to redirect if AJAX not configured
      if (!this.ajaxUrl || !this.nonce) {
        this.applyFiltersRedirect();
        return;
      }

      this.isLoading = true;
      this.showLoading();

      try {
        const formData = new FormData();
        formData.append('action', 'filter_products');
        formData.append('nonce', this.nonce);

        // Categories - send as IDs
        // On category pages, always include parent category context
        if (this.isCategoryPage && this.parentCategoryId) {
          if (this.categories.length > 0) {
            // User selected specific subcategories - send those
            const uniqueCategories = [...new Set(this.categories)];
            uniqueCategories.forEach(catId => formData.append('categories[]', catId));
          } else {
            // "All in [Category]" selected - filter by parent category
            formData.append('categories[]', this.parentCategoryId);
          }
        } else if (this.categories.length > 0) {
          // On shop page with category selection
          const uniqueCategories = [...new Set(this.categories)];
          uniqueCategories.forEach(catId => formData.append('categories[]', catId));
        }

        // Price
        if (this.currentMinPrice > this.priceMin) {
          formData.append('min_price', this.currentMinPrice);
        }
        if (this.currentMaxPrice < this.priceMax) {
          formData.append('max_price', this.currentMaxPrice);
        }

        // Availability
        if (this.onSale) {
          formData.append('on_sale', '1');
        }
        if (this.inStock) {
          formData.append('in_stock', '1');
        }

        // Sorting & pagination
        formData.append('orderby', this.orderby);
        formData.append('per_page', this.perPage);
        formData.append('paged', this.currentPage);

        const response = await fetch(this.ajaxUrl, {
          method: 'POST',
          body: formData,
        });

        const data = await response.json();

        if (data.success) {
          this.updateDOM(data.data);
          this.updateUrl();
          this.scrollToProducts();
        } else {
          this.applyFiltersRedirect();
        }
      } catch (error) {
        this.applyFiltersRedirect();
      } finally {
        this.isLoading = false;
        this.hideLoading();
      }
    },

    /**
     * Fallback: apply filters via page redirect
     */
    applyFiltersRedirect() {
      // On category pages, always stay on the current page URL
      // On shop page, use shop URL
      let baseUrl = this.shopUrl || window.location.origin + '/shop/';
      if (this.isCategoryPage) {
        // Use parent category URL if available, otherwise stay on current page
        baseUrl = this.parentCategoryUrl || window.location.pathname;
      }
      const url = new URL(baseUrl, window.location.origin);

      // On category pages, add subcategory filter as cat_ids if selected
      if (this.categories.length > 0) {
        const uniqueCategories = [...new Set(this.categories)];
        url.searchParams.set('cat_ids', uniqueCategories.join(','));
      }
      if (this.currentMinPrice > this.priceMin) {
        url.searchParams.set('min_price', this.currentMinPrice);
      }
      if (this.currentMaxPrice < this.priceMax) {
        url.searchParams.set('max_price', this.currentMaxPrice);
      }
      if (this.onSale) {
        url.searchParams.set('on_sale', '1');
      }
      if (this.inStock) {
        url.searchParams.set('in_stock', '1');
      }
      if (this.orderby !== 'menu_order') {
        url.searchParams.set('orderby', this.orderby);
      }
      if (this.perPage !== 12) {
        url.searchParams.set('per_page', this.perPage);
      }
      if (this.currentPage > 1) {
        url.searchParams.set('paged', this.currentPage);
      }

      window.location.href = url.toString();
    },

    /**
     * Update DOM with AJAX response
     */
    updateDOM(data) {
      // Update products grid
      const productsContainer = document.getElementById('shop-products-grid');
      if (productsContainer && data.products) {
        productsContainer.innerHTML = data.products;
      }

      // Update result count
      const resultCount = document.getElementById('shop-result-count');
      if (resultCount && data.result_count) {
        resultCount.innerHTML = `<p class="text-sm text-secondary-600">${data.result_count}</p>`;
      }

      // Update pagination state from response
      if (data.total_pages !== undefined) {
        this.totalPages = parseInt(data.total_pages, 10);
      }
      if (data.current_page !== undefined) {
        this.currentPage = parseInt(data.current_page, 10);
      }

      // Update pagination HTML
      const pagination = document.getElementById('shop-pagination');
      if (pagination) {
        pagination.innerHTML = data.pagination || '';
        this.attachPaginationHandlers();
      }

      // Update active filters section
      this.updateActiveFilters(data.active_filters || []);
    },

    /**
     * Update active filters display
     */
    updateActiveFilters(filters) {
      let container = document.getElementById('shop-active-filters');

      // If no container exists, try to find a place to insert it
      if (!container) {
        const pageHeader = document.querySelector('.page-header, [class*="page-header"], .mb-8');
        if (pageHeader && filters.length > 0) {
          // Create the container
          container = document.createElement('div');
          container.id = 'shop-active-filters';
          pageHeader.appendChild(container);
        }
      }

      if (!container) return;

      if (filters.length === 0) {
        container.innerHTML = '';
        container.classList.add('hidden');
        return;
      }

      container.classList.remove('hidden');

      const getIcon = (type) => {
        switch (type) {
          case 'category':
            return '<svg class="h-3.5 w-3.5 text-primary-500 group-hover:text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" /></svg>';
          case 'price':
            return '<svg class="h-3.5 w-3.5 text-green-500 group-hover:text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>';
          default:
            return '<svg class="h-3.5 w-3.5 text-secondary-400 group-hover:text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>';
        }
      };

      const filterItems = filters.map(filter => `
        <button
          type="button"
          class="active-filter-btn group inline-flex items-center gap-1.5 rounded-full bg-white px-3 py-1.5 text-sm font-medium text-secondary-700 shadow-sm ring-1 ring-secondary-200 transition-all hover:bg-red-50 hover:text-red-700 hover:ring-red-200"
          data-filter-type="${filter.type}"
          data-filter-id="${filter.id || ''}"
        >
          ${getIcon(filter.type)}
          <span>${filter.label}</span>
          <svg class="h-3.5 w-3.5 text-secondary-400 transition-colors group-hover:text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      `).join('');

      container.innerHTML = `
        <div class="mt-6 rounded-xl bg-gradient-to-r from-secondary-50 to-secondary-100/50 p-4">
          <div class="flex flex-wrap items-center gap-3">
            <div class="flex items-center gap-2 text-sm font-medium text-secondary-600">
              <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
              </svg>
              <span>Filters:</span>
            </div>
            <div class="flex flex-wrap items-center gap-2">
              ${filterItems}
            </div>
            ${filters.length > 1 ? `
              <button
                type="button"
                class="clear-all-filters-btn ml-auto inline-flex items-center gap-1.5 rounded-full bg-secondary-200/50 px-3 py-1.5 text-sm font-medium text-secondary-600 transition-all hover:bg-secondary-200 hover:text-secondary-800"
              >
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                Clear all
              </button>
            ` : ''}
          </div>
        </div>
      `;

      // Attach event listeners
      this.attachActiveFilterHandlers(container);
    },

    /**
     * Attach click handlers to active filter buttons
     */
    attachActiveFilterHandlers(container) {
      // Individual filter remove buttons
      container.querySelectorAll('.active-filter-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
          e.preventDefault();
          const type = btn.dataset.filterType;
          const id = btn.dataset.filterId ? parseInt(btn.dataset.filterId, 10) : null;
          this.removeActiveFilter(type, id);
        });
      });

      // Clear all button
      const clearAllBtn = container.querySelector('.clear-all-filters-btn');
      if (clearAllBtn) {
        clearAllBtn.addEventListener('click', (e) => {
          e.preventDefault();
          this.clearAllFilters();
        });
      }
    },

    /**
     * Remove a single active filter
     */
    removeActiveFilter(type, id) {
      switch (type) {
        case 'category':
          if (id) {
            this.categories = this.categories.filter(c => c !== id);
            this.stagedCategories = this.stagedCategories.filter(c => c !== id);
          }
          break;
        case 'price':
          this.currentMinPrice = this.priceMin;
          this.currentMaxPrice = this.priceMax;
          this.stagedMinPrice = this.priceMin;
          this.stagedMaxPrice = this.priceMax;
          break;
        case 'on_sale':
          this.onSale = false;
          break;
        case 'in_stock':
          this.inStock = false;
          break;
      }
      this.currentPage = 1;
      this.applyFilters();
    },

    /**
     * Attach click handlers to pagination links
     */
    attachPaginationHandlers() {
      const pagination = document.getElementById('shop-pagination');
      if (!pagination) return;

      // Handle new styled pagination buttons
      pagination.querySelectorAll('.pagination-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
          e.preventDefault();
          const page = parseInt(btn.dataset.page, 10);
          if (page && page > 0 && page <= this.totalPages) {
            this.goToPage(page);
          }
        });
      });

      // Fallback for old-style pagination links
      pagination.querySelectorAll('a.page-numbers').forEach(link => {
        link.addEventListener('click', (e) => {
          e.preventDefault();
          const href = link.getAttribute('href');
          if (href) {
            const url = new URL(href, window.location.origin);
            const page = parseInt(url.searchParams.get('paged') || '1', 10);
            this.goToPage(page);
          }
        });
      });
    },

    /**
     * Update browser URL
     */
    updateUrl() {
      // On category pages, always stay on the current page URL
      let baseUrl = this.shopUrl || window.location.pathname;
      if (this.isCategoryPage) {
        // Use parent category URL if available, otherwise stay on current page
        baseUrl = this.parentCategoryUrl || window.location.pathname;
      }
      const url = new URL(baseUrl, window.location.origin);

      // On category pages, add subcategory filter as cat_ids if selected
      if (this.categories.length > 0) {
        const uniqueCategories = [...new Set(this.categories)];
        url.searchParams.set('cat_ids', uniqueCategories.join(','));
      }
      if (this.currentMinPrice > this.priceMin) {
        url.searchParams.set('min_price', this.currentMinPrice);
      }
      if (this.currentMaxPrice < this.priceMax) {
        url.searchParams.set('max_price', this.currentMaxPrice);
      }
      if (this.onSale) {
        url.searchParams.set('on_sale', '1');
      }
      if (this.inStock) {
        url.searchParams.set('in_stock', '1');
      }
      if (this.orderby !== 'menu_order') {
        url.searchParams.set('orderby', this.orderby);
      }
      if (this.perPage !== 12) {
        url.searchParams.set('per_page', this.perPage);
      }
      if (this.currentPage > 1) {
        url.searchParams.set('paged', this.currentPage);
      }

      window.history.pushState({}, '', url.toString());

      // Dispatch event so other components can sync
      window.dispatchEvent(new CustomEvent('shop-filters-updated', {
        detail: {
          categories: this.categories,
          minPrice: this.currentMinPrice,
          maxPrice: this.currentMaxPrice,
          orderby: this.orderby,
          perPage: this.perPage,
          currentPage: this.currentPage,
          totalPages: this.totalPages
        }
      }));
    },

    // ==================== UI HELPERS ====================

    /**
     * Show loading overlay
     */
    showLoading() {
      const overlay = document.getElementById('shop-loading-overlay');
      if (overlay) {
        overlay.classList.remove('hidden');
      }
    },

    /**
     * Hide loading overlay
     */
    hideLoading() {
      const overlay = document.getElementById('shop-loading-overlay');
      if (overlay) {
        overlay.classList.add('hidden');
      }
    },

    /**
     * Scroll to products grid
     */
    scrollToProducts() {
      const grid = document.getElementById('shop-products-grid');
      if (grid) {
        const offset = 100;
        const top = grid.getBoundingClientRect().top + window.pageYOffset - offset;
        window.scrollTo({ top, behavior: 'smooth' });
      }
    },

    /**
     * Format price for display
     */
    formatPrice(value) {
      return this.currencySymbol + value.toLocaleString();
    }
  };
}
