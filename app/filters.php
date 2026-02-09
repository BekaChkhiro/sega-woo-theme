<?php

/**
 * Theme filters.
 */

namespace App;

/**
 * Add "… Continued" to the excerpt.
 *
 * @return string
 */
add_filter('excerpt_more', function () {
    return sprintf(' &hellip; <a href="%s">%s</a>', get_permalink(), __('Continued', 'sage'));
});

/**
 * Products Per Page
 *
 * Allow users to select how many products to display per page on shop archives.
 * Valid options: 12, 24, 48, 96
 */
add_filter('loop_shop_per_page', function ($per_page) {
    if (isset($_GET['per_page'])) {
        $requested = absint($_GET['per_page']);
        $allowed = [12, 24, 48, 96];

        if (in_array($requested, $allowed, true)) {
            return $requested;
        }
    }

    return $per_page;
}, 20);

/**
 * Image Performance Optimizations
 *
 * Add lazy loading and async decoding to images for better performance.
 */

/**
 * Add lazy loading and decoding attributes to WooCommerce product images.
 *
 * @param string $html Image HTML.
 * @param int    $attachment_id Attachment ID.
 * @param string $size Image size.
 * @param bool   $icon Whether the image should be an icon.
 * @param array  $attr Image attributes.
 * @return string Modified image HTML.
 */
add_filter('wp_get_attachment_image', function ($html, $attachment_id, $size, $icon, $attr) {
    // Skip if already has loading attribute (respect explicit settings)
    if (strpos($html, 'loading=') !== false) {
        // Still add decoding if not present
        if (strpos($html, 'decoding=') === false) {
            $html = str_replace('<img ', '<img decoding="async" ', $html);
        }
        return $html;
    }

    // Add lazy loading and async decoding
    $html = str_replace('<img ', '<img loading="lazy" decoding="async" ', $html);

    return $html;
}, 10, 5);

/**
 * Add fetchpriority="high" to above-the-fold images (LCP optimization).
 *
 * This filter adds fetchpriority="high" to the main product image on single product pages
 * to improve Largest Contentful Paint (LCP) performance.
 *
 * @param array $attr Image attributes.
 * @param \WP_Post $attachment Attachment post object.
 * @param string $size Image size.
 * @return array Modified attributes.
 */
add_filter('wp_get_attachment_image_attributes', function ($attr, $attachment, $size) {
    // On single product page, prioritize the main product image
    if (function_exists('is_product') && is_product()) {
        global $product;

        if ($product && $attachment->ID === $product->get_image_id()) {
            // Main product image should NOT be lazy loaded - it's above the fold
            $attr['loading'] = 'eager';
            $attr['fetchpriority'] = 'high';
            $attr['decoding'] = 'async';
        }
    }

    return $attr;
}, 10, 3);

/**
 * Load custom Blade templates for WooCommerce pages (cart, checkout, my account).
 *
 * These pages use shortcodes on regular WordPress pages, so we need to intercept
 * the template loading and render our Blade templates instead.
 */
add_filter('template_include', function ($template) {
    if (! function_exists('view')) {
        return $template;
    }

    // Cart page
    if (function_exists('is_cart') && is_cart()) {
        if (view()->exists('woocommerce.cart.cart')) {
            echo view('woocommerce.cart.cart')->render();
            return get_theme_file_path('resources/views/empty.blade.php');
        }
    }

    // Checkout page
    if (function_exists('is_checkout') && is_checkout() && ! is_order_received_page()) {
        if (view()->exists('woocommerce.checkout.form-checkout')) {
            echo view('woocommerce.checkout.form-checkout')->render();
            return get_theme_file_path('resources/views/empty.blade.php');
        }
    }

    // Order Received / Thank You page
    if (function_exists('is_order_received_page') && is_order_received_page()) {
        if (view()->exists('woocommerce.checkout.thankyou')) {
            global $wp;
            $order_id = isset($wp->query_vars['order-received']) ? absint($wp->query_vars['order-received']) : 0;
            $order = $order_id ? wc_get_order($order_id) : false;

            echo view('woocommerce.checkout.thankyou', ['order' => $order])->render();
            return get_theme_file_path('resources/views/empty.blade.php');
        }
    }

    // My Account page (for future implementation)
    if (function_exists('is_account_page') && is_account_page()) {
        if (view()->exists('woocommerce.myaccount.my-account')) {
            echo view('woocommerce.myaccount.my-account')->render();
            return get_theme_file_path('resources/views/empty.blade.php');
        }
    }

    return $template;
}, 99);

/**
 * WooCommerce Template Overrides
 *
 * The main WooCommerce template routing is handled by woocommerce.php in the theme root.
 * These filters handle sub-templates and components.
 */

/**
 * Remove default WooCommerce hooks that we're replacing with custom Blade templates.
 */
