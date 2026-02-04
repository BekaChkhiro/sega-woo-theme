/**
 * Mini Cart Alpine.js Component
 *
 * Handles cart dropdown functionality and real-time updates
 * via WooCommerce cart fragments.
 */
export default function miniCart() {
  return {
    open: false,
    loading: false,
    addingToCart: false,
    itemCount: parseInt(document.querySelector('.mini-cart-count')?.textContent || '0', 10),

    init() {
      // Set up event delegation for dynamically inserted elements
      this.setupEventDelegation();

      // Listen for WooCommerce cart fragment updates
      document.body.addEventListener('wc_fragments_refreshed', () => {
        this.refreshCart();
      });

      document.body.addEventListener('wc_fragments_loaded', () => {
        this.refreshCart();
      });

      // Listen for added_to_cart event (from WooCommerce AJAX)
      document.body.addEventListener('added_to_cart', (event) => {
        this.onAddedToCart(event);
      });

      // Listen for removed_from_cart event
      document.body.addEventListener('removed_from_cart', () => {
        this.refreshCart();
      });

      // Hook into WooCommerce add-to-cart buttons
      this.setupAddToCartHandlers();

      // Update count from fragments if available
      this.updateCountFromFragments();
    },

    /**
     * Set up event delegation for remove buttons
     * This ensures dynamically inserted elements (from fragments) work
     */
    setupEventDelegation() {
      const container = this.$el;
      if (!container) return;

      container.addEventListener('click', (e) => {
        const removeBtn = e.target.closest('.remove-from-cart');
        if (removeBtn) {
          e.preventDefault();
          e.stopPropagation();
          const cartItemKey = removeBtn.dataset.cartItemKey;
          if (cartItemKey) {
            this.removeItem(cartItemKey);
          }
        }
      });
    },

    /**
     * Set up handlers for add-to-cart buttons on product cards
     */
    setupAddToCartHandlers() {
      // Handle clicks on AJAX add-to-cart buttons
      document.body.addEventListener('click', (e) => {
        const addBtn = e.target.closest('.ajax_add_to_cart');
        if (addBtn && !addBtn.classList.contains('loading')) {
          e.preventDefault();
          e.stopPropagation();

          const productId = addBtn.dataset.product_id;
          const quantity = addBtn.dataset.quantity || 1;

          if (productId) {
            this.addToCart(productId, quantity, addBtn);
          }
        }
      });
    },

    toggle() {
      this.open = !this.open;
    },

    close() {
      this.open = false;
    },

    /**
     * Refresh cart data from the server
     */
    async refreshCart() {
      this.updateCountFromFragments();
    },

    /**
     * Update item count from WooCommerce fragments
     */
    updateCountFromFragments() {
      const countEl = document.querySelector('.mini-cart-count');
      if (countEl) {
        const count = parseInt(countEl.textContent || '0', 10);
        this.itemCount = isNaN(count) ? 0 : count;
      }
    },

    /**
     * Add item to cart via AJAX
     */
    async addToCart(productId, quantity = 1, buttonEl = null) {
      if (this.addingToCart) return;

      this.addingToCart = true;

      // Show loading state on button
      if (buttonEl) {
        buttonEl.classList.add('loading');
        const originalContent = buttonEl.innerHTML;
        buttonEl.dataset.originalContent = originalContent;
        buttonEl.innerHTML = `
          <svg class="h-4 w-4 sm:h-5 sm:w-5 animate-spin" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
        `;
      }

      try {
        const wcAjaxUrl = window.wc_add_to_cart_params?.wc_ajax_url || window.wc_cart_fragments_params?.wc_ajax_url;
        if (!wcAjaxUrl) {
          throw new Error('WooCommerce AJAX URL not found');
        }

        const response = await fetch(wcAjaxUrl.replace('%%endpoint%%', 'add_to_cart'), {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
          },
          body: new URLSearchParams({
            product_id: productId,
            quantity: quantity,
          }),
        });

        if (response.ok) {
          const data = await response.json();

          if (data.error) {
            // Show error toast
            this.showToast(data.error, 'error');
          } else {
            // Update fragments
            if (data.fragments) {
              this.updateFragments(data.fragments);
            }

            // Show success state on button briefly
            if (buttonEl) {
              buttonEl.innerHTML = `
                <svg class="h-4 w-4 sm:h-5 sm:w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
              `;
              buttonEl.classList.remove('bg-primary-600', 'hover:bg-primary-700');
              buttonEl.classList.add('bg-green-600');
            }

            // Show success toast
            this.showToast('Product added to cart', 'success');

            // Trigger event for other components
            document.body.dispatchEvent(new CustomEvent('added_to_cart', {
              detail: { productId, quantity, fragments: data.fragments },
            }));

            // Restore button after delay
            setTimeout(() => {
              if (buttonEl && buttonEl.dataset.originalContent) {
                buttonEl.innerHTML = buttonEl.dataset.originalContent;
                buttonEl.classList.remove('bg-green-600');
                buttonEl.classList.add('bg-primary-600', 'hover:bg-primary-700');
                delete buttonEl.dataset.originalContent;
              }
            }, 1500);
          }
        } else {
          throw new Error('Failed to add to cart');
        }
      } catch (error) {
        console.error('Error adding to cart:', error);
        this.showToast('Could not add to cart. Please try again.', 'error');

        // Restore button on error
        if (buttonEl && buttonEl.dataset.originalContent) {
          buttonEl.innerHTML = buttonEl.dataset.originalContent;
          delete buttonEl.dataset.originalContent;
        }
      } finally {
        this.addingToCart = false;
        if (buttonEl) {
          buttonEl.classList.remove('loading');
        }
      }
    },

    /**
     * Handle added to cart event
     */
    onAddedToCart(event) {
      // Briefly show the cart dropdown when item is added
      this.open = true;
      this.refreshCart();

      // Auto-close after 3 seconds
      setTimeout(() => {
        this.close();
      }, 3000);
    },

    /**
     * Remove item from cart via AJAX
     */
    async removeItem(cartItemKey) {
      if (this.loading) return;

      this.loading = true;

      // Find and animate the item being removed
      const itemEl = document.querySelector(`.mini-cart-item[data-key="${cartItemKey}"]`);
      if (itemEl) {
        itemEl.style.opacity = '0.5';
        itemEl.style.pointerEvents = 'none';
      }

      try {
        const response = await fetch(wc_cart_fragments_params?.wc_ajax_url?.replace('%%endpoint%%', 'remove_from_cart') || '', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
          },
          body: new URLSearchParams({
            cart_item_key: cartItemKey,
            security: wc_cart_fragments_params?.nonce || '',
          }),
        });

        if (response.ok) {
          const data = await response.json();

          // Update fragments
          if (data.fragments) {
            this.updateFragments(data.fragments);
          }

          // Show info toast
          this.showToast('Item removed from cart', 'info');

          // Trigger event for other components
          document.body.dispatchEvent(new CustomEvent('removed_from_cart', {
            detail: { cartItemKey },
          }));

          // Refresh the page fragment
          this.refreshMiniCartContent();
        }
      } catch (error) {
        console.error('Error removing item from cart:', error);
        this.showToast('Could not remove item. Please try again.', 'error');

        // Restore item visibility on error
        if (itemEl) {
          itemEl.style.opacity = '';
          itemEl.style.pointerEvents = '';
        }
      } finally {
        this.loading = false;
      }
    },

    /**
     * Update DOM with WooCommerce fragments
     */
    updateFragments(fragments) {
      for (const [selector, html] of Object.entries(fragments)) {
        const elements = document.querySelectorAll(selector);
        elements.forEach((el) => {
          el.outerHTML = html;
        });
      }

      this.updateCountFromFragments();
    },

    /**
     * Refresh mini cart content via AJAX
     */
    async refreshMiniCartContent() {
      try {
        const response = await fetch(wc_cart_fragments_params?.wc_ajax_url?.replace('%%endpoint%%', 'get_refreshed_fragments') || '', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
          },
        });

        if (response.ok) {
          const data = await response.json();

          if (data.fragments) {
            this.updateFragments(data.fragments);
          }
        }
      } catch (error) {
        console.error('Error refreshing cart:', error);
      }
    },

    /**
     * Show toast notification
     */
    showToast(message, type = 'info') {
      // Dispatch event for toast component
      document.body.dispatchEvent(new CustomEvent('show-toast', {
        detail: { message, type },
      }));
    },
  };
}
