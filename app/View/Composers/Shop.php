<?php

namespace App\View\Composers;

use Roots\Acorn\View\Composer;

class Shop extends Composer
{
    /**
     * List of views served by this composer.
     *
     * @var array
     */
    protected static $views = [
        'woocommerce.archive-product',
        'woocommerce.taxonomy-product_cat',
        'woocommerce.search-product',
        'woocommerce.loop.*',
        'partials.woocommerce.*',
        'partials.sidebar-shop',
    ];

    /**
     * Cache expiration time in seconds (1 hour).
     */
    protected const CACHE_EXPIRATION = HOUR_IN_SECONDS;

    /**
     * Cache key prefix.
     */
    protected const CACHE_PREFIX = 'sage_shop_';

    /**
     * Clear all shop-related transient caches.
     *
     * Should be called when products, categories, or prices change.
     */
    public static function clearCache(): void
    {
        delete_transient(self::CACHE_PREFIX . 'categories');
        delete_transient(self::CACHE_PREFIX . 'price_range');
        delete_transient(self::CACHE_PREFIX . 'total_products');
    }

    /**
     * Clear specific cache by type.
     */
    public static function clearCacheByType(string $type): void
    {
        delete_transient(self::CACHE_PREFIX . $type);
    }

    /**
     * Get the shop page title.
     */
    public function shopTitle(): string
    {
        if (is_search()) {
            return sprintf(
                __('Search results: "%s"', 'sega-woo-theme'),
                get_search_query()
            );
        }

        if (is_tax()) {
            return single_term_title('', false) ?: '';
        }

        if (is_shop()) {
            return woocommerce_page_title(false);
        }

        return get_the_archive_title();
    }

    /**
     * Get the shop page description.
     */
    public function shopDescription(): string
    {
        if (is_tax()) {
            return term_description() ?: '';
        }

        if (is_shop()) {
            $shop_page_id = wc_get_page_id('shop');
            return $shop_page_id ? get_post_field('post_content', $shop_page_id) : '';
        }

        return '';
    }

    /**
     * Check if this is the main shop page.
     */
    public function isShop(): bool
    {
        return is_shop();
    }

    /**
     * Check if this is a product category archive.
     */
    public function isCategory(): bool
    {
        return is_product_category();
    }

    /**
     * Check if this is a product tag archive.
     */
    public function isTag(): bool
    {
        return is_product_tag();
    }

    /**
     * Check if this is a search results page.
     */
    public function isSearch(): bool
    {
        return is_search();
    }

    /**
     * Get the current product category if on a category archive.
     */
    public function currentCategory(): ?\WP_Term
    {
        if (! is_product_category()) {
            return null;
        }

        return get_queried_object();
    }

    /**
     * Check if there are products to display.
     */
    public function hasProducts(): bool
    {
        return woocommerce_product_loop();
    }

    /**
     * Get the total number of products found.
     */
    public function totalProducts(): int
    {
        global $wp_query;
        return (int) $wp_query->found_posts;
    }

    /**
     * Get products per page setting.
     */
    public function productsPerPage(): int
    {
        return (int) apply_filters('loop_shop_per_page', wc_get_default_products_per_row() * wc_get_default_product_rows_per_page());
    }

    /**
     * Get available products per page options.
     */
    public function perPageOptions(): array
    {
        $options = [12, 24, 48, 96];
        $current = $this->currentPerPage();

        return array_map(function ($value) use ($current) {
            return [
                'value'    => $value,
                'label'    => sprintf(__('%d per page', 'sega-woo-theme'), $value),
                'selected' => $current === $value,
                'url'      => $this->getPerPageUrl($value),
            ];
        }, $options);
    }

    /**
     * Get current products per page value.
     */
    public function currentPerPage(): int
    {
        if (isset($_GET['per_page'])) {
            $requested = absint($_GET['per_page']);
            $allowed = [12, 24, 48, 96];

            if (in_array($requested, $allowed, true)) {
                return $requested;
            }
        }

        return $this->productsPerPage();
    }