add_action('init', function () {
    // Remove default result count and ordering (we have custom ones in archive-product.blade.php)
    remove_action('woocommerce_before_shop_loop', 'woocommerce_result_count', 20);
    remove_action('woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30);

    // Remove default "no products found" message (we have custom one)
    remove_action('woocommerce_no_products_found', 'wc_no_products_found');
});

/**
 * Override WooCommerce template parts with Blade views when available.
 *
 * @param string $template The path to the template file.
 * @param string $slug     The template slug.
 * @param string $name     The template name.
 * @return string
 */
add_filter('wc_get_template_part', function ($template, $slug, $name) {
    if (! function_exists('view')) {
        return $template;
    }

    // Build the Blade view name
    $view_name = 'woocommerce.' . str_replace('/', '.', $slug);
    if ($name) {
        $view_name .= '-' . $name;
    }

    // Check if a Blade view exists for this template part
    if (view()->exists($view_name)) {
        echo view($view_name)->render();
        return '';
    }

    return $template;
}, 10, 3);

/**
 * Override WooCommerce sub-templates with Blade views when available.
 *
 * @param string $template      The path to the template file.
 * @param string $template_name The name of the template.
 * @param array  $args          Template arguments.
 * @param string $template_path The template path.
 * @param string $default_path  The default path.
 * @return string
 */
add_filter('wc_get_template', function ($template, $template_name, $args, $template_path, $default_path) {
    if (! function_exists('view')) {
        return $template;
    }

    // Skip main templates handled by woocommerce.php
    $main_templates = [
        'archive-product.php',
        'single-product.php',
        'cart/cart.php',
        'checkout/form-checkout.php',
        'myaccount/my-account.php',
    ];

    if (in_array($template_name, $main_templates)) {
        return $template;
    }

    // Convert template name to Blade view format
    $blade_template = str_replace(['.php', '/'], ['', '.'], $template_name);
    $view_name = 'woocommerce.' . $blade_template;

    // Check if a Blade view exists for this template
    if (view()->exists($view_name)) {
        echo view($view_name, $args ?: [])->render();
        return get_theme_file_path('resources/views/empty.blade.php');
    }

    return $template;
}, 10, 5);

/**
 * Shop Sidebar Filters - Handle custom filter query parameters
 *
 * Filter products by "on_sale", "in_stock", and "product_cat" URL parameters.
 */
add_action('woocommerce_product_query', function ($query) {
    // On Sale filter
    if (isset($_GET['on_sale']) && $_GET['on_sale'] === '1') {
        $query->set('post__in', array_merge([0], wc_get_product_ids_on_sale()));
    }

    // In Stock filter
    if (isset($_GET['in_stock']) && $_GET['in_stock'] === '1') {
        $meta_query = $query->get('meta_query') ?: [];
        $meta_query[] = [
            'key'     => '_stock_status',
            'value'   => 'instock',
            'compare' => '=',
        ];
        $query->set('meta_query', $meta_query);
    }

    // Category filter via query parameter (for shop page filtering)
    // This allows filtering by category on the main shop page without navigation
    // Categories are now passed as IDs for cleaner URLs
    if (is_shop() && isset($_GET['cat_ids']) && ! empty($_GET['cat_ids'])) {
        $category_ids = array_filter(
            array_map('absint', explode(',', wc_clean(wp_unslash($_GET['cat_ids'])))),
            fn($id) => $id > 0
        );

        if (! empty($category_ids)) {
            $tax_query = $query->get('tax_query') ?: [];
            $tax_query[] = [
                'taxonomy' => 'product_cat',
                'field'    => 'term_id',
                'terms'    => $category_ids,
                'operator' => 'IN',
            ];
            $query->set('tax_query', $tax_query);
        }
    }
});

/**
 * WooCommerce Cart Fragments Optimization
 *
 * Optimized cart fragments with:
 * - Reduced payload size (only send what's needed)
 * - Session-based caching to avoid regenerating HTML
 * - Minimal DOM updates for better performance
 */

/**
 * Get cached mini-cart HTML from session or generate fresh.
 *
 * @param string $type The fragment type (items, footer).
 * @return string|null Cached HTML or null if not cached.
 */
function get_cached_mini_cart_fragment($type)
{
    if (! WC()->session) {
        return null;
    }

    $cart_hash = WC()->cart->get_cart_hash();
    $cache_key = 'mini_cart_' . $type . '_' . $cart_hash;

    return WC()->session->get($cache_key);
}

/**
 * Cache mini-cart HTML in session.
 *
 * @param string $type The fragment type.
 * @param string $html The HTML to cache.
 */
function set_cached_mini_cart_fragment($type, $html)
{
    if (! WC()->session) {
        return;
    }

    $cart_hash = WC()->cart->get_cart_hash();
    $cache_key = 'mini_cart_' . $type . '_' . $cart_hash;

    WC()->session->set($cache_key, $html);
}

/**
 * Clear mini-cart cache when cart changes.
 *
 * Session data is keyed by cart hash, so old cached fragments
 * become unreachable when the cart changes. This function explicitly
 * clears the previous cache to prevent session bloat.
 */
add_action('woocommerce_cart_updated', function () {
    if (! WC()->session) {
        return;
    }

    // Get previous cart hash from session
    $prev_hash = WC()->session->get('prev_cart_hash');
    $curr_hash = WC()->cart->get_cart_hash();

    // If hash changed, clear old cached fragments
    if ($prev_hash && $prev_hash !== $curr_hash) {
        WC()->session->set('mini_cart_items_' . $prev_hash, null);
        WC()->session->set('mini_cart_footer_' . $prev_hash, null);
    }

    // Store current hash for next comparison
    WC()->session->set('prev_cart_hash', $curr_hash);
}, 10);

/**
 * Add cart hash header to AJAX responses for client-side caching decisions.
 *
 * This allows JavaScript to detect if the cart has changed without
 * having to parse the full response.
 */
add_action('woocommerce_add_to_cart_fragments', function ($fragments) {
    if (function_exists('WC') && WC()->cart) {
        // Add cart hash as a data attribute for JavaScript access
        $fragments['cart_hash'] = WC()->cart->get_cart_hash();
    }
    return $fragments;
}, 999);

/**
 * Generate mini-cart items HTML (optimized version).
 * Updated to match partials/mini-cart.blade.php design.
 *
 * @return string
 */
function generate_mini_cart_items_html()
{
    $cart = WC()->cart;

    if ($cart->is_empty()) {
        return '<div class="mini-cart-items max-h-80 overflow-y-auto">
            <div class="flex flex-col items-center justify-center px-6 py-10 text-center">
                <div class="mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-gradient-to-br from-secondary-100 to-secondary-50">
                    <svg class="h-10 w-10 text-secondary-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" />
                    </svg>
                </div>
                <p class="mb-1 text-sm font-medium text-secondary-900">' . esc_html__('Your cart is empty', 'sage') . '</p>
                <p class="mb-4 text-xs text-secondary-500">' . esc_html__('Add items to get started', 'sage') . '</p>
                <a href="' . esc_url(wc_get_page_permalink('shop')) . '" class="inline-flex items-center gap-1.5 rounded-full bg-primary-600 px-5 py-2.5 text-sm font-semibold text-white shadow-md shadow-primary-600/20 transition-all hover:bg-primary-700 hover:shadow-lg hover:shadow-primary-600/30">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                    </svg>
                    ' . esc_html__('Start Shopping', 'sage') . '
                </a>
            </div>
        </div>';
    }

    $items_html = '<div class="mini-cart-items max-h-80 overflow-y-auto"><ul class="divide-y divide-secondary-100">';

    foreach ($cart->get_cart() as $cart_item_key => $cart_item) {
        $product = $cart_item['data'];
        if (! $product || ! $product->exists()) {
            continue;
        }

        // Get variation data
        $item_data = wc_get_formatted_cart_item_data($cart_item);
        $variation_html = '';
        if ($item_data) {
            $variation_html = '<div class="mt-1 text-xs text-secondary-500 [&_dl]:flex [&_dl]:flex-wrap [&_dl]:gap-x-2 [&_dd]:font-medium [&_dd]:text-secondary-600 [&_dt]:after:content-[\':\ \']">' . $item_data . '</div>';
        }

        $items_html .= sprintf(
            '<li class="mini-cart-item group p-4" data-key="%s">
                <div class="flex gap-4">
                    <a href="%s" class="flex-shrink-0">
                        <div class="h-20 w-20 overflow-hidden rounded-xl bg-secondary-100 [&_img]:h-full [&_img]:w-full [&_img]:object-cover">%s</div>
                    </a>
                    <div class="flex flex-1 flex-col justify-center">
                        <div class="flex items-start justify-between gap-2">
                            <a href="%s" class="text-sm font-medium text-secondary-900 transition-colors hover:text-primary-600 line-clamp-2">%s</a>
                            <button type="button" class="remove-from-cart flex-shrink-0 rounded-full p-1 text-secondary-400 transition-colors hover:bg-red-50 hover:text-red-500" data-cart-item-key="%s" aria-label="%s">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        %s
                        <div class="mt-2 flex items-center justify-between">
                            <span class="inline-flex items-center gap-0.5 rounded-full bg-secondary-100 px-2.5 py-1 text-xs font-medium text-secondary-600">
                                <span class="text-secondary-400">×</span>
                                %s
                            </span>
                            <span class="text-base font-bold text-secondary-900">%s</span>
                        </div>
                    </div>
                </div>
            </li>',
            esc_attr($cart_item_key),
            esc_url($product->get_permalink()),
            $product->get_image('woocommerce_gallery_thumbnail', ['loading' => 'lazy', 'decoding' => 'async']),
            esc_url($product->get_permalink()),
            esc_html($product->get_name()),
            esc_attr($cart_item_key),
            esc_attr__('Remove item', 'sage'),
            $variation_html,
            esc_html($cart_item['quantity']),
            $cart->get_product_subtotal($product, $cart_item['quantity'])
        );
    }

    $items_html .= '</ul></div>';

    return $items_html;
}

/**
 * Generate mini-cart footer HTML (optimized version).
 * Updated to match partials/mini-cart.blade.php design.
 *
 * @param string $subtotal The cart subtotal.
 * @return string
 */
function generate_mini_cart_footer_html($subtotal)
{
    $is_empty = WC()->cart->is_empty();
    $cart_url = wc_get_cart_url();
    $checkout_url = wc_get_checkout_url();

    return sprintf(
        '<div class="mini-cart-footer%s">
            <div class="border-t border-secondary-200 bg-white p-5">
                <div class="mb-4 flex items-center justify-between">
                    <span class="text-sm text-secondary-600">%s</span>
                    <span class="mini-cart-subtotal text-base font-bold text-secondary-900">%s</span>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <a href="%s" class="inline-flex items-center justify-center gap-1.5 rounded-xl border border-secondary-200 bg-white px-4 py-3 text-sm font-semibold text-secondary-700 shadow-sm transition-all hover:bg-secondary-50 hover:shadow focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" />
                        </svg>
                        %s
                    </a>
                    <a href="%s" class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-primary-600 px-4 py-3 text-sm font-semibold text-white shadow-md shadow-primary-600/20 transition-all hover:bg-primary-700 hover:shadow-lg hover:shadow-primary-600/30 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                        %s
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>',
        $is_empty ? ' hidden' : '',
        esc_html__('Subtotal', 'sage'),
        $subtotal,
        esc_url($cart_url),
        esc_html__('View Cart', 'sage'),
        esc_url($checkout_url),
        esc_html__('Checkout', 'sage')
    );
}

/**
 * Optimized cart fragments filter.
 */
add_filter('woocommerce_add_to_cart_fragments', function ($fragments) {
    if (! function_exists('WC') || ! WC()->cart) {
        return $fragments;
    }

    $item_count = WC()->cart->get_cart_contents_count();
    $subtotal = WC()->cart->get_cart_subtotal();

    // Fragment 1: Cart count badge (always fresh - tiny payload)
    $fragments['.mini-cart-count'] = sprintf(
        '<span class="mini-cart-count absolute -right-1 -top-1 flex h-5 w-5 items-center justify-center rounded-full bg-primary-600 text-xs font-medium text-white transition-transform %s">%s</span>',
        $item_count === 0 ? 'scale-0' : 'scale-100',
        $item_count > 99 ? '99+' : $item_count
    );

    // Fragment 2: Cart count text in header (always fresh - tiny payload)
    $fragments['.mini-cart-count-text'] = sprintf(
        '<span class="mini-cart-count-text">%s</span>',
        $item_count
    );

    // Fragment 3: Subtotal (always fresh - tiny payload)
    $fragments['.mini-cart-subtotal'] = sprintf(
        '<span class="mini-cart-subtotal text-base font-bold text-secondary-900">%s</span>',
        $subtotal
    );

    // Fragment 4: Mini-cart items (use cache if available)
    $cached_items = get_cached_mini_cart_fragment('items');
    if ($cached_items) {
        $fragments['.mini-cart-items'] = $cached_items;
    } else {
        $items_html = generate_mini_cart_items_html();
        set_cached_mini_cart_fragment('items', $items_html);
        $fragments['.mini-cart-items'] = $items_html;
    }

    // Fragment 5: Mini-cart footer (use cache if available)
    $cached_footer = get_cached_mini_cart_fragment('footer');
    if ($cached_footer) {
        $fragments['.mini-cart-footer'] = $cached_footer;
    } else {
        $footer_html = generate_mini_cart_footer_html($subtotal);
        set_cached_mini_cart_fragment('footer', $footer_html);
        $fragments['.mini-cart-footer'] = $footer_html;
    }

    // Cart page totals (only on cart page)
    if (is_cart()) {
        $fragments['.cart-subtotal'] = sprintf(
            '<span class="cart-subtotal text-sm font-medium text-secondary-900 transition-all duration-300">%s</span>',
            WC()->cart->get_cart_subtotal()
        );

        $fragments['.cart-total'] = sprintf(
            '<span class="cart-total text-xl font-bold text-secondary-900 transition-all duration-300">%s</span>',
            WC()->cart->get_total()
        );
    }

    return $fragments;
});

/**
 * AJAX Remove item from cart.
 *
 * Handles the custom remove_from_cart AJAX action.
 */
add_action('wp_ajax_remove_from_cart', __NAMESPACE__ . '\\ajax_remove_from_cart');
add_action('wp_ajax_nopriv_remove_from_cart', __NAMESPACE__ . '\\ajax_remove_from_cart');

function ajax_remove_from_cart()
{
    $cart_item_key = isset($_POST['cart_item_key']) ? sanitize_text_field(wp_unslash($_POST['cart_item_key'])) : '';

    if (! $cart_item_key) {
        wp_send_json_error(['message' => __('Invalid cart item.', 'sage')]);
    }

    $removed = WC()->cart->remove_cart_item($cart_item_key);

    if ($removed) {
        // Get updated cart data
        $cart_count = WC()->cart->get_cart_contents_count();
        $cart_subtotal = WC()->cart->get_cart_subtotal();
        $cart_total = WC()->cart->get_total();

        // Get cart fragments
        WC_AJAX::get_refreshed_fragments();
    } else {
        wp_send_json_error(['message' => __('Could not remove item from cart.', 'sage')]);
    }
}

/**
 * AJAX Update cart item quantity.
 *
 * Handles the custom update_cart_item_qty AJAX action for AJAX quantity updates.
 */
add_action('wp_ajax_update_cart_item_qty', __NAMESPACE__ . '\\ajax_update_cart_item_qty');
add_action('wp_ajax_nopriv_update_cart_item_qty', __NAMESPACE__ . '\\ajax_update_cart_item_qty');

function ajax_update_cart_item_qty()
{
    $cart_item_key = isset($_POST['cart_item_key']) ? sanitize_text_field(wp_unslash($_POST['cart_item_key'])) : '';
    $quantity = isset($_POST['quantity']) ? absint($_POST['quantity']) : 1;

    if (! $cart_item_key) {
        wp_send_json_error(['message' => __('Invalid cart item.', 'sage')]);
    }

    // Get cart item
    $cart = WC()->cart;
    $cart_item = $cart->get_cart_item($cart_item_key);

    if (! $cart_item) {
        wp_send_json_error(['message' => __('Cart item not found.', 'sage')]);
    }

    // If quantity is 0, remove the item
    if ($quantity === 0) {
        $removed = $cart->remove_cart_item($cart_item_key);

        if (! $removed) {
            wp_send_json_error(['message' => __('Could not remove item from cart.', 'sage')]);
        }
    } else {
        // Update quantity
        $product = $cart_item['data'];

        // Validate against stock if product manages stock
        if ($product->managing_stock()) {
            $stock_quantity = $product->get_stock_quantity();

            if ($stock_quantity !== null && $quantity > $stock_quantity) {
                wp_send_json_error([
                    'message' => sprintf(
                        __('Sorry, only %d items are in stock.', 'sage'),
                        $stock_quantity
                    ),
                    'max_quantity' => $stock_quantity,
                ]);
            }
        }

        // Validate against max purchase quantity
        $max_purchase = $product->get_max_purchase_quantity();
        if ($max_purchase > 0 && $quantity > $max_purchase) {
            wp_send_json_error([
                'message' => sprintf(
                    __('Sorry, you can only purchase %d of this item.', 'sage'),
                    $max_purchase
                ),
                'max_quantity' => $max_purchase,
            ]);
        }

        // Update the cart item quantity
        $updated = $cart->set_quantity($cart_item_key, $quantity, true);

        if (! $updated) {
            wp_send_json_error(['message' => __('Could not update cart.', 'sage')]);
        }
    }

    // Recalculate cart totals
    $cart->calculate_totals();

    // Get the updated cart item (if not removed)
    $updated_cart_item = $quantity > 0 ? $cart->get_cart_item($cart_item_key) : null;
    $item_subtotal = '';

    if ($updated_cart_item) {
        $product = $updated_cart_item['data'];
        $item_subtotal = $cart->get_product_subtotal($product, $updated_cart_item['quantity']);
    }

    // Build response data
    $response_data = [
        'cart_item_key' => $cart_item_key,
        'quantity' => $quantity,
        'item_subtotal' => $item_subtotal,
        'cart_subtotal' => $cart->get_cart_subtotal(),
        'cart_total' => $cart->get_total(),
        'cart_count' => $cart->get_cart_contents_count(),
        'is_empty' => $cart->is_empty(),
        'item_removed' => $quantity === 0,
    ];

    // Get fragments for mini-cart update
    ob_start();
    wc_get_template('cart/mini-cart.php');
    $mini_cart_html = ob_get_clean();

    $response_data['fragments'] = apply_filters('woocommerce_add_to_cart_fragments', [
        'div.widget_shopping_cart_content' => '<div class="widget_shopping_cart_content">' . $mini_cart_html . '</div>',
    ]);

    // Add custom fragments for our theme
    $response_data['fragments']['.mini-cart-count'] = sprintf(
        '<span class="mini-cart-count absolute -right-1 -top-1 flex h-5 w-5 items-center justify-center rounded-full bg-primary-600 text-xs font-medium text-white transition-transform %s">%s</span>',
        $response_data['cart_count'] === 0 ? 'scale-0' : 'scale-100',
        $response_data['cart_count'] > 99 ? '99+' : $response_data['cart_count']
    );

    $response_data['fragments']['.mini-cart-subtotal'] = sprintf(
        '<span class="mini-cart-subtotal text-base font-bold text-secondary-900">%s</span>',
        $cart->get_cart_subtotal()
    );

    $response_data['fragments']['.cart-subtotal'] = sprintf(
        '<span class="cart-subtotal text-sm font-medium text-secondary-900 transition-all duration-300">%s</span>',
        $cart->get_cart_subtotal()
    );

    $response_data['fragments']['.cart-total'] = sprintf(
        '<span class="cart-total text-xl font-bold text-secondary-900 transition-all duration-300">%s</span>',
        $cart->get_total()
    );

    // Add item-specific subtotal fragment
    if ($updated_cart_item) {
        $response_data['fragments'][".cart-item[data-cart-item-key=\"{$cart_item_key}\"] .cart-item-subtotal"] = sprintf(
            '<span class="cart-item-subtotal text-base font-semibold text-secondary-900 transition-all duration-300">%s</span>',
            $item_subtotal
        );
    }

    wp_send_json_success($response_data);
}

/**
 * Register custom WC AJAX endpoint for cart quantity update.
 *
 * This allows using the wc-ajax URL pattern: /?wc-ajax=update_cart_item
 */
add_action('wc_ajax_update_cart_item', __NAMESPACE__ . '\\wc_ajax_update_cart_item');
add_action('wc_ajax_nopriv_update_cart_item', __NAMESPACE__ . '\\wc_ajax_update_cart_item');

function wc_ajax_update_cart_item()
{
    // Reuse the same handler
    ajax_update_cart_item_qty();
}

/**
 * AJAX Add variable product to cart.
 *
 * Custom handler for adding variable products via AJAX.
 * WooCommerce's built-in add_to_cart AJAX only supports simple products.
 * We remove WooCommerce's default handler and replace with our own.
 */
add_action('init', function () {
    // Remove WooCommerce's default AJAX add to cart handler
    remove_action('wc_ajax_add_to_cart', ['WC_AJAX', 'add_to_cart']);
});

add_action('wc_ajax_add_to_cart', __NAMESPACE__ . '\\wc_ajax_add_to_cart');
add_action('wc_ajax_nopriv_add_to_cart', __NAMESPACE__ . '\\wc_ajax_add_to_cart');

function wc_ajax_add_to_cart()
{
    $product_id = apply_filters('woocommerce_add_to_cart_product_id', absint($_POST['product_id'] ?? 0));
    $variation_id = absint($_POST['variation_id'] ?? 0);
    $quantity = empty($_POST['quantity']) ? 1 : wc_stock_amount(wp_unslash($_POST['quantity']));

    // Get product
    $product = wc_get_product($product_id);

    if (! $product) {
        wp_send_json([
            'error' => __('Product not found.', 'sage'),
        ]);
        return;
    }

    // Handle variable products
    if ($product->is_type('variable') && $variation_id) {
        // Get variation attributes from POST
        $variation = [];
        foreach ($_POST as $key => $value) {
            if (strpos($key, 'attribute_') === 0) {
                $variation[$key] = wc_clean(wp_unslash($value));
            }
        }

        // Validate variation
        $variation_product = wc_get_product($variation_id);
        if (! $variation_product || ! $variation_product->is_purchasable() || ! $variation_product->is_in_stock()) {
            wp_send_json([
                'error' => __('This variation is unavailable.', 'sage'),
            ]);
            return;
        }

        // Add to cart
        $cart_item_key = WC()->cart->add_to_cart($product_id, $quantity, $variation_id, $variation);
    } else {
        // Simple product
        $cart_item_key = WC()->cart->add_to_cart($product_id, $quantity);
    }

    if (! $cart_item_key) {
        // Get WooCommerce notices as error message
        $notices = wc_get_notices('error');
        $error_message = ! empty($notices) ? wp_strip_all_tags($notices[0]['notice']) : __('Could not add to cart.', 'sage');
        wc_clear_notices();

        wp_send_json([
            'error' => $error_message,
        ]);
        return;
    }

    // Clear any notices
    wc_clear_notices();

    // Get cart fragments
    ob_start();
    wc_get_template('cart/mini-cart.php');
    $mini_cart = ob_get_clean();

    // Build fragments array
    $fragments = apply_filters('woocommerce_add_to_cart_fragments', [
        'div.widget_shopping_cart_content' => '<div class="widget_shopping_cart_content">' . $mini_cart . '</div>',
    ]);

    // Send success response
    wp_send_json([
        'fragments' => $fragments,
        'cart_hash' => WC()->cart->get_cart_hash(),
        'cart_quantity' => WC()->cart->get_cart_contents_count(),
    ]);
}

/**
 * Add cart page scripts.
 *
 * Adds our custom cart params for AJAX operations.
 * Note: wc-cart and wc-cart-fragments are now loaded via setup.php conditional loading.
 */
add_action('wp_enqueue_scripts', function () {
    if (is_cart()) {
        // Always add our custom cart params - WooCommerce may not add them for custom templates
        wp_add_inline_script('wc-cart', sprintf(
            'window.sega_cart_params = %s;',
            wp_json_encode([
                'ajax_url' => admin_url('admin-ajax.php'),
                'wc_ajax_url' => \WC_AJAX::get_endpoint('%%endpoint%%'),
                'update_cart_nonce' => wp_create_nonce('woocommerce-cart'),
            ])
        ), 'before');
    }
}, 25); // Run after conditional loading in setup.php (priority 20)

/**
 * Transient Cache Invalidation
 *
 * Clear shop-related transient caches when relevant data changes.
 * This ensures users always see fresh data after product/category updates.
 */

use App\View\Composers\Shop;
use App\View\Composers\Homepage;

/**
 * Clear all shop caches when a product is created, updated, or deleted.
 */
add_action('woocommerce_new_product', function () {
    Shop::clearCache();
    Homepage::clearCache();
});

add_action('woocommerce_update_product', function () {
    Shop::clearCache();
    Homepage::clearCache();
});

add_action('woocommerce_delete_product', function () {
    Shop::clearCache();
    Homepage::clearCache();
});

add_action('woocommerce_trash_product', function () {
    Shop::clearCache();
    Homepage::clearCache();
});

/**
 * Clear category cache when product categories change.
 */
add_action('created_product_cat', function () {
    Shop::clearCacheByType('categories');
    Homepage::clearCacheByType('featured_categories');
    Homepage::clearCacheByType('mega_menu_cats_0');
});

add_action('edited_product_cat', function () {
    Shop::clearCacheByType('categories');
    Homepage::clearCacheByType('featured_categories');
    Homepage::clearCacheByType('mega_menu_cats_0');
});

add_action('delete_product_cat', function () {
    Shop::clearCacheByType('categories');
    Homepage::clearCacheByType('featured_categories');
    Homepage::clearCacheByType('mega_menu_cats_0');
});

/**
 * Clear price range cache when product prices are updated.
 * This covers bulk price updates and individual product saves.
 */
add_action('woocommerce_product_set_price', function () {
    Shop::clearCacheByType('price_range');
});

add_action('woocommerce_variation_set_price', function () {
    Shop::clearCacheByType('price_range');
});

/**
 * Clear total products cache when product status changes.
 */
add_action('transition_post_status', function ($new_status, $old_status, $post) {
    if ($post->post_type !== 'product') {
        return;
    }

    // Only clear if transitioning to/from 'publish' status
    if ($new_status === 'publish' || $old_status === 'publish') {
        Shop::clearCacheByType('total_products');
    }
}, 10, 3);

/**
 * Clear all caches when WooCommerce tools clear transients.
 */
add_action('woocommerce_system_status_tool_executed', function ($tool) {
    if ($tool['id'] === 'clear_transients') {
        Shop::clearCache();
        Homepage::clearCache();
    }
});

/**
 * Clear all caches on product import completion.
 */
add_action('woocommerce_product_import_inserted_product_object', function () {
    Shop::clearCache();
    Homepage::clearCache();
});

add_action('woocommerce_product_import_finished', function () {
    Shop::clearCache();
    Homepage::clearCache();
});

/**
 * ============================================================================
 * WooCommerce Hook Customizations
 * ============================================================================
 *
 * Custom hooks to modify default WooCommerce behavior, layouts, and output.
 * These modifications are designed to work with our Blade templates and
 * Tailwind CSS styling.
 */

/**
 * Breadcrumb Customization
 *
 * Modify the default WooCommerce breadcrumb appearance.
 */
add_filter('woocommerce_breadcrumb_defaults', function ($defaults) {
    return [
        'delimiter'   => '<span class="mx-2 text-secondary-400">/</span>',
        'wrap_before' => '<nav class="woocommerce-breadcrumb text-sm text-secondary-600 mb-6" aria-label="' . esc_attr__('Breadcrumb', 'sage') . '">',
        'wrap_after'  => '</nav>',
        'before'      => '<span class="breadcrumb-item">',
        'after'       => '</span>',
        'home'        => __('Shop', 'sage'),
    ];
});

/**
 * Single Product Page Hook Modifications
 *
 * Reorganize the single product page layout by removing/repositioning hooks.
 */
add_action('init', function () {
    // Remove default product meta (SKU, categories, tags) - we handle this in our template
    remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40);

    // Remove default sharing (if any plugin adds it)
    remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 50);

    // Remove default breadcrumbs from before main content - we place them in our template
    remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20);

    // Remove default sidebar from shop pages - we have our own sidebar implementation
    remove_action('woocommerce_sidebar', 'woocommerce_get_sidebar', 10);
}, 15);

