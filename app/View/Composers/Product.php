<?php

namespace App\View\Composers;

use Roots\Acorn\View\Composer;
use WC_Product;
use WC_Product_Variable;

class Product extends Composer
{
    /**
     * List of views served by this composer.
     *
     * @var array
     */
    protected static $views = [
        'woocommerce.single-product',
        'woocommerce.single-product.*',
        'woocommerce.content-single-product',
    ];

    /**
     * Get the current product.
     */
    protected function getProduct(): ?WC_Product
    {
        global $product;

        if (! $product instanceof WC_Product) {
            $product = wc_get_product(get_the_ID());
        }

        return $product instanceof WC_Product ? $product : null;
    }

    /**
     * Get the product ID.
     */
    public function productId(): int
    {
        $product = $this->getProduct();
        return $product ? $product->get_id() : 0;
    }

    /**
     * Get the product name/title.
     */
    public function productName(): string
    {
        $product = $this->getProduct();
        return $product ? $product->get_name() : '';
    }

    /**
     * Get the product permalink.
     */
    public function productUrl(): string
    {
        $product = $this->getProduct();
        return $product ? $product->get_permalink() : '';
    }

    /**
     * Get the product type.
     */
    public function productType(): string
    {
        $product = $this->getProduct();
        return $product ? $product->get_type() : '';
    }

    /**
     * Check if product is a simple product.
     */
    public function isSimple(): bool
    {
        return $this->productType() === 'simple';
    }

    /**
     * Check if product is a variable product.
     */
    public function isVariable(): bool
    {
        return $this->productType() === 'variable';
    }

    /**
     * Check if product is a grouped product.
     */
    public function isGrouped(): bool
    {
        return $this->productType() === 'grouped';
    }

    /**
     * Check if product is an external/affiliate product.
     */
    public function isExternal(): bool
    {
        return $this->productType() === 'external';
    }

    /**
     * Get the short description.
     */
    public function shortDescription(): string
    {
        $product = $this->getProduct();
        return $product ? $product->get_short_description() : '';
    }

    /**
     * Get the full description.
     */
    public function description(): string
    {
        $product = $this->getProduct();
        return $product ? $product->get_description() : '';
    }

    /**
     * Get the product SKU.
     */
    public function sku(): string
    {
        $product = $this->getProduct();
        return $product ? $product->get_sku() : '';
    }

    /**
     * Get the regular price.
     */
    public function regularPrice(): string
    {
        $product = $this->getProduct();
        return $product ? $product->get_regular_price() : '';
    }

    /**
     * Get the sale price.
     */
    public function salePrice(): string
    {
        $product = $this->getProduct();
        return $product ? $product->get_sale_price() : '';
    }

    /**
     * Get the current price (sale price if on sale, otherwise regular).
     */
    public function currentPrice(): string
    {
        $product = $this->getProduct();
        return $product ? $product->get_price() : '';
    }

    /**
     * Get formatted price HTML.
     */
    public function priceHtml(): string
    {
        $product = $this->getProduct();
        return $product ? $product->get_price_html() : '';
    }

    /**
     * Check if product is on sale.
     */
    public function isOnSale(): bool
    {
        $product = $this->getProduct();
        return $product ? $product->is_on_sale() : false;
    }

    /**
     * Get sale percentage discount.
     *
     * For simple products: calculates the percentage from regular to sale price.
     * For variable products: returns the maximum discount percentage across all variations.
     */
    public function salePercentage(): int
    {
        $product = $this->getProduct();

        if (! $product || ! $product->is_on_sale()) {
            return 0;
        }

        // Handle variable products - find the maximum discount across variations
        if ($product instanceof WC_Product_Variable) {
            $max_percentage = 0;
            $variations = $product->get_available_variations();

            foreach ($variations as $variation) {
                $regular = (float) ($variation['display_regular_price'] ?? 0);
                $sale = (float) ($variation['display_price'] ?? 0);

                if ($regular > 0 && $sale > 0 && $sale < $regular) {
                    $percentage = round((($regular - $sale) / $regular) * 100);
                    $max_percentage = max($max_percentage, $percentage);
                }
            }

            return (int) $max_percentage;
        }

        // Handle simple and other product types
        $regular = (float) $product->get_regular_price();
        $sale = (float) $product->get_sale_price();

        if ($regular <= 0 || $sale <= 0 || $sale >= $regular) {
            return 0;
        }

        return (int) round((($regular - $sale) / $regular) * 100);
    }

