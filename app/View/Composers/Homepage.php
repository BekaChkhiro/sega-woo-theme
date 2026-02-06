<?php

namespace App\View\Composers;

use Roots\Acorn\View\Composer;

class Homepage extends Composer
{
    /**
     * List of views served by this composer.
     *
     * @var array
     */
    protected static $views = [
        'front-page',
    ];

    /**
     * Cache expiration time in seconds (1 hour).
     */
    protected const CACHE_EXPIRATION = HOUR_IN_SECONDS;

    /**
     * Cache key prefix.
     */
    protected const CACHE_PREFIX = 'sage_homepage_';

    /**
     * Clear all homepage-related transient caches.
     *
     * Should be called when products or categories change.
     */
    public static function clearCache(): void
    {
        delete_transient(self::CACHE_PREFIX . 'new_products');
        delete_transient(self::CACHE_PREFIX . 'sale_products');
        delete_transient(self::CACHE_PREFIX . 'bestsellers');
        delete_transient(self::CACHE_PREFIX . 'featured_categories');
    }

    /**
     * Clear specific cache by type.
     */
    public static function clearCacheByType(string $type): void
    {
        delete_transient(self::CACHE_PREFIX . $type);
    }

    /**
     * Get new products (recently added).
     *
     * @param int $limit Number of products to retrieve
     * @return array Array of WC_Product objects
     */
    public function newProducts(int $limit = 12): array
    {
        $cache_key = self::CACHE_PREFIX . 'new_products_' . $limit;
        $cached_ids = get_transient($cache_key);

        if ($cached_ids !== false && is_array($cached_ids)) {
            // Reconstruct WC_Product objects from cached IDs
            return array_filter(array_map('wc_get_product', $cached_ids));
        }

        if (! function_exists('wc_get_products')) {
            return [];
        }

        $products = wc_get_products([
            'limit' => $limit,
            'orderby' => 'date',
            'order' => 'DESC',
            'status' => 'publish',
            'visibility' => 'visible',
        ]);

        // Cache only product IDs to avoid serialization issues
        $product_ids = array_map(function ($product) {
            return $product->get_id();
        }, $products);

        set_transient($cache_key, $product_ids, self::CACHE_EXPIRATION);

        return $products;
    }

    /**
     * Get products on sale.
     *
     * @param int $limit Number of products to retrieve
     * @return array Array of WC_Product objects
     */
    public function saleProducts(int $limit = 12): array
    {
        $cache_key = self::CACHE_PREFIX . 'sale_products_' . $limit;
        $cached_ids = get_transient($cache_key);

        if ($cached_ids !== false && is_array($cached_ids)) {
            return array_filter(array_map('wc_get_product', $cached_ids));
        }

        if (! function_exists('wc_get_products')) {
            return [];
        }

        $products = wc_get_products([
            'limit' => $limit,
            'orderby' => 'date',
            'order' => 'DESC',
            'status' => 'publish',
            'visibility' => 'visible',
            'on_sale' => true,
        ]);

        $product_ids = array_map(function ($product) {
            return $product->get_id();
        }, $products);

        set_transient($cache_key, $product_ids, self::CACHE_EXPIRATION);

        return $products;
    }

    /**
     * Get bestselling products.
     *
     * @param int $limit Number of products to retrieve
     * @return array Array of WC_Product objects
     */
    public function bestsellers(int $limit = 12): array
    {
        $cache_key = self::CACHE_PREFIX . 'bestsellers_' . $limit;
        $cached_ids = get_transient($cache_key);

        if ($cached_ids !== false && is_array($cached_ids)) {
            return array_filter(array_map('wc_get_product', $cached_ids));
        }

        if (! function_exists('wc_get_products')) {
            return [];
        }

        $products = wc_get_products([
            'limit' => $limit,
            'orderby' => 'popularity',
            'order' => 'DESC',
            'status' => 'publish',
            'visibility' => 'visible',
        ]);

        $product_ids = array_map(function ($product) {
            return $product->get_id();
        }, $products);

        set_transient($cache_key, $product_ids, self::CACHE_EXPIRATION);

        return $products;
    }