/**
 * Product Tabs Customization
 *
 * Keep only the Description tab, remove Reviews and Additional Information.
 */
add_filter('woocommerce_product_tabs', function ($tabs) {
    // Remove reviews tab
    unset($tabs['reviews']);

    // Remove additional information tab
    unset($tabs['additional_information']);

    // Remove description tab if product has no description
    global $product;
    if ($product && empty($product->get_description())) {
        unset($tabs['description']);
    }

    return $tabs;
}, 98);

/**
 * Related Products Customization
 *
 * Control the number and columns of related products displayed.
 */
add_filter('woocommerce_output_related_products_args', function ($args) {
    $args['posts_per_page'] = 4; // Number of related products
    $args['columns'] = 4;        // Number of columns

    return $args;
});

/**
 * Upsells Customization
 *
 * Control the number of upsell products displayed.
 */
add_filter('woocommerce_upsell_display_args', function ($args) {
    $args['posts_per_page'] = 4;
    $args['columns'] = 4;

    return $args;
});

/**
 * Cross-sells Customization (Cart Page)
 *
 * Control the number of cross-sell products displayed on the cart page.
 */
add_filter('woocommerce_cross_sells_total', function () {
    return 4;
});

add_filter('woocommerce_cross_sells_columns', function () {
    return 4;
});