    /**
     * Check if product is purchasable.
     */
    public function isPurchasable(): bool
    {
        $product = $this->getProduct();
        return $product ? $product->is_purchasable() : false;
    }

    /**
     * Check if product is in stock.
     */
    public function isInStock(): bool
    {
        $product = $this->getProduct();
        return $product ? $product->is_in_stock() : false;
    }

    /**
     * Get stock status.
     */
    public function stockStatus(): string
    {
        $product = $this->getProduct();
        return $product ? $product->get_stock_status() : '';
    }

    /**
     * Get stock quantity.
     */
    public function stockQuantity(): ?int
    {
        $product = $this->getProduct();
        return $product ? $product->get_stock_quantity() : null;
    }

    /**
     * Check if stock management is enabled.
     */
    public function managesStock(): bool
    {
        $product = $this->getProduct();
        return $product ? $product->managing_stock() : false;
    }

    /**
     * Get stock availability text.
     */
    public function stockAvailability(): string
    {
        $product = $this->getProduct();

        if (! $product) {
            return '';
        }

        $availability = $product->get_availability();
        return $availability['availability'] ?? '';
    }

    /**
     * Get stock availability class.
     */
    public function stockAvailabilityClass(): string
    {
        $product = $this->getProduct();

        if (! $product) {
            return '';
        }

        $availability = $product->get_availability();
        return $availability['class'] ?? '';
    }

    /**
     * Check if backorders are allowed.
     */
    public function backordersAllowed(): bool
    {
        $product = $this->getProduct();
        return $product ? $product->backorders_allowed() : false;
    }

    /**
     * Get the main product image ID.
     */
    public function mainImageId(): int
    {
        $product = $this->getProduct();
        return $product ? (int) $product->get_image_id() : 0;
    }

    /**
     * Get the main product image URL.
     */
    public function mainImageUrl(string $size = 'woocommerce_single'): string
    {
        $image_id = $this->mainImageId();

        if (! $image_id) {
            return wc_placeholder_img_src($size);
        }

        $image_url = wp_get_attachment_image_url($image_id, $size);
        return $image_url ?: wc_placeholder_img_src($size);
    }

    /**
     * Get the main product image HTML.
     */
    public function mainImage(string $size = 'woocommerce_single'): string
    {
        $product = $this->getProduct();

        if (! $product) {
            return '';
        }

        $image_id = $product->get_image_id();

        if (! $image_id) {
            return wc_placeholder_img($size);
        }

        return wp_get_attachment_image($image_id, $size, false, [
            'class' => 'w-full h-auto object-cover',
            'alt'   => $this->productName(),
        ]);
    }

    /**
     * Get gallery image IDs.
     */
    public function galleryImageIds(): array
    {
        $product = $this->getProduct();
        return $product ? $product->get_gallery_image_ids() : [];
    }

    /**
     * Get all product images (main + gallery).
     */
    public function allImages(string $size = 'woocommerce_single', string $thumbSize = 'woocommerce_thumbnail'): array
    {
        $images = [];
        $product = $this->getProduct();

        if (! $product) {
            return $images;
        }

        // Main image
        $main_id = $product->get_image_id();
        if ($main_id) {
            $images[] = [
                'id'        => $main_id,
                'url'       => wp_get_attachment_image_url($main_id, $size),
                'full_url'  => wp_get_attachment_image_url($main_id, 'full'),
                'thumb_url' => wp_get_attachment_image_url($main_id, $thumbSize),
                'alt'       => get_post_meta($main_id, '_wp_attachment_image_alt', true) ?: $this->productName(),
                'srcset'    => wp_get_attachment_image_srcset($main_id, $size),
                'sizes'     => wp_get_attachment_image_sizes($main_id, $size),
                'is_main'   => true,
            ];
        } else {
            // Placeholder for products without images
            $images[] = [
                'id'        => 0,
                'url'       => wc_placeholder_img_src($size),
                'full_url'  => wc_placeholder_img_src('full'),
                'thumb_url' => wc_placeholder_img_src($thumbSize),
                'alt'       => $this->productName(),
                'srcset'    => '',
                'sizes'     => '',
                'is_main'   => true,
            ];
        }

        // Gallery images
        foreach ($this->galleryImageIds() as $gallery_id) {
            $images[] = [
                'id'        => $gallery_id,
                'url'       => wp_get_attachment_image_url($gallery_id, $size),
                'full_url'  => wp_get_attachment_image_url($gallery_id, 'full'),
                'thumb_url' => wp_get_attachment_image_url($gallery_id, $thumbSize),
                'alt'       => get_post_meta($gallery_id, '_wp_attachment_image_alt', true) ?: $this->productName(),
                'srcset'    => wp_get_attachment_image_srcset($gallery_id, $size),
                'sizes'     => wp_get_attachment_image_sizes($gallery_id, $size),
                'is_main'   => false,
            ];
        }

        return $images;
    }