    /**
     * Get the URL for a per page option.
     */
    protected function getPerPageUrl(int $perPage): string
    {
        $link = $this->getBaseShopUrl();
        return add_query_arg('per_page', $perPage, $link);
    }

    /**
     * Get the current page number.
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
     */
    public function totalPages(): int
    {
        // Try WooCommerce loop property first
        $wc_total = wc_get_loop_prop('total_pages');
        if ($wc_total) {
            return (int) $wc_total;
        }

        // Fallback to WordPress query
        global $wp_query;
        return (int) $wp_query->max_num_pages;
    }

    /**
     * Get the first product number on current page.
     */
    public function firstProduct(): int
    {
        $per_page = $this->currentPerPage();
        $current = $this->currentPage();

        return (($current - 1) * $per_page) + 1;
    }

    /**
     * Get the last product number on current page.
     */
    public function lastProduct(): int
    {
        $per_page = $this->currentPerPage();
        $current = $this->currentPage();
        $total = $this->totalProducts();

        return min($current * $per_page, $total);
    }

    /**
     * Get the result count text.
     */
    public function resultCount(): string
    {
        $total = $this->totalProducts();
        $first = $this->firstProduct();
        $last = $this->lastProduct();

        if ($total <= $this->currentPerPage() || $this->totalPages() === 1) {
            return sprintf(
                _n('Showing the single result', 'Showing all %d results', $total, 'sega-woo-theme'),
                $total
            );
        }

        return sprintf(
            __('Showing %1$d–%2$d of %3$d results', 'sega-woo-theme'),
            $first,
            $last,
            $total
        );
    }

    /**
     * Get the current orderby value.
     */
    public function currentOrderby(): string
    {
        return isset($_GET['orderby']) ? wc_clean(wp_unslash($_GET['orderby'])) : apply_filters('woocommerce_default_catalog_orderby', get_option('woocommerce_default_catalog_orderby', 'menu_order'));
    }

    /**
     * Get available sorting options.
     */
    public function sortingOptions(): array
    {
        $options = [];
        $catalog_orderby = apply_filters('woocommerce_catalog_orderby', [
            'menu_order' => __('Default sorting', 'woocommerce'),
            'popularity' => __('Sort by popularity', 'woocommerce'),
            'rating'     => __('Sort by average rating', 'woocommerce'),
            'date'       => __('Sort by latest', 'woocommerce'),
            'price'      => __('Sort by price: low to high', 'woocommerce'),
            'price-desc' => __('Sort by price: high to low', 'woocommerce'),
        ]);

        $current = $this->currentOrderby();

        foreach ($catalog_orderby as $value => $label) {
            $options[] = [
                'value'    => $value,
                'label'    => $label,
                'selected' => $current === $value,
                'url'      => $this->getSortingUrl($value),
            ];
        }

        return $options;
    }

    /**
     * Get the URL for a sorting option.
     */
    protected function getSortingUrl(string $orderby): string
    {
        $link = $this->getBaseShopUrl();
        return add_query_arg('orderby', $orderby, $link);
    }

    /**
     * Get the base shop URL preserving relevant query args.
     */
    protected function getBaseShopUrl(): string
    {
        $link = '';

        if (is_shop()) {
            $link = get_permalink(wc_get_page_id('shop'));
        } elseif (is_product_category()) {
            $link = get_term_link(get_queried_object());
        } elseif (is_product_tag()) {
            $link = get_term_link(get_queried_object());
        } elseif (is_search()) {
            $link = add_query_arg('s', get_search_query(), home_url('/'));
            $link = add_query_arg('post_type', 'product', $link);
        } else {
            $link = get_post_type_archive_link('product');
        }

        // Preserve existing filters
        $preserve = ['min_price', 'max_price', 'rating_filter', 'cat_ids', 'on_sale', 'in_stock', 'per_page'];
        foreach ($preserve as $param) {
            if (isset($_GET[$param]) && $_GET[$param] !== '') {
                $link = add_query_arg($param, wc_clean(wp_unslash($_GET[$param])), $link);
            }
        }

        return $link;
    }