/**
 * Add to Cart Button Text Customization
 *
 * Customize the "Add to Cart" button text based on product type.
 */
add_filter('woocommerce_product_add_to_cart_text', function ($text, $product) {
    if ($product->is_type('variable')) {
        return __('Select Options', 'sage');
    }

    if ($product->is_type('grouped')) {
        return __('View Products', 'sage');
    }

    if ($product->is_type('external')) {
        return $product->get_button_text() ?: __('Buy Product', 'sage');
    }

    if (! $product->is_in_stock()) {
        return __('Out of Stock', 'sage');
    }

    return __('Add to Cart', 'sage');
}, 10, 2);

/**
 * Single Product Add to Cart Button Text
 */
add_filter('woocommerce_product_single_add_to_cart_text', function ($text, $product) {
    if (! $product->is_in_stock()) {
        return __('Out of Stock', 'sage');
    }

    return __('Add to Cart', 'sage');
}, 10, 2);

/**
 * Empty Cart Message Customization
 */
add_filter('wc_empty_cart_message', function () {
    return __('Your cart is currently empty. Start shopping to add items to your cart.', 'sage');
});

/**
 * ============================================================================
 * Checkout Field Customization (T6.10)
 * ============================================================================
 *
 * Comprehensive checkout field customization including:
 * - Field reordering for better UX (contact info first)
 * - Custom placeholders and labels
 * - Tailwind CSS input classes
 * - Field priority adjustments
 * - Conditional field display
 * - Improved validation
 */

/**
 * Get Tailwind CSS classes for checkout form inputs.
 *
 * @param string $type The input type (text, select, textarea, checkbox).
 * @return array Array of CSS classes.
 */
function get_checkout_input_classes($type = 'text')
{
    $base_classes = [
        'w-full',
        'rounded-lg',
        'border',
        'border-secondary-300',
        'bg-white',
        'text-sm',
        'text-secondary-900',
        'placeholder-secondary-400',
        'shadow-sm',
        'transition-colors',
        'focus:border-primary-500',
        'focus:outline-none',
        'focus:ring-2',
        'focus:ring-primary-500',
    ];

    switch ($type) {
        case 'select':
            return array_merge($base_classes, ['px-4', 'py-2.5', 'appearance-none']);
        case 'textarea':
            return array_merge($base_classes, ['px-4', 'py-2.5', 'min-h-[100px]', 'resize-y']);
        case 'checkbox':
            return ['h-5', 'w-5', 'rounded', 'border-secondary-300', 'text-primary-600', 'focus:ring-primary-500'];
        default:
            return array_merge($base_classes, ['px-4', 'py-2.5']);
    }
}

/**
 * Get checkout field Customizer setting.
 *
 * Helper function to retrieve Customizer settings for checkout fields.
 *
 * @param string $field_key Full field key (e.g., 'billing_email').
 * @param string $setting   Setting name (enabled, label, placeholder, required).
 * @param mixed  $default   Default value if setting not found.
 * @return mixed
 */
function get_checkout_field_customizer_setting($field_key, $setting, $default = null)
{
    $setting_key = 'checkout_field_' . $field_key . '_' . $setting;
    return get_theme_mod($setting_key, $default);
}

/**
 * Check if a checkout field is enabled via Customizer.
 *
 * @param string $field_key Full field key (e.g., 'billing_email').
 * @return bool
 */
function is_checkout_field_enabled($field_key)
{
    $setting_key = 'checkout_field_' . $field_key . '_enabled';

    // Get all theme mods to check if setting was explicitly set
    $theme_mods = get_theme_mods();

    // If setting was never saved, default to enabled
    if (!isset($theme_mods[$setting_key])) {
        return true;
    }

    // Return the actual saved value (could be '1', '0', true, false, '', etc.)
    $value = $theme_mods[$setting_key];

    // Handle various falsy values that indicate "disabled"
    if ($value === '' || $value === '0' || $value === 0 || $value === false) {
        return false;
    }

    return true;
}

/**
 * Check if a checkout field is required via Customizer.
 *
 * @param string $field_key Full field key (e.g., 'billing_email').
 * @param bool   $default   Default required status.
 * @return bool
 */
function is_checkout_field_required($field_key, $default = true)
{
    $setting_key = 'checkout_field_' . $field_key . '_required';

    // Get all theme mods to check if setting was explicitly set
    $theme_mods = get_theme_mods();

    // If setting was never saved, use default
    if (!isset($theme_mods[$setting_key])) {
        return $default;
    }

    // Return the actual saved value
    $value = $theme_mods[$setting_key];

    // Handle various falsy values that indicate "not required"
    if ($value === '' || $value === '0' || $value === 0 || $value === false) {
        return false;
    }

    return true;
}

/**
 * Get checkout field label from Customizer.
 *
 * @param string $field_key Full field key (e.g., 'billing_email').
 * @param string $default   Default label.
 * @return string
 */
function get_checkout_field_label($field_key, $default)
{
    $label = get_checkout_field_customizer_setting($field_key, 'label', $default);
    return !empty($label) ? $label : $default;
}

/**
 * Get checkout field placeholder from Customizer.
 *
 * @param string $field_key Full field key (e.g., 'billing_email').
 * @param string $default   Default placeholder.
 * @return string
 */
function get_checkout_field_placeholder($field_key, $default)
{
    $placeholder = get_checkout_field_customizer_setting($field_key, 'placeholder', $default);
    return $placeholder !== null ? $placeholder : $default;
}

/**
 * Get checkout field priority from Customizer.
 *
 * @param string $field_key Full field key (e.g., 'billing_email').
 * @param int    $default   Default priority.
 * @return int
 */
function get_checkout_field_priority($field_key, $default = 10)
{
    $priority = get_checkout_field_customizer_setting($field_key, 'priority', $default);
    return (int) ($priority ?: $default);
}

/**
 * Get checkout field width from Customizer.
 *
 * @param string $field_key Full field key (e.g., 'billing_email').
 * @param string $default   Default width (100, 50, 33, 25).
 * @return string
 */
function get_checkout_field_width($field_key, $default = '100')
{
    $width = get_checkout_field_customizer_setting($field_key, 'width', $default);
    return $width ?: $default;
}

/**
 * Convert width percentage to CSS class.
 *
 * @param string $width Width percentage (25, 33, 50, 66, 75, 100).
 * @return array CSS classes for the field wrapper.
 */
function get_width_classes($width)
{
    $classes = ['form-row'];

    switch ($width) {
        case '25':
            $classes[] = 'form-row-quarter';
            break;
        case '33':
            $classes[] = 'form-row-third';
            break;
        case '50':
            $classes[] = 'form-row-half';
            break;
        case '66':
            $classes[] = 'form-row-two-thirds';
            break;
        case '75':
            $classes[] = 'form-row-three-quarters';
            break;
        case '100':
        default:
            $classes[] = 'form-row-wide';
            break;
    }

    return $classes;
}

/**
 * Customize checkout fields - main filter.
 *
 * Integrates with WordPress Customizer settings to allow:
 * - Enabling/disabling fields
 * - Custom labels
 * - Custom placeholders
 * - Required/optional toggle
 */
