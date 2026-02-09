<?php

namespace App\View\Composers;

use Roots\Acorn\View\Composer;

class SearchResults extends Composer
{
    /**
     * List of views served by this composer.
     *
     * @var array
     */
    protected static $views = [
        'woocommerce.search-product',
    ];

    /**
     * Data to be passed to view before rendering.
     *
     * @return array
     */
    public function with()
    {
        return [
            'searchQuery'      => $this->searchQuery(),
            'searchTitle'      => $this->searchTitle(),
            'searchResultsUrl' => $this->searchResultsUrl(),
            'isProductSearch'  => $this->isProductSearch(),
            'searchProducts'   => $this->searchProducts(),
            'searchPagination' => $this->searchPagination(),
            'currentPage'      => $this->currentPage(),
            'totalPages'       => $this->totalPages(),
            'totalProducts'    => $this->totalProducts(),
            'hasProducts'      => $this->hasProducts(),
            'perPage'          => $this->getProductsPerPage(),
        ];
    }

    /**
     * Get the current search query string.
     *
     * @return string
     */
    public function searchQuery(): string
    {
        return get_search_query();
    }

    /**
     * Get the search page title.
     *
     * @return string
     */
    public function searchTitle(): string
    {
        $query = $this->searchQuery();

        if (empty($query)) {
            return __('Search Products', 'sega-woo-theme');
        }

        return sprintf(
            __('Search results: "%s"', 'sega-woo-theme'),
            esc_html($query)
        );
    }

    /**
     * Check if this is a product-specific search.
     *
     * @return bool
     */
    public function isProductSearch(): bool
    {
        return is_search() && isset($_GET['post_type']) && $_GET['post_type'] === 'product';
    }

    /**
     * Get the current search results URL with all parameters.
     *
     * @return string
     */
    public function searchResultsUrl(): string
    {
        $url = add_query_arg([
            's'         => $this->searchQuery(),
            'post_type' => 'product',
        ], home_url('/'));

        // Preserve filter parameters
        $preserve = ['orderby', 'per_page', 'min_price', 'max_price', 'paged'];
        foreach ($preserve as $param) {
            if (isset($_GET[$param]) && $_GET[$param] !== '') {
                $url = add_query_arg($param, wc_clean(wp_unslash($_GET[$param])), $url);
            }
        }

        return $url;
    }

    /**
     * Get search products data including query setup.
     *
     * This sets up the WooCommerce loop properly for search results.
     *
     * @return array
     */
    public function searchProducts(): array
    {
        global $wp_query;

        // Get products per page (respecting user selection)
        $perPage = $this->getProductsPerPage();
        $currentPage = $this->currentPage();

        // Ensure WooCommerce loop is properly set up
        if (function_exists('wc_setup_loop')) {
            wc_setup_loop([
                'name'         => 'search',
                'is_search'    => true,
                'is_paginated' => true,
                'total'        => $wp_query->found_posts,
                'total_pages'  => $wp_query->max_num_pages,
                'per_page'     => $perPage,
                'current_page' => $currentPage,
            ]);
        }

        return [
            'total'       => (int) $wp_query->found_posts,
            'totalPages'  => (int) $wp_query->max_num_pages,
            'currentPage' => $currentPage,
            'perPage'     => $perPage,
            'hasProducts' => $wp_query->have_posts(),
        ];
    }

    /**
     * Get products per page setting.
     *
     * @return int
     */
    protected function getProductsPerPage(): int
    {
        if (isset($_GET['per_page'])) {
            $requested = absint($_GET['per_page']);
            $allowed = [12, 24, 48, 96];

            if (in_array($requested, $allowed, true)) {
                return $requested;
            }
        }

        return (int) apply_filters('loop_shop_per_page', wc_get_default_products_per_row() * wc_get_default_product_rows_per_page());
    }

    /**
     * Get pagination data for search results.
     *
     * @return array
     */
    public function searchPagination(): array
    {
        global $wp_query;

        $currentPage = $this->currentPage();
        $totalPages = (int) $wp_query->max_num_pages;

        return [
            'currentPage' => $currentPage,
            'totalPages'  => $totalPages,
            'hasPrev'     => $currentPage > 1,
            'hasNext'     => $currentPage < $totalPages,
            'prevUrl'     => $this->getPaginationUrl($currentPage - 1),
            'nextUrl'     => $this->getPaginationUrl($currentPage + 1),
            'pages'       => $this->getPaginationPages($currentPage, $totalPages),
        ];
    }