    /**
     * Get product categories for filtering.
     *
     * Uses transient caching to avoid repeated database queries.
     * Category structure is cached; active states and URLs are computed fresh.
     */
    public function productCategories(): array
    {
        $cache_key = self::CACHE_PREFIX . 'categories';
        $cached_categories = get_transient($cache_key);

        if ($cached_categories === false) {
            $categories = get_terms([
                'taxonomy'   => 'product_cat',
                'hide_empty' => true,
                'parent'     => 0,
            ]);

            if (is_wp_error($categories)) {
                return [];
            }

            // Cache the basic category structure without dynamic values
            $cached_categories = array_map(function ($category) {
                return [
                    'id'       => $category->term_id,
                    'name'     => $category->name,
                    'slug'     => $category->slug,
                    'count'    => $category->count,
                    'children' => $this->getCachedChildCategories($category->term_id),
                ];
            }, $categories);

            set_transient($cache_key, $cached_categories, self::CACHE_EXPIRATION);
        }

        // Add dynamic values (URLs and active states) that can't be cached
        return array_map(function ($category) {
            $category['url'] = $this->getCategoryFilterUrl($category['slug']);
            $category['active'] = $this->isCategoryActive($category['slug']);

            // Add dynamic values to children
            if (! empty($category['children'])) {
                $category['children'] = array_map(function ($child) {
                    $child['url'] = $this->getCategoryFilterUrl($child['slug']);
                    $child['active'] = $this->isCategoryActive($child['slug']);
                    return $child;
                }, $category['children']);
            }

            return $category;
        }, $cached_categories);
    }

    /**
     * Get child categories for caching (without dynamic values).
     */
    protected function getCachedChildCategories(int $parent_id): array
    {
        $children = get_terms([
            'taxonomy'   => 'product_cat',
            'hide_empty' => true,
            'parent'     => $parent_id,
        ]);

        if (is_wp_error($children) || empty($children)) {
            return [];
        }

        return array_map(function ($child) {
            return [
                'id'    => $child->term_id,
                'name'  => $child->name,
                'slug'  => $child->slug,
                'count' => $child->count,
            ];
        }, $children);
    }

    /**
     * Get child categories for a parent category.
     */
    protected function getChildCategories(int $parent_id): array
    {
        $children = get_terms([
            'taxonomy'   => 'product_cat',
            'hide_empty' => true,
            'parent'     => $parent_id,
        ]);

        if (is_wp_error($children) || empty($children)) {
            return [];
        }

        return array_map(function ($child) {
            return [
                'id'     => $child->term_id,
                'name'   => $child->name,
                'slug'   => $child->slug,
                'count'  => $child->count,
                'url'    => $this->getCategoryFilterUrl($child->slug),
                'active' => $this->isCategoryActive($child->slug),
            ];
        }, $children);
    }

    /**
     * Generate a category filter URL that preserves existing filters.
     *
     * Uses query parameter approach on shop page for better filter preservation,
     * or category archive URL with preserved filters on category pages.
     */
    protected function getCategoryFilterUrl(string $slug): string
    {
        $base_url = get_permalink(wc_get_page_id('shop'));
        $url = add_query_arg('product_cat', $slug, $base_url);

        // Preserve existing filters
        $filters_to_preserve = ['min_price', 'max_price', 'on_sale', 'in_stock', 'orderby', 'per_page'];
        foreach ($filters_to_preserve as $filter) {
            if (isset($_GET[$filter]) && $_GET[$filter] !== '') {
                $url = add_query_arg($filter, wc_clean(wp_unslash($_GET[$filter])), $url);
            }
        }

        return $url;
    }