    /**
     * Check if product has a gallery.
     */
    public function hasGallery(): bool
    {
        return count($this->galleryImageIds()) > 0;
    }

    /**
     * Get product categories.
     */
    public function categories(): array
    {
        $product = $this->getProduct();

        if (! $product) {
            return [];
        }

        $term_ids = $product->get_category_ids();
        $categories = [];

        foreach ($term_ids as $term_id) {
            $term = get_term($term_id, 'product_cat');
            if ($term && ! is_wp_error($term)) {
                $categories[] = [
                    'id'   => $term->term_id,
                    'name' => $term->name,
                    'slug' => $term->slug,
                    'url'  => get_term_link($term),
                ];
            }
        }

        return $categories;
    }

    /**
     * Get product tags.
     */
    public function tags(): array
    {
        $product = $this->getProduct();

        if (! $product) {
            return [];
        }

        $term_ids = $product->get_tag_ids();
        $tags = [];

        foreach ($term_ids as $term_id) {
            $term = get_term($term_id, 'product_tag');
            if ($term && ! is_wp_error($term)) {
                $tags[] = [
                    'id'   => $term->term_id,
                    'name' => $term->name,
                    'slug' => $term->slug,
                    'url'  => get_term_link($term),
                ];
            }
        }

        return $tags;
    }

    /**
     * Get the average rating.
     */
    public function averageRating(): float
    {
        $product = $this->getProduct();
        return $product ? (float) $product->get_average_rating() : 0;
    }

    /**
     * Get the review count.
     */
    public function reviewCount(): int
    {
        $product = $this->getProduct();
        return $product ? (int) $product->get_review_count() : 0;
    }

    /**
     * Get the rating count.
     */
    public function ratingCount(): int
    {
        $product = $this->getProduct();
        return $product ? (int) $product->get_rating_count() : 0;
    }

    /**
     * Check if reviews are enabled for this product.
     */
    public function reviewsEnabled(): bool
    {
        $product = $this->getProduct();
        return $product ? $product->get_reviews_allowed() : false;
    }

    /**
     * Get star rating HTML.
     */
    public function starRatingHtml(): string
    {
        $rating = $this->averageRating();

        if ($rating <= 0) {
            return '';
        }

        return wc_get_star_rating_html($rating, $this->ratingCount());
    }

    /**
     * Get rating data for custom star display.
     */
    public function ratingData(): array
    {
        $rating = $this->averageRating();

        return [
            'average'     => $rating,
            'count'       => $this->ratingCount(),
            'review_count' => $this->reviewCount(),
            'percentage'  => ($rating / 5) * 100,
            'full_stars'  => (int) floor($rating),
            'half_star'   => ($rating - floor($rating)) >= 0.5,
            'empty_stars' => 5 - (int) ceil($rating),
        ];
    }

    /**
     * Get product attributes.
     */
    public function attributes(): array
    {
        $product = $this->getProduct();

        if (! $product) {
            return [];
        }

        $attributes = [];
        $product_attributes = $product->get_attributes();

        foreach ($product_attributes as $attribute) {
            $name = $attribute->get_name();

            if ($attribute->is_taxonomy()) {
                $taxonomy = $attribute->get_taxonomy_object();
                $terms = wc_get_product_terms($product->get_id(), $name, ['fields' => 'all']);

                $values = [];
                foreach ($terms as $term) {
                    $values[] = [
                        'name' => $term->name,
                        'slug' => $term->slug,
                        'url'  => get_term_link($term),
                    ];
                }

                $attributes[] = [
                    'name'        => $taxonomy ? $taxonomy->attribute_label : $name,
                    'slug'        => $name,
                    'values'      => $values,
                    'is_taxonomy' => true,
                    'is_visible'  => $attribute->get_visible(),
                    'is_variation' => $attribute->get_variation(),
                ];
            } else {
                $values = array_map('trim', explode('|', $attribute->get_options()[0] ?? ''));

                $attributes[] = [
                    'name'        => $name,
                    'slug'        => sanitize_title($name),
                    'values'      => array_map(fn($v) => ['name' => $v, 'slug' => sanitize_title($v), 'url' => ''], $values),
                    'is_taxonomy' => false,
                    'is_visible'  => $attribute->get_visible(),
                    'is_variation' => $attribute->get_variation(),
                ];
            }
        }

        return $attributes;
    }

