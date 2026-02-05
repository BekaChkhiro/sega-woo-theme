/**
 * Search Popup Component
 *
 * Alpine.js component for the AJAX search popup overlay.
 * Uses the WP REST API endpoint for searching products and categories.
 *
 * REST API Endpoint: /wp-json/sega/v1/search
 */
export default function searchPopup() {
  return {
    isOpen: false,
    query: '',
    isLoading: false,
    results: {
      categories: [],
      products: [],
    },
    hasSearched: false,
    debounceTimer: null,
    abortController: null,

    /**
     * Initialize the component
     */
    init() {
      // Listen for global events to open/close
      window.addEventListener('open-search-popup', () => this.open());
      window.addEventListener('close-search-popup', () => this.close());

      // Note: ESC key handling is done via Alpine.js directive in the template:
      // @keydown.escape.window="close()"
    },

    /**
     * Open the search popup
     */
    open() {
      this.isOpen = true;
      document.body.classList.add('overflow-hidden');

      // Reset animation classes for re-entry animation
      this.resetAnimations();

      // Focus the search input after animation completes
      this.$nextTick(() => {
        const input = this.$refs.searchInput;
        if (input) {
          // Small delay to let animation start, then focus
          setTimeout(() => {
            input.focus();
          }, 100);
        }
      });
    },

    /**
     * Reset CSS animations by removing and re-adding animation classes
     * This ensures animations play each time the popup opens
     */
    resetAnimations() {
      // Reset category link animations
      this.$nextTick(() => {
        const categoryLinks = this.$el.querySelectorAll('.search-category-link');
        categoryLinks.forEach((link) => {
          link.style.animation = 'none';
          // Trigger reflow
          link.offsetHeight;
          link.style.animation = '';
        });

        // Reset section title animations
        const sectionTitles = this.$el.querySelectorAll('.search-section-title');
        sectionTitles.forEach((title) => {
          title.style.animation = 'none';
          title.offsetHeight;
          title.style.animation = '';
        });

        // Reset keyboard hints animation
        const keyboardHints = this.$el.querySelector('.search-keyboard-hints');
        if (keyboardHints) {
          keyboardHints.style.animation = 'none';
          keyboardHints.offsetHeight;
          keyboardHints.style.animation = '';
        }

        // Reset search icon pulse
        const iconPulse = this.$el.querySelector('.search-popup-icon-pulse');
        if (iconPulse) {
          iconPulse.style.animation = 'none';
          iconPulse.offsetHeight;
          iconPulse.style.animation = '';
        }
      });
    },

    /**
     * Close the search popup
     */
    close() {
      this.isOpen = false;
      document.body.classList.remove('overflow-hidden');
      this.clearSearch();
    },

    /**
     * Clear search state
     */
    clearSearch() {
      // Cancel any pending request
      if (this.abortController) {
        this.abortController.abort();
        this.abortController = null;
      }

      // Clear debounce timer
      if (this.debounceTimer) {
        clearTimeout(this.debounceTimer);
        this.debounceTimer = null;
      }

      this.query = '';
      this.results = {
        categories: [],
        products: [],
      };
      this.hasSearched = false;
      this.isLoading = false;
    },

    /**
     * Handle search input with debounce
     */
    handleInput() {
      // Clear existing timer
      if (this.debounceTimer) {
        clearTimeout(this.debounceTimer);
      }

      // Minimum 2 characters to search
      if (this.query.length < 2) {
        this.results = {
          categories: [],
          products: [],
        };
        this.hasSearched = false;
        return;
      }

      // Debounce the search
      this.debounceTimer = setTimeout(() => {
        this.performSearch();
      }, 300);
    },

    /**
     * Perform the AJAX search using WP REST API
     *
     * Calls: /wp-json/sega/v1/search?s={query}&per_page=6
     */
    async performSearch() {
      if (this.query.length < 2) return;

      // Cancel any pending request
      if (this.abortController) {
        this.abortController.abort();
      }

      this.isLoading = true;
      this.hasSearched = true;

      // Create new abort controller for this request
      this.abortController = new AbortController();

      try {
        // Build the API URL
        const baseUrl = window.location.origin;
        const apiUrl = new URL(`${baseUrl}/wp-json/sega/v1/search`);
        apiUrl.searchParams.set('s', this.query);
        apiUrl.searchParams.set('per_page', '6');

        // Make the API request
        const response = await fetch(apiUrl.toString(), {
          method: 'GET',
          headers: {
            'Accept': 'application/json',
          },
          signal: this.abortController.signal,
        });

        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();

        // Update results
        this.results = {
          categories: data.categories || [],
          products: data.products || [],
        };

        // Reset result item animations for stagger effect
        this.resetResultAnimations();

        // Dispatch event for any external listeners
        const event = new CustomEvent('search-popup-results', {
          detail: {
            query: this.query,
            results: this.results,
          }
        });
        window.dispatchEvent(event);

      } catch (error) {
        // Ignore abort errors (user typed new query)
        if (error.name === 'AbortError') {
          return;
        }

        console.error('Search error:', error);

        // Clear results on error
        this.results = {
          categories: [],
          products: [],
        };
      } finally {
        this.isLoading = false;
      }
    },

    /**
     * Reset result item animations when new results appear
     */
    resetResultAnimations() {
      this.$nextTick(() => {
        // Wait a frame for DOM to update with new results
        requestAnimationFrame(() => {
          // Reset result items
          const resultItems = this.$el.querySelectorAll('.search-result-item');
          resultItems.forEach((item) => {
            item.style.animation = 'none';
            item.offsetHeight;
            item.style.animation = '';
          });

          // Reset section titles
          const sectionTitles = this.$el.querySelectorAll('.search-section-title');
          sectionTitles.forEach((title) => {
            title.style.animation = 'none';
            title.offsetHeight;
            title.style.animation = '';
          });

          // Reset no results icon if visible
          const noResultsIcon = this.$el.querySelector('.search-no-results-icon');
          if (noResultsIcon) {
            noResultsIcon.style.animation = 'none';
            noResultsIcon.offsetHeight;
            noResultsIcon.style.animation = '';
          }
        });
      });
    },

    /**
     * Set search results (called from external API handler)
     */
    setResults(categories, products) {
      this.results = {
        categories: categories || [],
        products: products || [],
      };
      this.isLoading = false;
      this.resetResultAnimations();
    },

    /**
     * Check if there are any results
     */
    get hasResults() {
      return this.results.categories.length > 0 || this.results.products.length > 0;
    },

    /**
     * Check if we should show "no results" message
     */
    get showNoResults() {
      return this.hasSearched && !this.isLoading && !this.hasResults && this.query.length >= 2;
    },

    /**
     * Handle clicking outside the search content
     */
    handleBackdropClick(e) {
      // Only close if clicking directly on the backdrop
      if (e.target === e.currentTarget) {
        this.close();
      }
    },

    /**
     * Handle form submission (pressing Enter)
     */
    handleSubmit() {
      if (this.query.length >= 2) {
        // Navigate to search results page
        const searchUrl = new URL(window.location.origin);
        searchUrl.searchParams.set('s', this.query);
        searchUrl.searchParams.set('post_type', 'product');
        window.location.href = searchUrl.toString();
      }
    },
  };
}