    /**
     * Get featured products.
     *
     * @param int $limit Number of products to retrieve
     * @return array Array of WC_Product objects
     */
    public function featuredProducts(int $limit = 8): array
    {
        $cache_key = self::CACHE_PREFIX . 'featured_products_' . $limit;
        $cached_ids = get_transient($cache_key);

        if ($cached_ids !== false && is_array($cached_ids)) {
            return array_filter(array_map('wc_get_product', $cached_ids));
        }

        if (! function_exists('wc_get_products')) {
            return [];
        }

        $products = wc_get_products([
            'limit' => $limit,
            'featured' => true,
            'orderby' => 'date',
            'order' => 'DESC',
            'status' => 'publish',
            'visibility' => 'visible',
        ]);

        $product_ids = array_map(function ($product) {
            return $product->get_id();
        }, $products);

        set_transient($cache_key, $product_ids, self::CACHE_EXPIRATION);

        return $products;
    }

    /**
     * Get featured categories for homepage.
     *
     * @param int $limit Number of categories to retrieve
     * @return array
     */
    public function featuredCategories(int $limit = 6): array
    {
        $cache_key = self::CACHE_PREFIX . 'featured_categories_' . $limit;
        $cached = get_transient($cache_key);

        if ($cached !== false) {
            return $cached;
        }

        if (! function_exists('get_terms')) {
            return [];
        }

        $categories = get_terms([
            'taxonomy' => 'product_cat',
            'hide_empty' => true,
            'parent' => 0,
            'number' => $limit,
            'orderby' => 'count',
            'order' => 'DESC',
        ]);

        if (is_wp_error($categories)) {
            return [];
        }

        $formatted = array_map(function ($category) {
            $thumbnail_id = get_term_meta($category->term_id, 'thumbnail_id', true);
            $thumbnail = $thumbnail_id ? wp_get_attachment_image_url($thumbnail_id, 'medium') : null;

            return [
                'id' => $category->term_id,
                'name' => $category->name,
                'slug' => $category->slug,
                'count' => $category->count,
                'url' => get_term_link($category),
                'thumbnail' => $thumbnail,
            ];
        }, $categories);

        set_transient($cache_key, $formatted, self::CACHE_EXPIRATION);

        return $formatted;
    }

    /**
     * Get top-level product categories for mega menu.
     *
     * @param int $limit Number of categories (0 = all)
     * @return array
     */
    public function megaMenuCategories(int $limit = 0): array
    {
        $cache_key = self::CACHE_PREFIX . 'mega_menu_cats_' . $limit;
        $cached = get_transient($cache_key);

        if ($cached !== false) {
            return $cached;
        }

        if (! function_exists('get_terms')) {
            return [];
        }

        $args = [
            'taxonomy' => 'product_cat',
            'hide_empty' => true,
            'parent' => 0,
            'orderby' => 'menu_order',
            'order' => 'ASC',
        ];

        if ($limit > 0) {
            $args['number'] = $limit;
        }

        $categories = get_terms($args);

        if (is_wp_error($categories)) {
            return [];
        }

        $formatted = array_map(function ($category) {
            $thumbnail_id = get_term_meta($category->term_id, 'thumbnail_id', true);
            $thumbnail = $thumbnail_id ? wp_get_attachment_image_url($thumbnail_id, 'thumbnail') : null;

            return [
                'id' => $category->term_id,
                'name' => $category->name,
                'slug' => $category->slug,
                'count' => $category->count,
                'url' => get_term_link($category),
                'thumbnail' => $thumbnail,
                'children' => $this->getCategoryChildren($category->term_id),
            ];
        }, $categories);

        set_transient($cache_key, $formatted, self::CACHE_EXPIRATION);

        return $formatted;
    }

    /**
     * Get child categories for a parent category.
     *
     * @param int $parent_id Parent category ID
     * @return array
     */
    protected function getCategoryChildren(int $parent_id): array
    {
        if (! function_exists('get_terms')) {
            return [];
        }

        $children = get_terms([
            'taxonomy' => 'product_cat',
            'hide_empty' => true,
            'parent' => $parent_id,
            'orderby' => 'menu_order',
            'order' => 'ASC',
        ]);

        if (is_wp_error($children) || empty($children)) {
            return [];
        }

        return array_map(function ($child) {
            $thumbnail_id = get_term_meta($child->term_id, 'thumbnail_id', true);
            $thumbnail = $thumbnail_id ? wp_get_attachment_image_url($thumbnail_id, 'thumbnail') : null;

            return [
                'id' => $child->term_id,
                'name' => $child->name,
                'slug' => $child->slug,
                'count' => $child->count,
                'url' => get_term_link($child),
                'thumbnail' => $thumbnail,
            ];
        }, $children);
    }