add_filter('woocommerce_checkout_fields', function ($fields) {
    // =========================================================================
    // BILLING FIELDS CUSTOMIZATION
    // =========================================================================

    if (isset($fields['billing'])) {
        // Define field order, placeholders, labels, and priorities
        // Lower priority = appears earlier in the form
        $billing_config = [
            'billing_email' => [
                'priority'    => 5,
                'label'       => __('Email Address', 'sage'),
                'placeholder' => __('your@email.com', 'sage'),
                'class'       => ['form-row-wide'],
                'autocomplete' => 'email',
                'required'    => true,
            ],
            'billing_phone' => [
                'priority'    => 10,
                'label'       => __('Phone Number', 'sage'),
                'placeholder' => __('+1 (555) 000-0000', 'sage'),
                'class'       => ['form-row-wide'],
                'autocomplete' => 'tel',
                'required'    => true,
            ],
            'billing_first_name' => [
                'priority'    => 20,
                'label'       => __('First Name', 'sage'),
                'placeholder' => __('John', 'sage'),
                'class'       => ['form-row-first'],
                'autocomplete' => 'given-name',
                'required'    => true,
            ],
            'billing_last_name' => [
                'priority'    => 25,
                'label'       => __('Last Name', 'sage'),
                'placeholder' => __('Doe', 'sage'),
                'class'       => ['form-row-last'],
                'autocomplete' => 'family-name',
                'required'    => true,
            ],
            'billing_company' => [
                'priority'    => 30,
                'label'       => __('Company', 'sage'),
                'placeholder' => __('Company name (optional)', 'sage'),
                'required'    => false,
                'class'       => ['form-row-wide'],
                'autocomplete' => 'organization',
            ],
            'billing_country' => [
                'priority'    => 35,
                'label'       => __('Country / Region', 'sage'),
                'class'       => ['form-row-wide', 'address-field', 'update_totals_on_change'],
                'required'    => true,
            ],
            'billing_address_1' => [
                'priority'    => 40,
                'label'       => __('Street Address', 'sage'),
                'placeholder' => __('House number and street name', 'sage'),
                'class'       => ['form-row-wide', 'address-field'],
                'autocomplete' => 'address-line1',
                'required'    => true,
            ],
            'billing_address_2' => [
                'priority'    => 45,
                'label'       => __('Address Line 2', 'sage'),
                'label_class' => ['screen-reader-text'],
                'placeholder' => __('Apartment, suite, unit, etc. (optional)', 'sage'),
                'required'    => false,
                'class'       => ['form-row-wide', 'address-field'],
                'autocomplete' => 'address-line2',
            ],
            'billing_city' => [
                'priority'    => 50,
                'label'       => __('City', 'sage'),
                'placeholder' => __('City', 'sage'),
                'class'       => ['form-row-first', 'address-field'],
                'autocomplete' => 'address-level2',
                'required'    => true,
            ],
            'billing_state' => [
                'priority'    => 55,
                'label'       => __('State / Province', 'sage'),
                'class'       => ['form-row-last', 'address-field'],
                'required'    => true,
            ],
            'billing_postcode' => [
                'priority'    => 60,
                'label'       => __('ZIP / Postal Code', 'sage'),
                'placeholder' => __('ZIP / Postal Code', 'sage'),
                'class'       => ['form-row-first', 'address-field'],
                'autocomplete' => 'postal-code',
                'required'    => true,
            ],
        ];

        // Apply billing field configurations with Customizer overrides
        foreach ($billing_config as $key => $config) {
            // Check if field is enabled via Customizer
            if (!is_checkout_field_enabled($key)) {
                unset($fields['billing'][$key]);
                continue;
            }

            if (isset($fields['billing'][$key])) {
                // Get Customizer values
                $custom_priority = get_checkout_field_priority($key, $config['priority']);
                $custom_width = get_checkout_field_width($key, '100');
                $width_classes = get_width_classes($custom_width);

                // Apply base configuration
                foreach ($config as $prop => $value) {
                    if ($prop === 'label') {
                        $fields['billing'][$key][$prop] = get_checkout_field_label($key, $value);
                    } elseif ($prop === 'placeholder') {
                        $fields['billing'][$key][$prop] = get_checkout_field_placeholder($key, $value);
                    } elseif ($prop === 'required') {
                        $fields['billing'][$key][$prop] = is_checkout_field_required($key, $value);
                    } elseif ($prop === 'priority') {
                        $fields['billing'][$key][$prop] = $custom_priority;
                    } elseif ($prop === 'class') {
                        // Merge width classes with existing classes (preserve address-field, etc.)
                        $existing_classes = array_filter($value, function($class) {
                            return strpos($class, 'form-row') === false;
                        });
                        $fields['billing'][$key][$prop] = array_merge($width_classes, $existing_classes);
                    } else {
                        $fields['billing'][$key][$prop] = $value;
                    }
                }

                // Add Tailwind input classes
                $input_type = isset($fields['billing'][$key]['type']) ? $fields['billing'][$key]['type'] : 'text';
                if ($input_type === 'country' || $input_type === 'state') {
                    $input_type = 'select';
                }
                $fields['billing'][$key]['input_class'] = get_checkout_input_classes($input_type);
            }
        }
    }

    // =========================================================================
    // SHIPPING FIELDS CUSTOMIZATION
    // =========================================================================

    if (isset($fields['shipping'])) {
        $shipping_config = [
            'shipping_first_name' => [
                'priority'    => 10,
                'label'       => __('First Name', 'sage'),
                'placeholder' => __('John', 'sage'),
                'class'       => ['form-row-first'],
                'autocomplete' => 'given-name',
                'required'    => true,
            ],
            'shipping_last_name' => [
                'priority'    => 15,
                'label'       => __('Last Name', 'sage'),
                'placeholder' => __('Doe', 'sage'),
                'class'       => ['form-row-last'],
                'autocomplete' => 'family-name',
                'required'    => true,
            ],
            'shipping_company' => [
                'priority'    => 20,
                'label'       => __('Company', 'sage'),
                'placeholder' => __('Company name (optional)', 'sage'),
                'required'    => false,
                'class'       => ['form-row-wide'],
                'autocomplete' => 'organization',
            ],
            'shipping_country' => [
                'priority'    => 25,
                'label'       => __('Country / Region', 'sage'),
                'class'       => ['form-row-wide', 'address-field', 'update_totals_on_change'],
                'required'    => true,
            ],
            'shipping_address_1' => [
                'priority'    => 30,
                'label'       => __('Street Address', 'sage'),
                'placeholder' => __('House number and street name', 'sage'),
                'class'       => ['form-row-wide', 'address-field'],
                'autocomplete' => 'address-line1',
                'required'    => true,
            ],
            'shipping_address_2' => [
                'priority'    => 35,
                'label'       => __('Address Line 2', 'sage'),
                'label_class' => ['screen-reader-text'],
                'placeholder' => __('Apartment, suite, unit, etc. (optional)', 'sage'),
                'required'    => false,
                'class'       => ['form-row-wide', 'address-field'],
                'autocomplete' => 'address-line2',
            ],
            'shipping_city' => [
                'priority'    => 40,
                'label'       => __('City', 'sage'),
                'placeholder' => __('City', 'sage'),
                'class'       => ['form-row-first', 'address-field'],
                'autocomplete' => 'address-level2',
                'required'    => true,
            ],
            'shipping_state' => [
                'priority'    => 45,
                'label'       => __('State / Province', 'sage'),
                'class'       => ['form-row-last', 'address-field'],
                'required'    => true,
            ],
            'shipping_postcode' => [
                'priority'    => 50,
                'label'       => __('ZIP / Postal Code', 'sage'),
                'placeholder' => __('ZIP / Postal Code', 'sage'),
                'class'       => ['form-row-first', 'address-field'],
                'autocomplete' => 'postal-code',
                'required'    => true,
            ],
        ];

        // Apply shipping field configurations with Customizer overrides
        foreach ($shipping_config as $key => $config) {
            // Check if field is enabled via Customizer
            if (!is_checkout_field_enabled($key)) {
                unset($fields['shipping'][$key]);
                continue;
            }

            if (isset($fields['shipping'][$key])) {
                // Get Customizer values
                $custom_priority = get_checkout_field_priority($key, $config['priority']);
                $custom_width = get_checkout_field_width($key, '100');
                $width_classes = get_width_classes($custom_width);

                // Apply base configuration
                foreach ($config as $prop => $value) {
                    if ($prop === 'label') {
                        $fields['shipping'][$key][$prop] = get_checkout_field_label($key, $value);
                    } elseif ($prop === 'placeholder') {
                        $fields['shipping'][$key][$prop] = get_checkout_field_placeholder($key, $value);
                    } elseif ($prop === 'required') {
                        $fields['shipping'][$key][$prop] = is_checkout_field_required($key, $value);
                    } elseif ($prop === 'priority') {
                        $fields['shipping'][$key][$prop] = $custom_priority;
                    } elseif ($prop === 'class') {
                        $existing_classes = array_filter($value, function($class) {
                            return strpos($class, 'form-row') === false;
                        });
                        $fields['shipping'][$key][$prop] = array_merge($width_classes, $existing_classes);
                    } else {
                        $fields['shipping'][$key][$prop] = $value;
                    }
                }

                // Add Tailwind input classes
                $input_type = isset($fields['shipping'][$key]['type']) ? $fields['shipping'][$key]['type'] : 'text';
                if ($input_type === 'country' || $input_type === 'state') {
                    $input_type = 'select';
                }
                $fields['shipping'][$key]['input_class'] = get_checkout_input_classes($input_type);
            }
        }
    }

    // =========================================================================
    // ORDER / ADDITIONAL FIELDS CUSTOMIZATION
    // =========================================================================

    if (isset($fields['order']['order_comments'])) {
        // Check if order comments field is enabled
        if (!is_checkout_field_enabled('order_comments')) {
            unset($fields['order']['order_comments']);
        } else {
            $default_label = __('Order Notes', 'sage');
            $default_placeholder = __('Special instructions for delivery, gift messages, or any other notes about your order...', 'sage');

            $fields['order']['order_comments']['label'] = get_checkout_field_label('order_comments', $default_label);
            $fields['order']['order_comments']['placeholder'] = get_checkout_field_placeholder('order_comments', $default_placeholder);
            $fields['order']['order_comments']['required'] = is_checkout_field_required('order_comments', false);
            $fields['order']['order_comments']['input_class'] = get_checkout_input_classes('textarea');
            $fields['order']['order_comments']['class'] = ['form-row-wide', 'notes'];
        }
    }

    // =========================================================================
    // ACCOUNT FIELDS CUSTOMIZATION (for guest checkout with registration)
    // =========================================================================

    if (isset($fields['account'])) {
        if (isset($fields['account']['account_username'])) {
            $fields['account']['account_username']['placeholder'] = __('Choose a username', 'sage');
            $fields['account']['account_username']['input_class'] = get_checkout_input_classes('text');
        }

        if (isset($fields['account']['account_password'])) {
            $fields['account']['account_password']['placeholder'] = __('Create a password', 'sage');
            $fields['account']['account_password']['input_class'] = get_checkout_input_classes('text');
        }

        if (isset($fields['account']['account_password-2'])) {
            $fields['account']['account_password-2']['placeholder'] = __('Confirm your password', 'sage');
            $fields['account']['account_password-2']['input_class'] = get_checkout_input_classes('text');
        }
    }

    return $fields;
}, 9999); // High priority to run after all other filters

/**
 * Customize default address field arguments.
 *
 * This filter applies to both checkout and my account address forms.
 */
add_filter('woocommerce_default_address_fields', function ($fields) {
    // Reorder fields to put most important first
    $priority = 10;
    $order = ['first_name', 'last_name', 'company', 'country', 'address_1', 'address_2', 'city', 'state', 'postcode'];

    foreach ($order as $field_key) {
        if (isset($fields[$field_key])) {
            $fields[$field_key]['priority'] = $priority;
            $priority += 10;
        }
    }

    // Make address_2 and company optional with updated label
    if (isset($fields['address_2'])) {
        $fields['address_2']['required'] = false;
        $fields['address_2']['label_class'] = ['screen-reader-text'];
    }

    if (isset($fields['company'])) {
        $fields['company']['required'] = false;
    }

    return $fields;
});

/**
 * Add custom validation for checkout fields.
 */
add_action('woocommerce_checkout_process', function () {
    // Validate phone number format (basic validation)
    $phone = isset($_POST['billing_phone']) ? sanitize_text_field(wp_unslash($_POST['billing_phone'])) : '';

    if (! empty($phone)) {
        // Remove common formatting characters for validation
        $phone_digits = preg_replace('/[^0-9]/', '', $phone);

        // Check if phone has reasonable length (between 7 and 15 digits)
        if (strlen($phone_digits) < 7 || strlen($phone_digits) > 15) {
            wc_add_notice(
                __('Please enter a valid phone number.', 'sage'),
                'error'
            );
        }
    }

    // Validate email domain (basic check for common typos)
    $email = isset($_POST['billing_email']) ? sanitize_email(wp_unslash($_POST['billing_email'])) : '';

    if (! empty($email)) {
        $domain = substr(strrchr($email, '@'), 1);

        // Check for common typos in email domains
        $common_typos = [
            'gmial.com'    => 'gmail.com',
            'gmai.com'     => 'gmail.com',
            'gamil.com'    => 'gmail.com',
            'gmail.co'     => 'gmail.com',
            'hotmal.com'   => 'hotmail.com',
            'hotmai.com'   => 'hotmail.com',
            'yahooo.com'   => 'yahoo.com',
            'yaho.com'     => 'yahoo.com',
        ];

        if (isset($common_typos[$domain])) {
            wc_add_notice(
                sprintf(
                    /* translators: %s: suggested email domain */
                    __('Did you mean @%s? Please check your email address.', 'sage'),
                    $common_typos[$domain]
                ),
                'notice'
            );
        }
    }
});