    /**
     * Get visible attributes (for display on product page).
     */
    public function visibleAttributes(): array
    {
        return array_filter($this->attributes(), fn($attr) => $attr['is_visible']);
    }

    /**
     * Get variation attributes (for variable products).
     */
    public function variationAttributes(): array
    {
        $product = $this->getProduct();

        if (! $product || ! $product instanceof WC_Product_Variable) {
            return [];
        }

        return $product->get_variation_attributes();
    }

    /**
     * Get available variations for variable products.
     */
    public function variations(): array
    {
        $product = $this->getProduct();

        if (! $product || ! $product instanceof WC_Product_Variable) {
            return [];
        }

        return $product->get_available_variations();
    }

    /**
     * Get variations as JSON for JavaScript.
     */
    public function variationsJson(): string
    {
        return wp_json_encode($this->variations());
    }

    /**
     * Get default attributes for variable products.
     */
    public function defaultAttributes(): array
    {
        $product = $this->getProduct();

        if (! $product || ! $product instanceof WC_Product_Variable) {
            return [];
        }

        return $product->get_default_attributes();
    }

    /**
     * Get variation form data for JavaScript.
     */
    public function variationFormData(): array
    {
        $product = $this->getProduct();

        if (! $product || ! $product instanceof WC_Product_Variable) {
            return [];
        }

        return [
            'product_id'         => $product->get_id(),
            'available_variations' => $product->get_available_variations('objects'),
            'attributes'         => $this->variationAttributes(),
            'default_attributes' => $this->defaultAttributes(),
        ];
    }

    /**
     * Get minimum and maximum prices for variable products.
     */
    public function priceRange(): array
    {
        $product = $this->getProduct();

        if (! $product || ! $product instanceof WC_Product_Variable) {
            return [
                'min' => $this->currentPrice(),
                'max' => $this->currentPrice(),
            ];
        }

        return [
            'min'          => $product->get_variation_price('min'),
            'max'          => $product->get_variation_price('max'),
            'min_regular'  => $product->get_variation_regular_price('min'),
            'max_regular'  => $product->get_variation_regular_price('max'),
        ];
    }

    /**
     * Get related products.
     */
    public function relatedProducts(int $limit = 4): array
    {
        $product = $this->getProduct();

        if (! $product) {
            return [];
        }

        $related_ids = wc_get_related_products($product->get_id(), $limit);
        $related = [];

        foreach ($related_ids as $related_id) {
            $related_product = wc_get_product($related_id);

            if (! $related_product) {
                continue;
            }

            $related[] = $this->formatProductCard($related_product);
        }

        return $related;
    }

    /**
     * Get upsell products.
     */
    public function upsellProducts(int $limit = 4): array
    {
        $product = $this->getProduct();

        if (! $product) {
            return [];
        }

        $upsell_ids = $product->get_upsell_ids();
        $upsells = [];

        foreach (array_slice($upsell_ids, 0, $limit) as $upsell_id) {
            $upsell_product = wc_get_product($upsell_id);

            if (! $upsell_product) {
                continue;
            }

            $upsells[] = $this->formatProductCard($upsell_product);
        }

        return $upsells;
    }

    /**
     * Get cross-sell products.
     */
    public function crossSellProducts(int $limit = 4): array
    {
        $product = $this->getProduct();

        if (! $product) {
            return [];
        }

        $crosssell_ids = $product->get_cross_sell_ids();
        $crosssells = [];

        foreach (array_slice($crosssell_ids, 0, $limit) as $crosssell_id) {
            $crosssell_product = wc_get_product($crosssell_id);

            if (! $crosssell_product) {
                continue;
            }

            $crosssells[] = $this->formatProductCard($crosssell_product);
        }

        return $crosssells;
    }