    /**
     * Check if a category is currently active (either via archive or query param).
     * Now uses term_id for comparison since we filter by ID.
     */
    protected function isCategoryActive(string $slug): bool
    {
        // Check if on category archive page
        if (is_product_category($slug)) {
            return true;
        }

        // Check if category is selected via query parameter (now using IDs)
        if (isset($_GET['cat_ids'])) {
            // Get the term by slug to find its ID
            $term = get_term_by('slug', $slug, 'product_cat');
            if ($term && ! is_wp_error($term)) {
                $selected_ids = array_map('absint', explode(',', wc_clean(wp_unslash($_GET['cat_ids']))));
                return in_array($term->term_id, $selected_ids, true);
            }
        }

        return false;
    }

    /**
     * Get the currently selected category slug (from archive or query param).
     * Returns the first selected category for backwards compatibility.
     */
    public function selectedCategory(): ?string
    {
        if (is_product_category()) {
            $term = get_queried_object();
            return $term->slug ?? null;
        }

        if (isset($_GET['cat_ids']) && ! empty($_GET['cat_ids'])) {
            $categories = array_map('sanitize_title', explode(',', wc_clean(wp_unslash($_GET['cat_ids']))));
            return $categories[0] ?? null;
        }

        return null;
    }

    /**
     * Get all currently selected category slugs (supports multi-select).
     *
     * @return array Array of selected category slugs
     */
    public function selectedCategories(): array
    {
        $selected = [];

        // On category archive page, that category is selected
        if (is_product_category()) {
            $term = get_queried_object();
            if ($term && isset($term->slug)) {
                $selected[] = $term->slug;
            }
        }

        // Categories selected via query parameter (comma-separated)
        if (isset($_GET['cat_ids']) && ! empty($_GET['cat_ids'])) {
            $param_categories = array_map('sanitize_title', explode(',', wc_clean(wp_unslash($_GET['cat_ids']))));
            $selected = array_merge($selected, $param_categories);
        }

        return array_unique($selected);
    }

    /**
     * Get the count of selected categories.
     */
    public function selectedCategoryCount(): int
    {
        return count($this->selectedCategories());
    }

    /**
     * Get the number of columns for the product grid.
     */
    public function gridColumns(): int
    {
        return (int) apply_filters('loop_shop_columns', wc_get_default_products_per_row());
    }

    /**
     * Get grid column classes for Tailwind.
     *
     * Provides responsive grid classes optimized for e-commerce:
     * - Extra Small (< 480px): 1 column for small phones
     * - Small (480px - 639px): 2 columns for larger phones
     * - Tablet (640px - 1023px): 2-3 columns for tablets
     * - Desktop (1024px - 1279px): 3-4 columns for laptops
     * - Large (1280px+): 4-5 columns for large screens
     *
     * Uses mobile-first approach with standard Tailwind breakpoints.
     */
    public function gridClasses(): string
    {
        $columns = $this->gridColumns();

        // Responsive grid configurations optimized for each target column count
        // Mobile-first: start with smallest screens, add breakpoints for larger
        $classes = [
            1 => 'grid-cols-1',
            2 => 'grid-cols-1 xs:grid-cols-2',
            3 => 'grid-cols-1 xs:grid-cols-2 md:grid-cols-3',
            4 => 'grid-cols-1 xs:grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4',
            5 => 'grid-cols-1 xs:grid-cols-2 sm:grid-cols-3 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5',
            6 => 'grid-cols-1 xs:grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6',
        ];

        return $classes[$columns] ?? $classes[4];
    }

    /**
     * Check if the shop has an active sidebar.
     */
    public function hasSidebar(): bool
    {
        return is_active_sidebar('sidebar-shop');
    }

    /**
     * Get active price filter values.
     */
    public function priceFilter(): array
    {
        return [
            'min' => isset($_GET['min_price']) ? (float) wc_clean(wp_unslash($_GET['min_price'])) : null,
            'max' => isset($_GET['max_price']) ? (float) wc_clean(wp_unslash($_GET['max_price'])) : null,
        ];
    }