/**
 * Customize form field HTML output.
 *
 * Adds additional Tailwind classes to the form field wrapper.
 */
add_filter('woocommerce_form_field_args', function ($args, $key, $value) {
    // Add base classes to all form fields
    if (! isset($args['class'])) {
        $args['class'] = [];
    }

    // Ensure class is an array
    if (! is_array($args['class'])) {
        $args['class'] = [$args['class']];
    }

    // Add common wrapper classes
    $args['class'][] = 'woocommerce-form-field';

    // Add label classes for better styling
    if (! isset($args['label_class'])) {
        $args['label_class'] = [];
    }
    $args['label_class'][] = 'block';
    $args['label_class'][] = 'mb-1.5';
    $args['label_class'][] = 'text-sm';
    $args['label_class'][] = 'font-medium';
    $args['label_class'][] = 'text-secondary-700';

    return $args;
}, 10, 3);

/**
 * Customize the checkout field container HTML.
 *
 * Wraps fields in a container with responsive grid classes.
 */
add_filter('woocommerce_form_field', function ($field, $key, $args, $value) {
    // Add data attribute for easier JavaScript targeting
    if (strpos($field, 'woocommerce-input-wrapper') !== false) {
        $field = str_replace(
            'woocommerce-input-wrapper',
            'woocommerce-input-wrapper" data-field="' . esc_attr($key),
            $field
        );
    }

    return $field;
}, 10, 4);

/**
 * Custom Body Classes for WooCommerce Pages
 *
 * Add utility classes to body for easier CSS targeting.
 */
add_filter('body_class', function ($classes) {
    if (function_exists('is_shop') && is_shop()) {
        $classes[] = 'wc-shop-page';
    }

    if (function_exists('is_product_category') && is_product_category()) {
        $classes[] = 'wc-category-page';
    }

    if (function_exists('is_product_tag') && is_product_tag()) {
        $classes[] = 'wc-tag-page';
    }

    if (function_exists('is_product') && is_product()) {
        $classes[] = 'wc-single-product-page';

        // Add product type class
        global $product;

        // Ensure $product is a valid WC_Product object
        if (! $product instanceof \WC_Product) {
            $product = wc_get_product(get_the_ID());
        }

        if ($product instanceof \WC_Product) {
            $classes[] = 'wc-product-type-' . $product->get_type();

            // Add "on sale" class
            if ($product->is_on_sale()) {
                $classes[] = 'wc-product-on-sale';
            }

            // Add "out of stock" class
            if (! $product->is_in_stock()) {
                $classes[] = 'wc-product-out-of-stock';
            }
        }
    }

    if (function_exists('is_cart') && is_cart()) {
        $classes[] = 'wc-cart-page';

        // Add empty cart class
        if (WC()->cart && WC()->cart->is_empty()) {
            $classes[] = 'wc-cart-empty';
        }
    }

    if (function_exists('is_checkout') && is_checkout()) {
        $classes[] = 'wc-checkout-page';
    }

    if (function_exists('is_account_page') && is_account_page()) {
        $classes[] = 'wc-account-page';
    }

    return $classes;
});

/**
 * WooCommerce Notice Wrapper Customization
 *
 * Customize the wrapper for WooCommerce notices (success, error, info).
 */
add_filter('woocommerce_demo_store', function ($notice) {
    // Customize demo store notice styling
    return '<div class="woocommerce-store-notice fixed bottom-0 left-0 right-0 z-50 bg-primary-600 px-4 py-3 text-center text-sm text-white shadow-lg">
        <a href="#" class="woocommerce-store-notice__dismiss-link absolute right-4 top-1/2 -translate-y-1/2 text-white/80 hover:text-white">&times;</a>
        ' . wp_kses_post($notice) . '
    </div>';
});

/**
 * Login Form Customization
 *
 * Modify the login form redirect URL.
 */
add_filter('woocommerce_login_redirect', function ($redirect, $user) {
    // Redirect to My Account page after login
    return wc_get_page_permalink('myaccount');
}, 10, 2);

/**
 * Registration Form Customization
 *
 * Modify the registration redirect URL.
 */
add_filter('woocommerce_registration_redirect', function () {
    // Redirect to My Account page after registration
    return wc_get_page_permalink('myaccount');
});

/**
 * Product Thumbnail Size for Archives
 *
 * Ensure consistent thumbnail sizes in product loops.
 */
add_filter('single_product_archive_thumbnail_size', function () {
    return 'woocommerce_thumbnail';
});

/**
 * Disable WooCommerce Default Styles (Additional)
 *
 * We already handle main styles in setup.php, but ensure all styles are removed.
 */
add_filter('woocommerce_enqueue_styles', '__return_empty_array');

/**
 * Gallery Thumbnail Columns
 *
 * Control the number of thumbnails per row in product gallery.
 */
add_filter('woocommerce_product_thumbnails_columns', function () {
    return 4;
});

/**
 * Review Gravatar Size
 *
 * Control the size of gravatars in product reviews.
 */
add_filter('woocommerce_review_gravatar_size', function () {
    return 48;
});

/**
 * Order Button Text
 *
 * Customize the "Place Order" button text on checkout.
 */
add_filter('woocommerce_order_button_text', function () {
    return __('Complete Order', 'sage');
});

/**
 * Cart Item Quantity Input Args
 *
 * Customize quantity input on cart page.
 */
add_filter('woocommerce_quantity_input_args', function ($args, $product) {
    // Set step to 1 for all products
    $args['step'] = 1;

    // Set minimum to 1 (can't have 0 items)
    $args['min_value'] = 1;

    // Set maximum based on stock if managing stock
    if ($product->managing_stock() && $product->get_stock_quantity()) {
        $args['max_value'] = $product->get_stock_quantity();
    }

    return $args;
}, 10, 2);

/**
 * Single Product Quantity Input Args
 *
 * Specific customization for single product page quantity input.
 */
add_filter('woocommerce_quantity_input_args', function ($args, $product) {
    if (is_product()) {
        $args['input_value'] = 1; // Default quantity on single product pages
    }

    return $args;
}, 11, 2);

/**
 * Add Product Schema Markup
 *
 * Enhance product structured data for SEO.
 */
add_filter('woocommerce_structured_data_product', function ($markup, $product) {
    // Add brand if available (from a custom field or product attribute)
    $brand = $product->get_attribute('brand');
    if ($brand) {
        $markup['brand'] = [
            '@type' => 'Brand',
            'name'  => $brand,
        ];
    }

    // Add availability status more explicitly
    if ($product->is_in_stock()) {
        $markup['availability'] = 'https://schema.org/InStock';
    } else {
        $markup['availability'] = 'https://schema.org/OutOfStock';
    }

    return $markup;
}, 10, 2);

/**
 * Customize "Continue Shopping" URL
 *
 * After adding to cart, customize where "Continue Shopping" goes.
 */
add_filter('woocommerce_continue_shopping_redirect', function () {
    return wc_get_page_permalink('shop');
});

/**
 * Cart Hash for AJAX Efficiency
 *
 * Add cart hash to cart page for JavaScript cache invalidation.
 */
add_action('woocommerce_before_cart', function () {
    if (WC()->cart) {
        echo '<div class="cart-hash-container" data-cart-hash="' . esc_attr(WC()->cart->get_cart_hash()) . '" style="display:none;"></div>';
    }
});

/**
 * Minimum Order Amount Notice (Optional - Commented Out)
 *
 * Uncomment and configure to set a minimum order amount.
 */
/*
add_action('woocommerce_check_cart_items', function () {
    $minimum_amount = 50; // Minimum order amount

    if (WC()->cart->subtotal < $minimum_amount) {
        wc_add_notice(
            sprintf(
                __('Your current order total is %s — you must have an order with a minimum of %s to place your order.', 'sage'),
                wc_price(WC()->cart->subtotal),
                wc_price($minimum_amount)
            ),
            'error'
        );
    }
});
*/

/**
 * Remove "What is PayPal?" link from checkout
 *
 * Removes the info link that can distract customers.
 */
add_filter('woocommerce_gateway_icon', function ($icon, $gateway_id) {
    if ($gateway_id === 'paypal') {
        // Remove the "What is PayPal?" link, keep just the icon
        $icon = preg_replace('/<a[^>]*>.*?<\/a>/i', '', $icon);
    }
    return $icon;
}, 10, 2);

/**
 * Customize "Sold Individually" Products
 *
 * Products marked as "sold individually" show different messaging.
 */
add_filter('woocommerce_add_to_cart_sold_individually_quantity', function () {
    return 1;
});

/**
 * Ajax Search Products - Include SKU (Optional)
 *
 * Allow searching products by SKU in admin and frontend search.
 */
add_filter('woocommerce_product_search_form_params', function ($params) {
    return $params;
});

/**
 * ============================================================================
 * AJAX Product Search REST API (T8.3)
 * ============================================================================
 *
 * Custom REST API endpoint for AJAX product search in the search popup.
 * Returns both matching categories and products.
 *
 * Endpoint: /wp-json/sega/v1/search
 * Method: GET
 * Parameters:
 *   - s (string): Search query (required, min 2 characters)
 *   - per_page (int): Number of products to return (default: 6, max: 20)
 *
 * Response format:
 * {
 *   "categories": [
 *     { "id": 1, "name": "Category Name", "url": "...", "count": 10 }
 *   ],
 *   "products": [
 *     {
 *       "id": 1,
 *       "name": "Product Name",
 *       "url": "...",
 *       "image": "...",
 *       "regular_price": "$10.00",
 *       "sale_price": "$8.00",
 *       "on_sale": true
 *     }
 *   ]
 * }
 */

/**
 * Register the search REST API endpoint.
 */
add_action('rest_api_init', function () {
    register_rest_route('sega/v1', '/search', [
        'methods'             => 'GET',
        'callback'            => __NAMESPACE__ . '\\rest_api_product_search',
        'permission_callback' => '__return_true', // Public endpoint
        'args'                => [
            's' => [
                'required'          => true,
                'type'              => 'string',
                'sanitize_callback' => 'sanitize_text_field',
                'validate_callback' => function ($param) {
                    return is_string($param) && strlen($param) >= 2;
                },
            ],
            'per_page' => [
                'required'          => false,
                'type'              => 'integer',
                'default'           => 6,
                'sanitize_callback' => 'absint',
                'validate_callback' => function ($param) {
                    return is_numeric($param) && $param >= 1 && $param <= 20;
                },
            ],
        ],
    ]);
});

/**
 * Handle the product search REST API request.
 *
 * @param \WP_REST_Request $request The REST request object.
 * @return \WP_REST_Response The REST response with search results.
 */
function rest_api_product_search($request)
{
    $search_query = $request->get_param('s');
    $per_page = $request->get_param('per_page') ?: 6;

    // Search categories
    $categories = search_product_categories($search_query, 4);

    // Search products
    $products = search_products($search_query, $per_page);

    return rest_ensure_response([
        'categories' => $categories,
        'products'   => $products,
        'query'      => $search_query,
    ]);
}

/**
 * Search product categories by name.
 *
 * @param string $query  The search query.
 * @param int    $limit  Maximum number of categories to return.
 * @return array Array of matching categories.
 */