    /**
     * Get pagination URL for a specific page.
     *
     * @param int $page
     * @return string
     */
    protected function getPaginationUrl(int $page): string
    {
        if ($page < 1) {
            return '';
        }

        $baseUrl = add_query_arg([
            's'         => $this->searchQuery(),
            'post_type' => 'product',
        ], home_url('/'));

        // Add page number
        if ($page > 1) {
            $baseUrl = add_query_arg('paged', $page, $baseUrl);
        }

        // Preserve other query params
        $preserve = ['orderby', 'per_page', 'min_price', 'max_price'];
        foreach ($preserve as $param) {
            if (isset($_GET[$param]) && $_GET[$param] !== '') {
                $baseUrl = add_query_arg($param, wc_clean(wp_unslash($_GET[$param])), $baseUrl);
            }
        }

        return $baseUrl;
    }

    /**
     * Get pagination pages array with dots handling.
     *
     * @param int $currentPage
     * @param int $totalPages
     * @param int $range Number of pages to show on each side of current page
     * @return array
     */
    protected function getPaginationPages(int $currentPage, int $totalPages, int $range = 2): array
    {
        $pages = [];

        if ($totalPages <= 1) {
            return $pages;
        }

        // Always include first page
        $pages[] = [
            'number'    => 1,
            'url'       => $this->getPaginationUrl(1),
            'isCurrent' => $currentPage === 1,
            'isDots'    => false,
        ];

        // Add dots before current range if needed
        if ($currentPage - $range > 2) {
            $pages[] = [
                'number'    => null,
                'url'       => '',
                'isCurrent' => false,
                'isDots'    => true,
            ];
        }

        // Add pages in range
        for ($i = max(2, $currentPage - $range); $i <= min($totalPages - 1, $currentPage + $range); $i++) {
            $pages[] = [
                'number'    => $i,
                'url'       => $this->getPaginationUrl($i),
                'isCurrent' => $currentPage === $i,
                'isDots'    => false,
            ];
        }

        // Add dots after current range if needed
        if ($currentPage + $range < $totalPages - 1) {
            $pages[] = [
                'number'    => null,
                'url'       => '',
                'isCurrent' => false,
                'isDots'    => true,
            ];
        }

        // Always include last page if more than 1 page
        if ($totalPages > 1) {
            $pages[] = [
                'number'    => $totalPages,
                'url'       => $this->getPaginationUrl($totalPages),
                'isCurrent' => $currentPage === $totalPages,
                'isDots'    => false,
            ];
        }

        return $pages;
    }

    /**
     * Get suggested search terms when no results found.
     *
     * @return array
     */
    public function searchSuggestions(): array
    {
        $query = $this->searchQuery();

        if (empty($query)) {
            return [];
        }

        // Get popular product categories as suggestions
        $categories = get_terms([
            'taxonomy'   => 'product_cat',
            'hide_empty' => true,
            'orderby'    => 'count',
            'order'      => 'DESC',
            'number'     => 5,
        ]);

        if (is_wp_error($categories)) {
            return [];
        }

        return array_map(function ($category) {
            return [
                'name' => $category->name,
                'url'  => get_term_link($category),
                'type' => 'category',
            ];
        }, $categories);
    }

    /**
     * Get recent searches from session (if implemented).
     *
     * @return array
     */
    public function recentSearches(): array
    {
        // Could be extended to use session or cookies
        return [];
    }

    /**
     * Get the current page number.
     *
     * @return int
     */
    public function currentPage(): int
    {
        // Check query string first (for search URLs with ?paged=X)
        if (isset($_GET['paged'])) {
            return max(1, absint($_GET['paged']));
        }

        // Fall back to WordPress query var
        $paged = get_query_var('paged', 1);
        if ($paged < 1) {
            $paged = get_query_var('page', 1);
        }

        return max(1, (int) $paged);
    }

    /**
     * Get the total number of pages.
     *
     * @return int
     */
    public function totalPages(): int
    {
        global $wp_query;
        return (int) $wp_query->max_num_pages;
    }

    /**
     * Get total number of products found.
     *
     * @return int
     */
    public function totalProducts(): int
    {
        global $wp_query;
        return (int) $wp_query->found_posts;
    }

    /**
     * Check if there are products to display.
     *
     * @return bool
     */
    public function hasProducts(): bool
    {
        global $wp_query;
        return $wp_query->have_posts();
    }
}