    /**
     * Format a product for card display.
     */
    protected function formatProductCard(WC_Product $product): array
    {
        $image_id = $product->get_image_id();

        return [
            'id'           => $product->get_id(),
            'name'         => $product->get_name(),
            'url'          => $product->get_permalink(),
            'price_html'   => $product->get_price_html(),
            'price'        => $product->get_price(),
            'regular_price' => $product->get_regular_price(),
            'on_sale'      => $product->is_on_sale(),
            'in_stock'     => $product->is_in_stock(),
            'image_url'    => $image_id ? wp_get_attachment_image_url($image_id, 'woocommerce_thumbnail') : wc_placeholder_img_src('woocommerce_thumbnail'),
            'image_alt'    => $image_id ? get_post_meta($image_id, '_wp_attachment_image_alt', true) : $product->get_name(),
            'rating'       => $product->get_average_rating(),
            'review_count' => $product->get_review_count(),
        ];
    }

    /**
     * Get product tabs data.
     */
    public function tabs(): array
    {
        $product = $this->getProduct();

        if (! $product) {
            return [];
        }

        // Get WooCommerce tabs
        $tabs = apply_filters('woocommerce_product_tabs', []);

        $result = [];
        foreach ($tabs as $key => $tab) {
            $result[$key] = [
                'title'    => $tab['title'],
                'priority' => $tab['priority'] ?? 10,
                'callback' => $tab['callback'] ?? null,
            ];
        }

        return $result;
    }

    /**
     * Check if product has description tab content.
     */
    public function hasDescription(): bool
    {
        return ! empty($this->description());
    }

    /**
     * Check if product has additional information (attributes).
     */
    public function hasAdditionalInfo(): bool
    {
        return count($this->visibleAttributes()) > 0;
    }

    /**
     * Check if product has reviews.
     */
    public function hasReviews(): bool
    {
        return $this->reviewsEnabled() && $this->reviewCount() > 0;
    }

    /**
     * Get the add to cart button text.
     */
    public function addToCartText(): string
    {
        $product = $this->getProduct();
        return $product ? $product->single_add_to_cart_text() : __('Add to cart', 'woocommerce');
    }

    /**
     * Get the add to cart URL.
     */
    public function addToCartUrl(): string
    {
        $product = $this->getProduct();
        return $product ? $product->add_to_cart_url() : '';
    }

    /**
     * Get min/max quantity for add to cart.
     */
    public function quantityInputData(): array
    {
        $product = $this->getProduct();

        if (! $product) {
            return [
                'min'   => 1,
                'max'   => '',
                'step'  => 1,
                'value' => 1,
            ];
        }

        $max_value = $product->get_max_purchase_quantity();

        return [
            'min'   => apply_filters('woocommerce_quantity_input_min', $product->get_min_purchase_quantity(), $product),
            'max'   => apply_filters('woocommerce_quantity_input_max', $max_value > 0 ? $max_value : '', $product),
            'step'  => apply_filters('woocommerce_quantity_input_step', 1, $product),
            'value' => apply_filters('woocommerce_quantity_input_value', $product->get_min_purchase_quantity(), $product),
        ];
    }

