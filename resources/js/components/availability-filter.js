/**
 * Availability Filter Component
 *
 * Handles On Sale and In Stock filters with AJAX.
 */
export default function availabilityFilter(initialOnSale = false, initialInStock = false) {
  return {
    onSale: initialOnSale,
    inStock: initialInStock,
    isLoading: false,

    async applyFilter() {
      if (this.isLoading || !window.sageShopAjax) return;

      this.isLoading = true;
      const overlay = document.getElementById('shop-loading-overlay');
      if (overlay) overlay.classList.remove('hidden');

      try {
        const currentParams = new URLSearchParams(window.location.search);
        const formData = new FormData();

        formData.append('action', 'filter_products');
        formData.append('nonce', window.sageShopAjax.nonce);

        // Preserve categories
        if (currentParams.has('cat_ids')) {
          currentParams.get('cat_ids').split(',').forEach(cat => {
            formData.append('categories[]', cat.trim());
          });
        }

        // Preserve price
        if (currentParams.has('min_price')) formData.append('min_price', currentParams.get('min_price'));
        if (currentParams.has('max_price')) formData.append('max_price', currentParams.get('max_price'));

        // Add availability filters
        if (this.onSale) formData.append('on_sale', '1');
        if (this.inStock) formData.append('in_stock', '1');

        // Preserve sorting
        if (currentParams.has('orderby')) formData.append('orderby', currentParams.get('orderby'));
        if (currentParams.has('per_page')) formData.append('per_page', currentParams.get('per_page'));

        formData.append('paged', '1');

        const response = await fetch(window.sageShopAjax.ajaxUrl, { method: 'POST', body: formData });
        const data = await response.json();

        if (data.success) {
          // Update DOM
          const productsContainer = document.getElementById('shop-products-grid');
          if (productsContainer && data.data.products) {
            productsContainer.innerHTML = data.data.products;
          }

          const resultCount = document.getElementById('shop-result-count');
          if (resultCount && data.data.result_count) {
            resultCount.innerHTML = `<p class="text-sm text-secondary-600">${data.data.result_count}</p>`;
          }

          const pagination = document.getElementById('shop-pagination');
          if (pagination) {
            pagination.innerHTML = data.data.pagination || '';
          }

          // Update URL
          const url = new URL(window.sageShopAjax.shopUrl);
          const params = new URLSearchParams(window.location.search);
          params.forEach((value, key) => {
            if (key !== 'on_sale' && key !== 'in_stock' && key !== 'paged') {
              url.searchParams.set(key, value);
            }
          });
          if (this.onSale) url.searchParams.set('on_sale', '1');
          if (this.inStock) url.searchParams.set('in_stock', '1');
          window.history.pushState({}, '', url.toString());

          // Scroll to products
          const grid = document.getElementById('shop-products-grid');
          if (grid) {
            const top = grid.getBoundingClientRect().top + window.pageYOffset - 100;
            window.scrollTo({ top, behavior: 'smooth' });
          }
        }
      } catch (error) {
        console.error('Filter error:', error);
      } finally {
        this.isLoading = false;
        const overlay = document.getElementById('shop-loading-overlay');
        if (overlay) overlay.classList.add('hidden');
      }
    }
  };
}
