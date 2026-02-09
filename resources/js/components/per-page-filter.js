/**
 * Per Page Filter Component
 *
 * Handles products per page selection with AJAX.
 */
export default function perPageFilter(initialPerPage = 12) {
  return {
    currentPerPage: initialPerPage,
    isLoading: false,

    async changePerPage(value) {
      if (this.isLoading || value === this.currentPerPage || !window.sageShopAjax) return;

      this.isLoading = true;
      this.currentPerPage = value;

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

        // Preserve filters
        if (currentParams.has('min_price')) formData.append('min_price', currentParams.get('min_price'));
        if (currentParams.has('max_price')) formData.append('max_price', currentParams.get('max_price'));
        if (currentParams.has('on_sale')) formData.append('on_sale', currentParams.get('on_sale'));
        if (currentParams.has('in_stock')) formData.append('in_stock', currentParams.get('in_stock'));
        if (currentParams.has('orderby')) formData.append('orderby', currentParams.get('orderby'));

        // Set new per_page and reset to page 1
        formData.append('per_page', value);
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
          currentParams.forEach((val, key) => {
            if (key !== 'per_page' && key !== 'paged') {
              url.searchParams.set(key, val);
            }
          });
          if (value !== 12) url.searchParams.set('per_page', value);
          window.history.pushState({}, '', url.toString());

          // Scroll to products
          const grid = document.getElementById('shop-products-grid');
          if (grid) {
            const top = grid.getBoundingClientRect().top + window.pageYOffset - 100;
            window.scrollTo({ top, behavior: 'smooth' });
          }
        }
      } catch (error) {
        console.error('Per page change error:', error);
      } finally {
        this.isLoading = false;
        const overlay = document.getElementById('shop-loading-overlay');
        if (overlay) overlay.classList.add('hidden');
      }
    }
  };
}
