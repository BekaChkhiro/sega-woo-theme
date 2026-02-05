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
        'woocommerce.loop.*',
        'partials.woocommerce.*',
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
                __('Search results: "%s"', 'sage'),
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
     * Get the current page number.
     */
    public function currentPage(): int
    {
        return max(1, get_query_var('paged', 1));
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
        $per_page = $this->productsPerPage();
        $current = $this->currentPage();

        return (($current - 1) * $per_page) + 1;
    }

    /**
     * Get the last product number on current page.
     */
    public function lastProduct(): int
    {
        $per_page = $this->productsPerPage();
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

        if ($total <= $this->productsPerPage() || $this->totalPages() === 1) {
            return sprintf(
                _n('Showing the single result', 'Showing all %d results', $total, 'sage'),
                $total
            );
        }

        return sprintf(
            __('Showing %1$d–%2$d of %3$d results', 'sage'),
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
        $preserve = ['min_price', 'max_price', 'rating_filter', 'product_cat', 'on_sale', 'in_stock'];
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
        $filters_to_preserve = ['min_price', 'max_price', 'on_sale', 'in_stock', 'orderby'];
        foreach ($filters_to_preserve as $filter) {
            if (isset($_GET[$filter]) && $_GET[$filter] !== '') {
                $url = add_query_arg($filter, wc_clean(wp_unslash($_GET[$filter])), $url);
            }
        }

        return $url;
    }

    /**
     * Check if a category is currently active (either via archive or query param).
     */
    protected function isCategoryActive(string $slug): bool
    {
        // Check if on category archive page
        if (is_product_category($slug)) {
            return true;
        }

        // Check if category is selected via query parameter
        if (isset($_GET['product_cat'])) {
            $selected_cats = array_map('sanitize_title', explode(',', wc_clean(wp_unslash($_GET['product_cat']))));
            return in_array($slug, $selected_cats, true);
        }

        return false;
    }

    /**
     * Get the currently selected category slug (from archive or query param).
     */
    public function selectedCategory(): ?string
    {
        if (is_product_category()) {
            $term = get_queried_object();
            return $term->slug ?? null;
        }

        if (isset($_GET['product_cat']) && ! empty($_GET['product_cat'])) {
            return sanitize_title(wc_clean(wp_unslash($_GET['product_cat'])));
        }

        return null;
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

        // Category filter (archive page)
        if (is_product_category()) {
            $term = get_queried_object();
            $remove_url = get_permalink(wc_get_page_id('shop'));

            // Preserve other filters when removing category
            $filters_to_preserve = ['min_price', 'max_price', 'on_sale', 'in_stock', 'orderby'];
            foreach ($filters_to_preserve as $filter) {
                if (isset($_GET[$filter]) && $_GET[$filter] !== '') {
                    $remove_url = add_query_arg($filter, wc_clean(wp_unslash($_GET[$filter])), $remove_url);
                }
            }

            $filters[] = [
                'type'       => 'category',
                'label'      => $term->name,
                'remove_url' => $remove_url,
            ];
        }

        // Category filter (query parameter on shop page)
        if (is_shop() && isset($_GET['product_cat']) && ! empty($_GET['product_cat'])) {
            $category_slug = sanitize_title(wc_clean(wp_unslash($_GET['product_cat'])));
            $term = get_term_by('slug', $category_slug, 'product_cat');

            if ($term && ! is_wp_error($term)) {
                $filters[] = [
                    'type'       => 'category',
                    'label'      => $term->name,
                    'remove_url' => remove_query_arg('product_cat'),
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
                $label = sprintf(__('From %s', 'sage'), wc_price($price['min']));
            } else {
                $label = sprintf(__('Up to %s', 'sage'), wc_price($price['max']));
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
                'label'      => sprintf(__('Search: "%s"', 'sage'), get_search_query()),
                'remove_url' => get_permalink(wc_get_page_id('shop')),
            ];
        }

        // On Sale filter
        if (isset($_GET['on_sale']) && $_GET['on_sale'] === '1') {
            $filters[] = [
                'type'       => 'on_sale',
                'label'      => __('On Sale', 'sage'),
                'remove_url' => remove_query_arg('on_sale'),
            ];
        }

        // In Stock filter
        if (isset($_GET['in_stock']) && $_GET['in_stock'] === '1') {
            $filters[] = [
                'type'       => 'in_stock',
                'label'      => __('In Stock', 'sage'),
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
            'label' => __('Home', 'sage'),
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
                'label' => sprintf(__('Tag: %s', 'sage'), $current_term->name),
                'url'   => '',
            ];
        }

        // Search
        if (is_search()) {
            $breadcrumbs[] = [
                'label' => sprintf(__('Search: "%s"', 'sage'), get_search_query()),
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

}
