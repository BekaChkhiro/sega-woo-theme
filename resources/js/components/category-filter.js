/**
 * Category Filter Component
 *
 * Handles checkbox-style category filtering for the shop sidebar.
 * Supports multi-category selection with comma-separated URL parameters.
 * Implements smart subcategory logic - auto-expands/collapses based on selection.
 * Uses AJAX for filtering without page reload.
 */
export default function categoryFilter(config = {}) {
  return {
    shopUrl: config.shopUrl || '',
    preserveParams: config.preserveParams || ['min_price', 'max_price', 'on_sale', 'in_stock', 'orderby', 'per_page'],
    selectedCategories: config.initialCategories || [],
    categoryTree: config.categoryTree || {}, // Maps parent slug -> array of child slugs
    expandedParents: {}, // Track which parent categories are expanded (slug -> boolean)
    isLoading: false, // AJAX loading state
    // AJAX config - use config values or fallback to global
    ajaxUrl: config.ajaxUrl || (window.sageShopAjax?.ajaxUrl) || '',
    nonce: config.nonce || (window.sageShopAjax?.nonce) || '',
    useAjax: !!(config.ajaxUrl || window.sageShopAjax), // Check if AJAX is available

    /**
     * Initialize the component
     */
    init() {
      console.log('[CategoryFilter] Init started');
      console.log('[CategoryFilter] Config:', {
        shopUrl: this.shopUrl,
        categoryTree: this.categoryTree,
        initialCategories: this.selectedCategories,
        ajaxUrl: this.ajaxUrl,
        useAjax: this.useAjax
      });

      // Use initial categories from config, fallback to URL parsing
      if (!this.selectedCategories.length) {
        const currentParams = new URLSearchParams(window.location.search);
        const productCat = currentParams.get('cat_ids');
        console.log('[CategoryFilter] URL cat_ids:', productCat);
        if (productCat) {
          this.selectedCategories = productCat.split(',')
            .map(slug => this.decodeSlugIfNeeded(slug.trim()))
            .filter(slug => slug.length > 0); // Filter out empty strings
        }
      } else {
        // Filter out empty strings from initial categories
        this.selectedCategories = this.selectedCategories.filter(slug => slug && slug.length > 0);
      }

      console.log('[CategoryFilter] Selected categories (filtered):', this.selectedCategories);

      // Initialize expanded state based on selected categories
      this.initializeExpandedState();

      // Attach pagination handlers for AJAX (if available)
      if (this.useAjax) {
        this.attachPaginationHandlers();

        // Handle browser back/forward buttons
        window.addEventListener('popstate', () => {
          // Re-parse URL and reload
          const params = new URLSearchParams(window.location.search);
          const cats = params.get('cat_ids');
          this.selectedCategories = cats ? cats.split(',').map(s => this.decodeSlugIfNeeded(s.trim())) : [];
          this.initializeExpandedState();
          this.applyFilterAjax();
        });
      }

      console.log('[CategoryFilter] Init completed');
    },

    /**
     * Check if a category is currently selected
     * @param {string} slug - Category slug to check
     * @returns {boolean}
     */
    isCategorySelected(slug) {
      return this.selectedCategories.includes(slug);
    },

    /**
     * Initialize which parent categories should be expanded
     * Smart logic: expand parent if it or any of its children are selected
     */
    initializeExpandedState() {
      console.log('[CategoryFilter] initializeExpandedState started');
      console.log('[CategoryFilter] categoryTree:', this.categoryTree);
      console.log('[CategoryFilter] categoryTree type:', typeof this.categoryTree);

      this.expandedParents = {};

      // For each parent in the category tree
      Object.keys(this.categoryTree).forEach(parentSlug => {
        const rawChildSlugs = this.categoryTree[parentSlug];
        console.log(`[CategoryFilter] Parent "${parentSlug}" children:`, rawChildSlugs, 'type:', typeof rawChildSlugs, 'isArray:', Array.isArray(rawChildSlugs));

        // Ensure childSlugs is always an array
        let childSlugs = [];
        if (Array.isArray(rawChildSlugs)) {
          childSlugs = rawChildSlugs;
        } else if (rawChildSlugs && typeof rawChildSlugs === 'object') {
          // If it's an object, convert values to array
          childSlugs = Object.values(rawChildSlugs);
          console.log(`[CategoryFilter] Converted object to array:`, childSlugs);
        }

        // Expand if parent is selected
        if (this.selectedCategories.includes(parentSlug)) {
          this.expandedParents[parentSlug] = true;
          return;
        }

        // Expand if any child is selected
        const hasSelectedChild = childSlugs.some(childSlug =>
          this.selectedCategories.includes(childSlug)
        );

        if (hasSelectedChild) {
          this.expandedParents[parentSlug] = true;
        }
      });

      console.log('[CategoryFilter] expandedParents:', this.expandedParents);
    },

    /**
     * Check if a parent category should be expanded
     * @param {string} parentSlug - The parent category slug
     * @returns {boolean}
     */
    isParentExpanded(parentSlug) {
      return this.expandedParents[parentSlug] === true;
    },

    /**
     * Toggle expansion state of a parent category
     * @param {string} parentSlug - The parent category slug
     */
    toggleExpanded(parentSlug) {
      this.expandedParents[parentSlug] = !this.expandedParents[parentSlug];
    },

    /**
     * Get the parent slug for a given child slug
     * @param {string} childSlug - The child category slug
     * @returns {string|null} Parent slug or null if not found
     */
    getParentSlug(childSlug) {
      for (const [parentSlug, children] of Object.entries(this.categoryTree)) {
        if (children.includes(childSlug)) {
          return parentSlug;
        }
      }
      return null;
    },

    /**
     * Get the base shop URL, ensuring it's always valid
     * @returns {string} The shop URL
     */
    getShopUrl() {
      // If shopUrl is provided and valid, use it
      if (this.shopUrl && this.shopUrl.length > 0) {
        // Check if it's a full URL or relative
        if (this.shopUrl.startsWith('http')) {
          return this.shopUrl;
        }
        // It's a relative URL, make it absolute
        return window.location.origin + this.shopUrl;
      }

      // Fallback: try to find shop URL from current location
      // Remove any query params and hash
      const currentPath = window.location.pathname;

      // If we're on a shop-related page, use the base shop path
      if (currentPath.includes('/shop')) {
        const shopPath = currentPath.split('/shop')[0] + '/shop/';
        return window.location.origin + shopPath;
      }

      // Ultimate fallback
      return window.location.origin + '/shop/';
    },

    /**
     * Toggle a category selection (multi-select enabled)
     * Smart subcategory logic:
     * - When parent is selected, auto-expand to show subcategories
     * - When parent is deselected, collapse if no children are selected
     * - When child is selected, ensure parent section stays expanded
     *
     * @param {string} slug - Category slug
     * @param {boolean} checked - Whether the checkbox is checked
     * @param {boolean} isParent - Whether this is a parent category (optional)
     */
    toggleCategory(slug, checked, isParent = false) {
      const decodedSlug = this.decodeSlugIfNeeded(slug);

      if (checked) {
        // Add category if not already selected
        if (!this.selectedCategories.includes(decodedSlug)) {
          this.selectedCategories.push(decodedSlug);
        }

        // Smart expansion logic
        if (isParent || this.categoryTree[decodedSlug]) {
          // This is a parent category - auto-expand to show subcategories
          this.expandedParents[decodedSlug] = true;
        } else {
          // This is a child category - ensure parent stays expanded
          const parentSlug = this.getParentSlug(decodedSlug);
          if (parentSlug) {
            this.expandedParents[parentSlug] = true;
          }
        }
      } else {
        // Remove category from selection
        this.selectedCategories = this.selectedCategories.filter(s => s !== decodedSlug);

        // Smart collapse logic for parent categories
        if (isParent || this.categoryTree[decodedSlug]) {
          // Check if any children are still selected
          const childSlugs = this.categoryTree[decodedSlug] || [];
          const hasSelectedChild = childSlugs.some(childSlug =>
            this.selectedCategories.includes(childSlug)
          );

          // Only collapse if no children are selected
          if (!hasSelectedChild) {
            this.expandedParents[decodedSlug] = false;
          }
        }
      }

      this.applyFilter();
    },

    /**
     * Apply the current category filter
     * Uses AJAX if available, otherwise falls back to page reload
     */
    applyFilter() {
      if (this.useAjax) {
        this.applyFilterAjax();
      } else {
        this.applyFilterRedirect();
      }
    },

    /**
     * Apply filter using AJAX (no page reload)
     */
    async applyFilterAjax() {
      if (this.isLoading) return;

      // Fallback to redirect if AJAX not properly configured
      if (!this.ajaxUrl || !this.nonce) {
        console.warn('AJAX not configured, falling back to redirect');
        this.applyFilterRedirect();
        return;
      }

      this.isLoading = true;
      this.showLoading();

      try {
        // Get current filter parameters
        const currentParams = new URLSearchParams(window.location.search);
        const formData = new FormData();

        formData.append('action', 'filter_products');
        formData.append('nonce', this.nonce);

        // Add categories
        if (this.selectedCategories.length > 0) {
          this.selectedCategories.forEach(cat => {
            formData.append('categories[]', cat);
          });
        }

        // Preserve other filters
        if (currentParams.has('min_price')) {
          formData.append('min_price', currentParams.get('min_price'));
        }
        if (currentParams.has('max_price')) {
          formData.append('max_price', currentParams.get('max_price'));
        }
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

        const response = await fetch(this.ajaxUrl, {
          method: 'POST',
          body: formData,
        });

        const data = await response.json();

        if (data.success) {
          this.updateProductsGrid(data.data);
          this.updateUrl();
          this.scrollToProducts();
        } else {
          console.error('Filter error:', data.data?.message || 'Unknown error');
          // Fallback to page reload on error
          this.applyFilterRedirect();
        }
      } catch (error) {
        console.error('AJAX filter error:', error);
        // Fallback to page reload on error
        this.applyFilterRedirect();
      } finally {
        this.isLoading = false;
        this.hideLoading();
      }
    },

    /**
     * Apply filter using page redirect (fallback)
     */
    applyFilterRedirect() {
      try {
        const baseUrl = this.getShopUrl();
        const url = new URL(baseUrl);

        // Set category parameter with comma-separated slugs
        if (this.selectedCategories.length > 0) {
          url.searchParams.set('cat_ids', this.selectedCategories.join(','));
        }

        // Preserve other filter parameters from current URL
        const currentParams = new URLSearchParams(window.location.search);
        this.preserveParams.forEach(param => {
          if (currentParams.has(param)) {
            url.searchParams.set(param, currentParams.get(param));
          }
        });

        // Reset to page 1 when changing category
        url.searchParams.delete('paged');
        url.searchParams.delete('page');

        window.location.href = url.toString();
      } catch (error) {
        console.error('Category filter error:', error);
        // Fallback: simple redirect
        if (this.selectedCategories.length > 0) {
          const fallbackUrl = this.getShopUrl() + '?cat_ids=' + encodeURIComponent(this.selectedCategories.join(','));
          window.location.href = fallbackUrl;
        } else {
          window.location.href = this.getShopUrl();
        }
      }
    },

    /**
     * Update the products grid with AJAX response
     * @param {Object} data - Response data from AJAX
     */
    updateProductsGrid(data) {
      // Update products
      const productsContainer = document.getElementById('shop-products-grid');
      if (productsContainer && data.products) {
        productsContainer.innerHTML = data.products;
      }

      // Update result count
      const resultCount = document.getElementById('shop-result-count');
      if (resultCount && data.result_count) {
        resultCount.innerHTML = `<p class="text-sm text-secondary-600">${data.result_count}</p>`;
      }

      // Update pagination
      const pagination = document.getElementById('shop-pagination');
      if (pagination) {
        pagination.innerHTML = data.pagination || '';
        // Re-attach pagination click handlers
        this.attachPaginationHandlers();
      }
    },

    /**
     * Attach click handlers to pagination links for AJAX
     */
    attachPaginationHandlers() {
      const pagination = document.getElementById('shop-pagination');
      if (!pagination) return;

      pagination.querySelectorAll('a.page-numbers').forEach(link => {
        link.addEventListener('click', (e) => {
          e.preventDefault();
          const href = link.getAttribute('href');
          const url = new URL(href, window.location.origin);
          const page = url.searchParams.get('paged') || '1';
          this.loadPage(parseInt(page, 10));
        });
      });
    },

    /**
     * Load a specific page via AJAX
     * @param {number} page - Page number to load
     */
    async loadPage(page) {
      if (this.isLoading) return;

      this.isLoading = true;
      this.showLoading();

      try {
        const currentParams = new URLSearchParams(window.location.search);
        const formData = new FormData();

        formData.append('action', 'filter_products');
        formData.append('nonce', this.nonce);
        formData.append('paged', page.toString());

        // Add current categories
        if (this.selectedCategories.length > 0) {
          this.selectedCategories.forEach(cat => {
            formData.append('categories[]', cat);
          });
        }

        // Preserve other filters
        if (currentParams.has('min_price')) {
          formData.append('min_price', currentParams.get('min_price'));
        }
        if (currentParams.has('max_price')) {
          formData.append('max_price', currentParams.get('max_price'));
        }
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

        const response = await fetch(this.ajaxUrl, {
          method: 'POST',
          body: formData,
        });

        const data = await response.json();

        if (data.success) {
          this.updateProductsGrid(data.data);
          this.updateUrl(page);
          this.scrollToProducts();
        }
      } catch (error) {
        console.error('Pagination error:', error);
      } finally {
        this.isLoading = false;
        this.hideLoading();
      }
    },

    /**
     * Update browser URL without page reload
     * @param {number} page - Current page number (optional)
     */
    updateUrl(page = 1) {
      const baseUrl = this.getShopUrl();
      const url = new URL(baseUrl);
      const currentParams = new URLSearchParams(window.location.search);

      // Set category parameter
      if (this.selectedCategories.length > 0) {
        url.searchParams.set('cat_ids', this.selectedCategories.join(','));
      }

      // Preserve other filters
      this.preserveParams.forEach(param => {
        if (currentParams.has(param)) {
          url.searchParams.set(param, currentParams.get(param));
        }
      });

      // Set page if not first page
      if (page > 1) {
        url.searchParams.set('paged', page.toString());
      }

      // Update URL without reload
      window.history.pushState({}, '', url.toString());
    },

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
      const productsGrid = document.getElementById('shop-products-grid');
      if (productsGrid) {
        const offset = 100; // Account for fixed header
        const top = productsGrid.getBoundingClientRect().top + window.pageYOffset - offset;
        window.scrollTo({ top, behavior: 'smooth' });
      }
    },

    /**
     * Decode slug if it's already URL-encoded to prevent double encoding
     * @param {string} slug - The category slug
     * @returns {string} Decoded slug
     */
    decodeSlugIfNeeded(slug) {
      // Check if slug contains encoded characters (starts with % followed by hex)
      if (/%[0-9A-Fa-f]{2}/.test(slug)) {
        try {
          return decodeURIComponent(slug);
        } catch (e) {
          // If decoding fails, return original
          return slug;
        }
      }
      return slug;
    },

    /**
     * Navigate to a specific category (single selection - legacy support)
     * @param {string} slug - Category slug to navigate to
     */
    navigateToCategory(slug) {
      const decodedSlug = this.decodeSlugIfNeeded(slug);
      this.selectedCategories = [decodedSlug];
      this.applyFilter();
    },

    /**
     * Clear category filter and show all products
     */
    clearCategory() {
      this.selectedCategories = [];
      this.applyFilter();
    }
  };
}