    /**
     * Get breadcrumb data for single product.
     */
    public function breadcrumbs(): array
    {
        $breadcrumbs = [];
        $product = $this->getProduct();

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
                'url'   => get_permalink($shop_page_id),
            ];
        }

        // Product categories (primary category)
        if ($product) {
            $categories = $product->get_category_ids();
            if (! empty($categories)) {
                $primary_cat_id = $categories[0];
                $ancestors = get_ancestors($primary_cat_id, 'product_cat');
                $ancestors = array_reverse($ancestors);

                foreach ($ancestors as $ancestor_id) {
                    $ancestor = get_term($ancestor_id, 'product_cat');
                    if ($ancestor && ! is_wp_error($ancestor)) {
                        $breadcrumbs[] = [
                            'label' => $ancestor->name,
                            'url'   => get_term_link($ancestor),
                        ];
                    }
                }

                $primary_cat = get_term($primary_cat_id, 'product_cat');
                if ($primary_cat && ! is_wp_error($primary_cat)) {
                    $breadcrumbs[] = [
                        'label' => $primary_cat->name,
                        'url'   => get_term_link($primary_cat),
                    ];
                }
            }
        }

        // Current product (no URL)
        $breadcrumbs[] = [
            'label' => $this->productName(),
            'url'   => '',
        ];

        return $breadcrumbs;
    }

    /**
     * Get the product weight.
     */
    public function weight(): string
    {
        $product = $this->getProduct();

        if (! $product || ! $product->has_weight()) {
            return '';
        }

        return wc_format_weight($product->get_weight());
    }

    /**
     * Get the product dimensions.
     */
    public function dimensions(): string
    {
        $product = $this->getProduct();

        if (! $product || ! $product->has_dimensions()) {
            return '';
        }

        return wc_format_dimensions($product->get_dimensions(false));
    }

    /**
     * Check if product has weight.
     */
    public function hasWeight(): bool
    {
        $product = $this->getProduct();
        return $product ? $product->has_weight() : false;
    }

    /**
     * Check if product has dimensions.
     */
    public function hasDimensions(): bool
    {
        $product = $this->getProduct();
        return $product ? $product->has_dimensions() : false;
    }

    /**
     * Get the shop page URL.
     */
    public function shopUrl(): string
    {
        return get_permalink(wc_get_page_id('shop')) ?: '';
    }

    /**
     * Check if product is featured.
     */
    public function isFeatured(): bool
    {
        $product = $this->getProduct();
        return $product ? $product->is_featured() : false;
    }

    /**
     * Check if product is virtual.
     */
    public function isVirtual(): bool
    {
        $product = $this->getProduct();
        return $product ? $product->is_virtual() : false;
    }

    /**
     * Check if product is downloadable.
     */
    public function isDownloadable(): bool
    {
        $product = $this->getProduct();
        return $product ? $product->is_downloadable() : false;
    }

    /**
     * Get the product object for advanced usage.
     */
    public function product(): ?WC_Product
    {
        return $this->getProduct();
    }

    /**
     * Get the display type for an attribute.
     *
     * @param string $attributeName The attribute name (e.g., 'pa_color' or 'color')
     * @return string 'select', 'button', or 'color'
     */
    public function getAttributeDisplayType(string $attributeName): string
    {
        // Normalize attribute name - remove 'pa_' prefix if present
        $slug = str_replace('pa_', '', $attributeName);

        $type = get_option("sega_attribute_{$slug}_type", 'select');

        // Validate the type
        if (!in_array($type, ['select', 'button', 'color'], true)) {
            return 'select';
        }

        return $type;
    }

    /**
     * Get the swatch color for a term.
     *
     * @param string $attributeName The attribute name (e.g., 'pa_color' or 'color')
     * @param string $termSlug The term slug (e.g., 'red')
     * @return string|null Hex color value or null
     */
    public function getTermSwatchColor(string $attributeName, string $termSlug): ?string
    {
        // Normalize attribute name - remove 'pa_' prefix if present
        $attrSlug = str_replace('pa_', '', $attributeName);

        $color = get_option("sega_attribute_{$attrSlug}_{$termSlug}_color");

        if (!$color) {
            return null;
        }

        return $color;
    }

    /**
     * Get variation attributes with display configuration.
     *
     * Returns enriched attribute data including display type, colors, and options.
     *
     * @return array
     */
    public function variationAttributesWithDisplay(): array
    {
        $product = $this->getProduct();

        if (!$product || !$product instanceof WC_Product_Variable) {
            return [];
        }

        $variation_attributes = $product->get_variation_attributes();
        $default_attributes = $product->get_default_attributes();
        $result = [];

        foreach ($variation_attributes as $attribute_name => $options) {
            $sanitized_name = sanitize_title($attribute_name);
            $display_type = $this->getAttributeDisplayType($attribute_name);

            // Get attribute label
            $label = wc_attribute_label($attribute_name);

            // Build options array with additional data
            $enriched_options = [];
            foreach ($options as $option) {
                $option_slug = $option;
                $option_name = $option;

                // Get term data if it's a taxonomy attribute
                if (taxonomy_exists($attribute_name)) {
                    $term = get_term_by('slug', $option, $attribute_name);
                    if ($term) {
                        $option_name = $term->name;
                        $option_slug = $term->slug;
                    }
                }

                $option_data = [
                    'slug' => $option_slug,
                    'name' => $option_name,
                    'selected' => isset($default_attributes[$sanitized_name]) &&
                                  $default_attributes[$sanitized_name] === $option_slug,
                ];

                // Add color data if display type is color
                if ($display_type === 'color') {
                    $color = $this->getTermSwatchColor($attribute_name, $option_slug);
                    $option_data['color'] = $color ?: '#808080'; // Default gray if no color set
                }

                $enriched_options[] = $option_data;
            }

            $result[$attribute_name] = [
                'name' => $attribute_name,
                'label' => $label,
                'display_type' => $display_type,
                'sanitized_name' => $sanitized_name,
                'options' => $enriched_options,
            ];
        }

        return $result;
    }

}