function search_product_categories($query, $limit = 4)
{
    $terms = get_terms([
        'taxonomy'   => 'product_cat',
        'hide_empty' => true,
        'name__like' => $query,
        'number'     => $limit,
        'orderby'    => 'count',
        'order'      => 'DESC',
    ]);

    if (is_wp_error($terms) || empty($terms)) {
        return [];
    }

    $categories = [];

    foreach ($terms as $term) {
        $categories[] = [
            'id'    => $term->term_id,
            'name'  => $term->name,
            'slug'  => $term->slug,
            'url'   => get_term_link($term),
            'count' => $term->count,
        ];
    }

    return $categories;
}

/**
 * Search products by title, SKU, and content.
 *
 * @param string $query    The search query.
 * @param int    $per_page Maximum number of products to return.
 * @return array Array of matching products.
 */
function search_products($query, $per_page = 6)
{
    // Build the WP_Query args for product search
    $args = [
        'post_type'      => 'product',
        'post_status'    => 'publish',
        's'              => $query,
        'posts_per_page' => $per_page,
        'orderby'        => 'relevance',
        'order'          => 'DESC',
        'meta_query'     => [],
        'tax_query'      => [
            // Exclude hidden products (WooCommerce 3.0+ uses taxonomy for visibility)
            [
                'taxonomy' => 'product_visibility',
                'field'    => 'name',
                'terms'    => ['exclude-from-search'],
                'operator' => 'NOT IN',
            ],
        ],
    ];

    // Check if WooCommerce is set to hide out of stock products from catalog
    if ('yes' === get_option('woocommerce_hide_out_of_stock_items')) {
        $args['tax_query'][] = [
            'taxonomy' => 'product_visibility',
            'field'    => 'name',
            'terms'    => ['outofstock'],
            'operator' => 'NOT IN',
        ];
    }

    // Also search by SKU
    $sku_products = search_products_by_sku($query, $per_page);
    $sku_ids = array_map(function ($product) {
        return $product['id'];
    }, $sku_products);

    $search_query = new \WP_Query($args);
    $products = [];
    $added_ids = [];

    // First add SKU matches (they're more specific)
    foreach ($sku_products as $product) {
        if (count($products) >= $per_page) {
            break;
        }
        $products[] = $product;
        $added_ids[] = $product['id'];
    }

    // Then add title/content matches (excluding SKU matches to avoid duplicates)
    if ($search_query->have_posts()) {
        while ($search_query->have_posts() && count($products) < $per_page) {
            $search_query->the_post();
            $product_id = get_the_ID();

            // Skip if already added from SKU search
            if (in_array($product_id, $added_ids)) {
                continue;
            }

            $product = wc_get_product($product_id);

            if (! $product || ! $product->is_visible()) {
                continue;
            }

            $products[] = format_product_for_search($product);
            $added_ids[] = $product_id;
        }
        wp_reset_postdata();
    }

    return $products;
}

/**
 * Search products by SKU.
 *
 * @param string $query    The SKU to search for.
 * @param int    $per_page Maximum number of products to return.
 * @return array Array of matching products.
 */
function search_products_by_sku($query, $per_page = 6)
{
    global $wpdb;

    // Search for products with SKU matching the query
    // This includes partial matches
    $sku_query = '%' . $wpdb->esc_like($query) . '%';

    // Get product IDs where SKU matches (including variations)
    $product_ids = $wpdb->get_col(
        $wpdb->prepare(
            "SELECT DISTINCT p.ID
            FROM {$wpdb->posts} p
            INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
            WHERE p.post_type IN ('product', 'product_variation')
            AND p.post_status = 'publish'
            AND pm.meta_key = '_sku'
            AND pm.meta_value LIKE %s
            LIMIT %d",
            $sku_query,
            $per_page
        )
    );

    if (empty($product_ids)) {
        return [];
    }

    $products = [];

    foreach ($product_ids as $product_id) {
        $product = wc_get_product($product_id);

        if (! $product) {
            continue;
        }

        // If it's a variation, get the parent product for display
        if ($product->is_type('variation')) {
            $parent_id = $product->get_parent_id();
            $parent_product = wc_get_product($parent_id);

            if ($parent_product && $parent_product->is_visible()) {
                // Check if parent already added
                $parent_already_added = false;
                foreach ($products as $p) {
                    if ($p['id'] === $parent_id) {
                        $parent_already_added = true;
                        break;
                    }
                }

                if (! $parent_already_added) {
                    $products[] = format_product_for_search($parent_product);
                }
            }
        } elseif ($product->is_visible()) {
            $products[] = format_product_for_search($product);
        }

        if (count($products) >= $per_page) {
            break;
        }
    }

    return $products;
}

/**
 * Format a WC_Product for the search results.
 *
 * @param \WC_Product $product The product object.
 * @return array Formatted product data.
 */
function format_product_for_search($product)
{
    // Get product image
    $image_id = $product->get_image_id();
    $image_url = '';

    if ($image_id) {
        $image_src = wp_get_attachment_image_src($image_id, 'woocommerce_thumbnail');
        $image_url = $image_src ? $image_src[0] : '';
    }

    // If no image, use placeholder
    if (! $image_url) {
        $image_url = wc_placeholder_img_src('woocommerce_thumbnail');
    }

    // Get prices
    $regular_price = $product->get_regular_price();
    $sale_price = $product->get_sale_price();
    $on_sale = $product->is_on_sale();

    // For variable products, get price range
    if ($product->is_type('variable')) {
        $regular_price = $product->get_variation_regular_price('min');
        $sale_price = $product->get_variation_sale_price('min');
    }

    // Format prices with currency - decode HTML entities for clean display
    $formatted_regular_price = $regular_price ? html_entity_decode(wp_strip_all_tags(wc_price($regular_price)), ENT_QUOTES, 'UTF-8') : '';
    $formatted_sale_price = $sale_price ? html_entity_decode(wp_strip_all_tags(wc_price($sale_price)), ENT_QUOTES, 'UTF-8') : '';

    // Get stock status
    $stock_status = $product->get_stock_status(); // 'instock', 'outofstock', 'onbackorder'
    $stock_quantity = $product->get_stock_quantity();

    return [
        'id'             => $product->get_id(),
        'name'           => $product->get_name(),
        'url'            => $product->get_permalink(),
        'image'          => $image_url,
        'regular_price'  => $formatted_regular_price,
        'sale_price'     => $formatted_sale_price,
        'on_sale'        => $on_sale,
        'type'           => $product->get_type(),
        'sku'            => $product->get_sku(),
        'stock_status'   => $stock_status,
        'stock_quantity' => $stock_quantity,
        'in_stock'       => $product->is_in_stock(),
    ];
}

/**
 * Filter products by stock status in admin
 */
add_filter('woocommerce_products_admin_list_table_filters', function ($filters) {
    return $filters;
});

/**
 * ============================================================================
 * Custom Sale Badge with Percentage
 * ============================================================================
 *
 * Replace the default "Sale!" badge with a percentage discount badge.
 * Handles both simple and variable products.
 */

/**
 * Calculate the sale percentage for a product.
 *
 * For simple products: calculates the percentage from regular to sale price.
 * For variable products: returns the maximum discount percentage across all variations.
 *
 * @param \WC_Product $product The product object.
 * @return int The discount percentage (0 if not on sale or can't calculate).
 */
function get_product_sale_percentage($product)
{
    if (! $product || ! $product->is_on_sale()) {
        return 0;
    }

    // Handle variable products
    if ($product->is_type('variable')) {
        $max_percentage = 0;
        $variations = $product->get_available_variations();

        foreach ($variations as $variation) {
            $regular_price = (float) ($variation['display_regular_price'] ?? 0);
            $sale_price = (float) ($variation['display_price'] ?? 0);

            if ($regular_price > 0 && $sale_price > 0 && $sale_price < $regular_price) {
                $percentage = round((($regular_price - $sale_price) / $regular_price) * 100);
                $max_percentage = max($max_percentage, $percentage);
            }
        }

        return (int) $max_percentage;
    }

    // Handle simple and other product types
    $regular_price = (float) $product->get_regular_price();
    $sale_price = (float) $product->get_sale_price();

    if ($regular_price <= 0 || $sale_price <= 0 || $sale_price >= $regular_price) {
        return 0;
    }

    return (int) round((($regular_price - $sale_price) / $regular_price) * 100);
}

/**
 * Custom sale flash badge with percentage discount.
 *
 * Replaces the default "Sale!" text with the actual discount percentage.
 * Uses Tailwind CSS classes for styling.
 *
 * @param string      $html    The sale flash HTML.
 * @param \WP_Post    $post    The post object.
 * @param \WC_Product $product The product object.
 * @return string Modified sale flash HTML.
 */
add_filter('woocommerce_sale_flash', function ($html, $post, $product) {
    $percentage = get_product_sale_percentage($product);

    if ($percentage > 0) {
        // Badge with percentage and down arrow icon
        return sprintf(
            '<span class="onsale inline-flex items-center gap-1 rounded-full bg-red-500 px-2.5 py-1 text-xs font-bold text-white shadow-sm">
                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                </svg>
                -%d%%
            </span>',
            $percentage
        );
    }

    // Fallback to simple "Sale" badge if percentage can't be calculated
    return sprintf(
        '<span class="onsale rounded-full bg-red-500 px-2.5 py-1 text-xs font-bold text-white shadow-sm">%s</span>',
        esc_html__('Sale', 'sage')
    );
}, 10, 3);

/**
 * ============================================================================
 * Disable Elementor on Front Page (Fix JavaScript Errors)
 * ============================================================================
 *
 * Front page uses Blade templates and doesn't need Elementor.
 * Disabling Elementor scripts/styles on front page prevents JS errors.
 */
add_action('wp_enqueue_scripts', function () {
    // Only run on front page
    if (! is_front_page()) {
        return;
    }

    // Dequeue Elementor frontend scripts
    wp_dequeue_script('elementor-frontend');
    wp_dequeue_script('elementor-frontend-modules');
    wp_dequeue_script('elementor-pro-frontend');

    // Dequeue Elementor styles
    wp_dequeue_style('elementor-frontend');
    wp_dequeue_style('elementor-pro');
    wp_dequeue_style('elementor-icons');
    wp_dequeue_style('elementor-animations');
}, 999);

/**
 * ============================================================================
 * AJAX Product Filter
 * ============================================================================
 *
 * Handles AJAX requests for filtering products by category, price, etc.
 * Returns HTML for products grid, result count, and pagination.
 */

/**
 * Register AJAX handlers for product filtering.
 */
add_action('wp_ajax_filter_products', __NAMESPACE__ . '\\ajax_filter_products');
add_action('wp_ajax_nopriv_filter_products', __NAMESPACE__ . '\\ajax_filter_products');

/**
 * AJAX handler for product filtering.
 *
 * @return void
 */