    /**
     * Check if there are new products available.
     *
     * @return bool
     */
    public function hasNewProducts(): bool
    {
        return ! empty($this->newProducts());
    }

    /**
     * Check if there are sale products available.
     *
     * @return bool
     */
    public function hasSaleProducts(): bool
    {
        return ! empty($this->saleProducts());
    }

    /**
     * Check if there are bestsellers available.
     *
     * @return bool
     */
    public function hasBestsellers(): bool
    {
        return ! empty($this->bestsellers());
    }

    /**
     * Check if there are featured categories available.
     *
     * @return bool
     */
    public function hasFeaturedCategories(): bool
    {
        return ! empty($this->featuredCategories());
    }

    /**
     * Get shop page URL.
     *
     * @return string
     */
    public function shopUrl(): string
    {
        if (! function_exists('wc_get_page_permalink')) {
            return '/shop';
        }

        return wc_get_page_permalink('shop') ?: '/shop';
    }

    /**
     * Get shop URL with specific filter.
     *
     * @param string $filter Filter type (orderby, on_sale, etc.)
     * @param string $value Filter value
     * @return string
     */
    public function shopUrlWithFilter(string $filter, string $value): string
    {
        $base_url = $this->shopUrl();

        return add_query_arg($filter, $value, $base_url);
    }

    /**
     * Get "New Arrivals" shop URL.
     *
     * @return string
     */
    public function newArrivalsUrl(): string
    {
        return $this->shopUrlWithFilter('orderby', 'date');
    }

    /**
     * Get "On Sale" shop URL.
     *
     * @return string
     */
    public function onSaleUrl(): string
    {
        return $this->shopUrlWithFilter('on_sale', 'true');
    }

    /**
     * Get "Bestsellers" shop URL.
     *
     * @return string
     */
    public function bestsellersUrl(): string
    {
        return $this->shopUrlWithFilter('orderby', 'popularity');
    }

    /**
     * Get homepage slider settings from Customizer.
     *
     * @return array
     */
    public function sliderSettings(): array
    {
        return \App\Customizer\HomepageSlider::getSliderSettings();
    }

    /**
     * Get slider slides from Customizer.
     *
     * @return array
     */
    public function sliderSlides(): array
    {
        return \App\Customizer\HomepageSlider::getSlides();
    }

    /**
     * Check if slider has any configured slides.
     *
     * @return bool
     */
    public function hasSliderSlides(): bool
    {
        return \App\Customizer\HomepageSlider::hasSlides();
    }

    /**
     * Data to be passed to view.
     *
     * @return array
     */
    public function with(): array
    {
        return [
            // Products
            'newProducts' => $this->newProducts(),
            'saleProducts' => $this->saleProducts(),
            'bestsellers' => $this->bestsellers(),
            'featuredProducts' => $this->featuredProducts(),

            // Categories (fetch more for carousel scrolling)
            'featuredCategories' => $this->featuredCategories(12),
            'megaMenuCategories' => $this->megaMenuCategories(),

            // Slider settings from Customizer
            'sliderSettings' => $this->sliderSettings(),
            'sliderSlides' => $this->sliderSlides(),
            'hasSliderSlides' => $this->hasSliderSlides(),

            // Boolean checks
            'hasNewProducts' => $this->hasNewProducts(),
            'hasSaleProducts' => $this->hasSaleProducts(),
            'hasBestsellers' => $this->hasBestsellers(),
            'hasFeaturedCategories' => $this->hasFeaturedCategories(),

            // URLs
            'shopUrl' => $this->shopUrl(),
            'newArrivalsUrl' => $this->newArrivalsUrl(),
            'onSaleUrl' => $this->onSaleUrl(),
            'bestsellersUrl' => $this->bestsellersUrl(),
        ];
    }
}