    /**
     * Get the price range for all products.
     *
     * Uses transient caching to avoid expensive database queries.
     */
    public function priceRange(): array
    {
        $cache_key = self::CACHE_PREFIX . 'price_range';
        $cached_range = get_transient($cache_key);

        if ($cached_range !== false) {
            return $cached_range;
        }

        global $wpdb;

        $min = $wpdb->get_var("
            SELECT MIN(CAST(meta_value AS DECIMAL(10,2)))
            FROM {$wpdb->postmeta}
            WHERE meta_key = '_price'
            AND meta_value > 0
        ");

        $max = $wpdb->get_var("
            SELECT MAX(CAST(meta_value AS DECIMAL(10,2)))
            FROM {$wpdb->postmeta}
            WHERE meta_key = '_price'
        ");

        $range = [
            'min' => (float) ($min ?: 0),
            'max' => (float) ($max ?: 0),
        ];

        set_transient($cache_key, $range, self::CACHE_EXPIRATION);

        return $range;
    }

    /**
     * Get active filters summary for display.
     */
    public function activeFilters(): array
    {
        $filters = [];

        // On category pages: don't show the category itself as a filter
        // The category page IS the context, not an active filter
        // Only show subcategories (cat_ids) and other filters the user actively selects

        // Category filter (query parameter on shop page only)
        // On category pages, cat_ids represents subcategory selections
        if (is_shop() && isset($_GET['cat_ids']) && ! empty($_GET['cat_ids'])) {
            $raw_param = wc_clean(wp_unslash($_GET['cat_ids']));
            $raw_ids = array_filter(array_map('trim', explode(',', $raw_param)));

            // Find unique terms by term_id
            $unique_terms = [];
            $added_term_ids = [];

            foreach ($raw_ids as $raw_id) {
                $term_id = absint($raw_id);
                if ($term_id <= 0 || in_array($term_id, $added_term_ids, true)) {
                    continue;
                }

                $term = get_term($term_id, 'product_cat');
                if ($term && ! is_wp_error($term)) {
                    $unique_terms[] = $term;
                    $added_term_ids[] = $term_id;
                }
            }

            // Build filters from unique terms
            foreach ($unique_terms as $term) {
                // Build remove URL with remaining term IDs
                $remaining_ids = array_map(
                    fn($t) => $t->term_id,
                    array_filter($unique_terms, fn($t) => $t->term_id !== $term->term_id)
                );

                // Build remove URL - stays on current page (category or shop)
                $remove_url = empty($remaining_ids)
                    ? remove_query_arg('cat_ids')
                    : add_query_arg('cat_ids', implode(',', $remaining_ids));

                $filters[] = [
                    'type'       => 'category',
                    'label'      => $term->name,
                    'id'         => $term->term_id,
                    'remove_url' => $remove_url,
                ];
            }
        }

        // Subcategory filter on category pages (cat_ids parameter)
        if (is_product_category() && isset($_GET['cat_ids']) && ! empty($_GET['cat_ids'])) {
            $raw_param = wc_clean(wp_unslash($_GET['cat_ids']));
            $raw_ids = array_filter(array_map('trim', explode(',', $raw_param)));

            // Find unique terms by term_id
            $unique_terms = [];
            $added_term_ids = [];

            foreach ($raw_ids as $raw_id) {
                $term_id = absint($raw_id);
                if ($term_id <= 0 || in_array($term_id, $added_term_ids, true)) {
                    continue;
                }

                $term = get_term($term_id, 'product_cat');
                if ($term && ! is_wp_error($term)) {
                    $unique_terms[] = $term;
                    $added_term_ids[] = $term_id;
                }
            }

            // Build filters from unique terms
            foreach ($unique_terms as $term) {
                // Build remove URL with remaining term IDs
                $remaining_ids = array_map(
                    fn($t) => $t->term_id,
                    array_filter($unique_terms, fn($t) => $t->term_id !== $term->term_id)
                );

                // Stay on category page when removing subcategory filter
                $remove_url = empty($remaining_ids)
                    ? remove_query_arg('cat_ids')
                    : add_query_arg('cat_ids', implode(',', $remaining_ids));

                $filters[] = [
                    'type'       => 'category',
                    'label'      => $term->name,
                    'id'         => $term->term_id,
                    'remove_url' => $remove_url,
                ];
            }
        }

        // Price filter
        $price = $this->priceFilter();
        if ($price['min'] !== null || $price['max'] !== null) {
            $label = '';
            if ($price['min'] !== null && $price['max'] !== null) {
                $label = sprintf('%s - %s', wc_price($price['min']), wc_price($price['max']));
            } elseif ($price['min'] !== null) {
                $label = sprintf(__('From %s', 'sega-woo-theme'), wc_price($price['min']));
            } else {
                $label = sprintf(__('Up to %s', 'sega-woo-theme'), wc_price($price['max']));
            }

            $filters[] = [
                'type'       => 'price',
                'label'      => $label,
                'remove_url' => remove_query_arg(['min_price', 'max_price']),
            ];
        }

        // Search filter
        if (is_search()) {
            $filters[] = [
                'type'       => 'search',
                'label'      => sprintf(__('Search: "%s"', 'sega-woo-theme'), get_search_query()),
                'remove_url' => get_permalink(wc_get_page_id('shop')),
            ];
        }

        // On Sale filter
        if (isset($_GET['on_sale']) && $_GET['on_sale'] === '1') {
            $filters[] = [
                'type'       => 'on_sale',
                'label'      => __('On Sale', 'sega-woo-theme'),
                'remove_url' => remove_query_arg('on_sale'),
            ];
        }

        // In Stock filter
        if (isset($_GET['in_stock']) && $_GET['in_stock'] === '1') {
            $filters[] = [
                'type'       => 'in_stock',
                'label'      => __('In Stock', 'sega-woo-theme'),
                'remove_url' => remove_query_arg('in_stock'),
            ];
        }

        return $filters;
    }

    /**
     * Get breadcrumb data.
     */
    public function breadcrumbs(): array
    {
        $breadcrumbs = [];

        // Home
        $breadcrumbs[] = [
            'label' => __('Home', 'sega-woo-theme'),
            'url'   => home_url('/'),
        ];

        // Shop
        $shop_page_id = wc_get_page_id('shop');
        if ($shop_page_id > 0) {
            $breadcrumbs[] = [
                'label' => get_the_title($shop_page_id),
                'url'   => is_shop() ? '' : get_permalink($shop_page_id),
            ];
        }

        // Category hierarchy
        if (is_product_category()) {
            $current_term = get_queried_object();
            $ancestors = get_ancestors($current_term->term_id, 'product_cat');
            $ancestors = array_reverse($ancestors);

            foreach ($ancestors as $ancestor_id) {
                $ancestor = get_term($ancestor_id, 'product_cat');
                $breadcrumbs[] = [
                    'label' => $ancestor->name,
                    'url'   => get_term_link($ancestor),
                ];
            }

            $breadcrumbs[] = [
                'label' => $current_term->name,
                'url'   => '',
            ];
        }

        // Tag
        if (is_product_tag()) {
            $current_term = get_queried_object();
            $breadcrumbs[] = [
                'label' => sprintf(__('Tag: %s', 'sega-woo-theme'), $current_term->name),
                'url'   => '',
            ];
        }

        // Search
        if (is_search()) {
            $breadcrumbs[] = [
                'label' => sprintf(__('Search: "%s"', 'sega-woo-theme'), get_search_query()),
                'url'   => '',
            ];
        }

        return $breadcrumbs;
    }

    /**
     * Get the shop page URL.
     */
    public function shopUrl(): string
    {
        return get_permalink(wc_get_page_id('shop')) ?: '';
    }

    /**
     * Check if viewing filtered results.
     */
    public function isFiltered(): bool
    {
        return ! empty($this->activeFilters());
    }

    /**
     * Get total count of all products.
     *
     * Uses transient caching to avoid repeated queries.
     */
    public function totalAllProducts(): int
    {
        $cache_key = self::CACHE_PREFIX . 'total_products';
        $cached_count = get_transient($cache_key);

        if ($cached_count !== false) {
            return (int) $cached_count;
        }

        $count = wp_count_posts('product');
        $total = (int) $count->publish;

        set_transient($cache_key, $total, self::CACHE_EXPIRATION);

        return $total;
    }

    /**
     * Check if a category has an active child.
     */
    public function hasActiveChild(array $category): bool
    {
        if (empty($category['children'])) {
            return false;
        }

        foreach ($category['children'] as $child) {
            if ($child['active']) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get categories for the filter sidebar based on context.
     *
     * On the main shop page: returns all top-level categories with children.
     * On a category page: returns only subcategories of the current category.
     *
     * @return array
     */
    public function filterCategories(): array
    {
        // If on a category page, return subcategories of that category
        if (is_product_category()) {
            $currentTerm = get_queried_object();

            if ($currentTerm && ! is_wp_error($currentTerm)) {
                return $this->getSubcategoriesForFilter($currentTerm->term_id);
            }
        }

        // Default: return all categories (for main shop page)
        return $this->productCategories();
    }

    /**
     * Get subcategories of a parent category for the filter sidebar.
     *
     * @param int $parentId The parent category term ID
     * @return array
     */
    protected function getSubcategoriesForFilter(int $parentId): array
    {
        $subcategories = get_terms([
            'taxonomy'   => 'product_cat',
            'hide_empty' => true,
            'parent'     => $parentId,
        ]);

        if (is_wp_error($subcategories) || empty($subcategories)) {
            return [];
        }

        return array_map(function ($term) {
            return [
                'id'       => $term->term_id,
                'name'     => $term->name,
                'slug'     => $term->slug,
                'count'    => $term->count,
                'url'      => $this->getCategoryFilterUrl($term->slug),
                'active'   => $this->isCategoryActive($term->slug),
                'children' => $this->getChildCategories($term->term_id),
            ];
        }, $subcategories);
    }

    /**
     * Get the parent category info when on a category page.
     * Used to show a "back to parent" or "all in category" option.
     *
     * @return array|null
     */
    public function parentCategoryInfo(): ?array
    {
        if (! is_product_category()) {
            return null;
        }

        $currentTerm = get_queried_object();

        if (! $currentTerm || is_wp_error($currentTerm)) {
            return null;
        }

        // Get total product count for current category (including subcategories)
        $totalInCategory = $this->getCategoryProductCount($currentTerm->term_id);

        return [
            'id'    => $currentTerm->term_id,
            'name'  => $currentTerm->name,
            'slug'  => $currentTerm->slug,
            'count' => $totalInCategory,
            'url'   => get_term_link($currentTerm),
        ];
    }

    /**
     * Get total product count for a category including all subcategories.
     *
     * @param int $termId
     * @return int
     */
    protected function getCategoryProductCount(int $termId): int
    {
        $term = get_term($termId, 'product_cat');

        if (! $term || is_wp_error($term)) {
            return 0;
        }

        // Get direct count
        $count = (int) $term->count;

        // Add counts from subcategories
        $children = get_terms([
            'taxonomy'   => 'product_cat',
            'hide_empty' => true,
            'parent'     => $termId,
        ]);

        if (! is_wp_error($children) && ! empty($children)) {
            foreach ($children as $child) {
                $count += $this->getCategoryProductCount($child->term_id);
            }
        }

        return $count;
    }

}