function ajax_filter_products(): void
{
    // Verify nonce for security
    if (! isset($_POST['nonce']) || ! wp_verify_nonce($_POST['nonce'], 'filter_products_nonce')) {
        wp_send_json_error(['message' => __('Security check failed', 'sage')], 403);
    }

    // Get filter parameters - categories are now IDs, deduplicate
    $categories = isset($_POST['categories']) ? array_values(array_unique(array_map('absint', (array) $_POST['categories']))) : [];
    $categories = array_filter($categories, fn($id) => $id > 0); // Remove invalid IDs
    $min_price = isset($_POST['min_price']) ? floatval($_POST['min_price']) : null;
    $max_price = isset($_POST['max_price']) ? floatval($_POST['max_price']) : null;
    $orderby = isset($_POST['orderby']) ? sanitize_text_field($_POST['orderby']) : 'menu_order';
    $per_page = isset($_POST['per_page']) ? absint($_POST['per_page']) : 12;
    $paged = isset($_POST['paged']) ? absint($_POST['paged']) : 1;
    $on_sale = isset($_POST['on_sale']) && $_POST['on_sale'] === '1';
    $in_stock = isset($_POST['in_stock']) && $_POST['in_stock'] === '1';

    // Validate per_page
    $allowed_per_page = [12, 24, 48, 96];
    if (! in_array($per_page, $allowed_per_page, true)) {
        $per_page = 12;
    }

    // Build query args
    $args = [
        'post_type'      => 'product',
        'post_status'    => 'publish',
        'posts_per_page' => $per_page,
        'paged'          => $paged,
        'orderby'        => 'menu_order title',
        'order'          => 'ASC',
    ];

    // Apply sorting
    switch ($orderby) {
        case 'popularity':
            $args['meta_key'] = 'total_sales';
            $args['orderby'] = 'meta_value_num';
            $args['order'] = 'DESC';
            break;
        case 'rating':
            $args['meta_key'] = '_wc_average_rating';
            $args['orderby'] = 'meta_value_num';
            $args['order'] = 'DESC';
            break;
        case 'date':
            $args['orderby'] = 'date';
            $args['order'] = 'DESC';
            break;
        case 'price':
            $args['meta_key'] = '_price';
            $args['orderby'] = 'meta_value_num';
            $args['order'] = 'ASC';
            break;
        case 'price-desc':
            $args['meta_key'] = '_price';
            $args['orderby'] = 'meta_value_num';
            $args['order'] = 'DESC';
            break;
    }

    // Apply category filter (using term IDs)
    if (! empty($categories)) {
        $args['tax_query'] = [
            [
                'taxonomy' => 'product_cat',
                'field'    => 'term_id',
                'terms'    => $categories,
                'operator' => 'IN',
            ],
        ];
    }

    // Apply price filter
    if ($min_price !== null || $max_price !== null) {
        $args['meta_query'] = ['relation' => 'AND'];

        if ($min_price !== null) {
            $args['meta_query'][] = [
                'key'     => '_price',
                'value'   => $min_price,
                'compare' => '>=',
                'type'    => 'DECIMAL(10,2)',
            ];
        }

        if ($max_price !== null) {
            $args['meta_query'][] = [
                'key'     => '_price',
                'value'   => $max_price,
                'compare' => '<=',
                'type'    => 'DECIMAL(10,2)',
            ];
        }
    }

    // Apply on sale filter
    if ($on_sale) {
        $sale_products = wc_get_product_ids_on_sale();
        $args['post__in'] = ! empty($sale_products) ? $sale_products : [0];
    }

    // Apply in stock filter
    if ($in_stock) {
        if (! isset($args['meta_query'])) {
            $args['meta_query'] = ['relation' => 'AND'];
        }
        $args['meta_query'][] = [
            'key'     => '_stock_status',
            'value'   => 'instock',
            'compare' => '=',
        ];
    }

    // Run the query
    $query = new \WP_Query($args);

    // Setup WooCommerce loop
    wc_setup_loop([
        'name'         => 'product',
        'is_paginated' => true,
        'total'        => $query->found_posts,
        'total_pages'  => $query->max_num_pages,
        'per_page'     => $per_page,
        'current_page' => $paged,
    ]);

    // Get grid classes from Shop composer
    $columns = apply_filters('loop_shop_columns', wc_get_default_products_per_row());
    $grid_classes = [
        1 => 'grid-cols-1',
        2 => 'grid-cols-1 xs:grid-cols-2',
        3 => 'grid-cols-1 xs:grid-cols-2 md:grid-cols-3',
        4 => 'grid-cols-1 xs:grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4',
        5 => 'grid-cols-1 xs:grid-cols-2 sm:grid-cols-3 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5',
        6 => 'grid-cols-1 xs:grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6',
    ];
    $grid_class = $grid_classes[$columns] ?? $grid_classes[4];

    // Build response
    ob_start();

    if ($query->have_posts()) {
        echo '<ul class="products grid gap-3 xs:gap-4 sm:gap-5 lg:gap-6 xl:gap-8 ' . esc_attr($grid_class) . '">';

        while ($query->have_posts()) {
            $query->the_post();
            $product = wc_get_product(get_the_ID());

            echo '<li class="flex w-full">';
            echo \Roots\view('partials.product-card', ['product' => $product])->render();
            echo '</li>';
        }

        echo '</ul>';
    } else {
        echo '<div class="flex flex-col items-center justify-center py-16 text-center">';
        echo '<svg class="mb-4 h-16 w-16 text-secondary-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">';
        echo '<path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />';
        echo '</svg>';
        echo '<h2 class="mb-2 text-xl font-semibold text-secondary-900">' . esc_html__('No products found', 'sage') . '</h2>';
        echo '<p class="mb-6 max-w-sm text-secondary-600">' . esc_html__('No products match your selected filters. Try adjusting your filter criteria.', 'sage') . '</p>';
        echo '</div>';
    }

    $products_html = ob_get_clean();

    // Build result count HTML
    $total = $query->found_posts;
    $first = (($paged - 1) * $per_page) + 1;
    $last = min($paged * $per_page, $total);

    if ($total <= $per_page || $query->max_num_pages === 1) {
        $result_count = sprintf(
            _n('Showing the single result', 'Showing all %d results', $total, 'sage'),
            $total
        );
    } else {
        $result_count = sprintf(
            __('Showing %1$d–%2$d of %3$d results', 'sage'),
            $first,
            $last,
            $total
        );
    }

    // Build pagination HTML with custom styling
    ob_start();
    if ($query->max_num_pages > 1) {
        $total_pages = $query->max_num_pages;
        $range = 2;

        echo '<nav class="mt-10 flex items-center justify-center" aria-label="' . esc_attr__('Product pagination', 'sage') . '">';
        echo '<ul class="flex items-center gap-1">';

        // Previous button
        $prev_class = $paged > 1
            ? 'text-secondary-600 hover:bg-secondary-100 hover:text-secondary-900'
            : 'text-secondary-300 cursor-not-allowed pointer-events-none';
        echo '<li>';
        echo '<a href="#" class="pagination-btn flex h-10 w-10 items-center justify-center rounded-lg transition-colors ' . $prev_class . '" data-page="' . ($paged - 1) . '" aria-label="' . esc_attr__('Previous page', 'sage') . '">';
        echo '<svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>';
        echo '</a>';
        echo '</li>';

        // Page numbers
        $show_dots = false;
        for ($i = 1; $i <= $total_pages; $i++) {
            if ($i == 1 || $i == $total_pages || ($i >= $paged - $range && $i <= $paged + $range)) {
                $show_dots = true;
                $page_class = $paged === $i
                    ? 'bg-primary-600 text-white'
                    : 'text-secondary-700 hover:bg-secondary-100';
                echo '<li>';
                echo '<a href="#" class="pagination-btn flex h-10 w-10 items-center justify-center rounded-lg text-sm font-medium transition-colors ' . $page_class . '" data-page="' . $i . '"' . ($paged === $i ? ' aria-current="page"' : '') . '>';
                echo $i;
                echo '</a>';
                echo '</li>';
            } elseif ($show_dots) {
                $show_dots = false;
                echo '<li><span class="flex h-10 w-10 items-center justify-center text-secondary-400">&hellip;</span></li>';
            }
        }

        // Next button
        $next_class = $paged < $total_pages
            ? 'text-secondary-600 hover:bg-secondary-100 hover:text-secondary-900'
            : 'text-secondary-300 cursor-not-allowed pointer-events-none';
        echo '<li>';
        echo '<a href="#" class="pagination-btn flex h-10 w-10 items-center justify-center rounded-lg transition-colors ' . $next_class . '" data-page="' . ($paged + 1) . '" aria-label="' . esc_attr__('Next page', 'sage') . '">';
        echo '<svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>';
        echo '</a>';
        echo '</li>';

        echo '</ul>';
        echo '</nav>';
    }
    $pagination_html = ob_get_clean();

    wp_reset_postdata();

    // Build active filters HTML
    $active_filters = [];

    // Category filters from cat_ids
    if (! empty($categories)) {
        foreach ($categories as $cat_id) {
            $term = get_term($cat_id, 'product_cat');
            if ($term && ! is_wp_error($term)) {
                $remaining_ids = array_filter($categories, fn($id) => $id !== $cat_id);
                $active_filters[] = [
                    'type'  => 'category',
                    'label' => $term->name,
                    'id'    => $cat_id,
                ];
            }
        }
    }

    // Price filter
    if ($min_price > 0 || $max_price > 0) {
        $price_label = '';
        if ($min_price > 0 && $max_price > 0) {
            $price_label = wc_price($min_price) . ' - ' . wc_price($max_price);
        } elseif ($min_price > 0) {
            $price_label = sprintf(__('From %s', 'sage'), wc_price($min_price));
        } else {
            $price_label = sprintf(__('Up to %s', 'sage'), wc_price($max_price));
        }
        $active_filters[] = [
            'type'  => 'price',
            'label' => $price_label,
        ];
    }

    // On Sale filter
    if ($on_sale) {
        $active_filters[] = [
            'type'  => 'on_sale',
            'label' => __('On Sale', 'sage'),
        ];
    }

    // In Stock filter
    if ($in_stock) {
        $active_filters[] = [
            'type'  => 'in_stock',
            'label' => __('In Stock', 'sage'),
        ];
    }

    // Send JSON response
    wp_send_json_success([
        'products'       => $products_html,
        'pagination'     => $pagination_html,
        'result_count'   => $result_count,
        'total'          => $total,
        'total_pages'    => $query->max_num_pages,
        'current_page'   => $paged,
        'active_filters' => $active_filters,
    ]);
}

/**
 * Output AJAX data for shop filtering.
 * Uses wp_head to inject global JS variable before Alpine.js loads.
 */
add_action('wp_head', function () {
    if (! is_shop() && ! is_product_category() && ! is_product_tag()) {
        return;
    }

    $data = [
        'ajaxUrl'         => admin_url('admin-ajax.php'),
        'nonce'           => wp_create_nonce('filter_products_nonce'),
        'shopUrl'         => get_permalink(wc_get_page_id('shop')),
        'isCategoryPage'  => is_product_category(),
        'categoryId'      => null,
        'categoryUrl'     => null,
    ];

    // Add category info when on a category page
    if (is_product_category()) {
        $term = get_queried_object();
        if ($term && ! is_wp_error($term)) {
            $data['categoryId']  = $term->term_id;
            $data['categoryUrl'] = get_term_link($term);
        }
    }

    echo '<script>window.sageShopAjax = ' . wp_json_encode($data) . ';</script>' . "\n";
}, 1);
