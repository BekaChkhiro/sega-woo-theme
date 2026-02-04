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
    if (is_shop() && isset($_GET['product_cat']) && ! empty($_GET['product_cat'])) {
        $category_slugs = array_map('sanitize_title', explode(',', wc_clean(wp_unslash($_GET['product_cat']))));

        if (! empty($category_slugs)) {
            $tax_query = $query->get('tax_query') ?: [];
            $tax_query[] = [
                'taxonomy' => 'product_cat',
                'field'    => 'slug',
                'terms'    => $category_slugs,
                'operator' => 'IN',
            ];
            $query->set('tax_query', $tax_query);
        }
    }
});

/**
 * WooCommerce Cart Fragments
 *
 * Update the mini-cart via AJAX when items are added/removed from cart.
 */
add_filter('woocommerce_add_to_cart_fragments', function ($fragments) {
    if (! function_exists('WC') || ! WC()->cart) {
        return $fragments;
    }

    $item_count = WC()->cart->get_cart_contents_count();
    $subtotal = WC()->cart->get_cart_subtotal();

    // Update the cart count badge
    $fragments['.mini-cart-count'] = sprintf(
        '<span class="mini-cart-count absolute -right-1 -top-1 flex h-5 w-5 items-center justify-center rounded-full bg-primary-600 text-xs font-medium text-white transition-transform %s">%s</span>',
        $item_count === 0 ? 'scale-0' : 'scale-100',
        $item_count > 99 ? '99+' : $item_count
    );

    // Update the subtotal
    $fragments['.mini-cart-subtotal'] = sprintf(
        '<span class="mini-cart-subtotal text-base font-semibold text-secondary-900">%s</span>',
        $subtotal
    );

    // Update the mini cart items list
    ob_start();
    if (WC()->cart->is_empty()) {
        ?>
        <div class="mini-cart-items max-h-80 overflow-y-auto">
            <div class="flex flex-col items-center justify-center py-8 text-center">
                <svg class="mb-3 h-12 w-12 text-secondary-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" />
                </svg>
                <p class="text-sm text-secondary-500"><?php esc_html_e('Your cart is empty', 'sage'); ?></p>
                <a
                    href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>"
                    class="mt-3 text-sm font-medium text-primary-600 hover:text-primary-700"
                >
                    <?php esc_html_e('Continue Shopping', 'sage'); ?> &rarr;
                </a>
            </div>
        </div>
        <?php
    } else {
        ?>
        <ul class="mini-cart-items divide-y divide-secondary-100 px-4 max-h-80 overflow-y-auto">
            <?php foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) :
                $product = $cart_item['data'];
                if (! $product || ! $product->exists()) {
                    continue;
                }
                ?>
                <li class="mini-cart-item flex gap-3 py-3" data-key="<?php echo esc_attr($cart_item_key); ?>">
                    <a href="<?php echo esc_url($product->get_permalink()); ?>" class="flex-shrink-0">
                        <div class="h-16 w-16 overflow-hidden rounded-md bg-secondary-100">
                            <?php echo $product->get_image('woocommerce_gallery_thumbnail'); ?>
                        </div>
                    </a>
                    <div class="flex flex-1 flex-col">
                        <div class="flex justify-between">
                            <a
                                href="<?php echo esc_url($product->get_permalink()); ?>"
                                class="text-sm font-medium text-secondary-900 hover:text-primary-600 line-clamp-2"
                            >
                                <?php echo esc_html($product->get_name()); ?>
                            </a>
                            <button
                                type="button"
                                class="remove-from-cart ml-2 flex-shrink-0 text-secondary-400 hover:text-red-500 transition-colors"
                                data-cart-item-key="<?php echo esc_attr($cart_item_key); ?>"
                                aria-label="<?php esc_attr_e('Remove item', 'sage'); ?>"
                            >
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        <div class="mt-1 flex items-center justify-between text-sm">
                            <span class="text-secondary-500">
                                <?php esc_html_e('Qty:', 'sage'); ?> <?php echo esc_html($cart_item['quantity']); ?>
                            </span>
                            <span class="font-medium text-secondary-900">
                                <?php echo WC()->cart->get_product_subtotal($product, $cart_item['quantity']); ?>
                            </span>
                        </div>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
        <?php
    }
    $fragments['.mini-cart-items'] = ob_get_clean();

    // Update the mini cart footer (show/hide based on cart state)
    $cart_url = wc_get_cart_url();
    $checkout_url = wc_get_checkout_url();

    ob_start();
    if (WC()->cart->is_empty()) {
        ?>
        <div class="mini-cart-footer hidden">
            <div class="border-t border-secondary-200 bg-secondary-50 px-4 py-4">
                <div class="mb-4 flex items-center justify-between">
                    <span class="text-sm font-medium text-secondary-900"><?php esc_html_e('Subtotal', 'sage'); ?></span>
                    <span class="mini-cart-subtotal text-base font-semibold text-secondary-900"><?php echo $subtotal; ?></span>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <a href="<?php echo esc_url($cart_url); ?>" class="inline-flex items-center justify-center rounded-md border border-secondary-300 bg-white px-4 py-2 text-sm font-medium text-secondary-700 shadow-sm transition-colors hover:bg-secondary-50 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                        <?php esc_html_e('View Cart', 'sage'); ?>
                    </a>
                    <a href="<?php echo esc_url($checkout_url); ?>" class="inline-flex items-center justify-center rounded-md border border-transparent bg-primary-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition-colors hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                        <?php esc_html_e('Checkout', 'sage'); ?>
                    </a>
                </div>
            </div>
        </div>
        <?php
    } else {
        ?>
        <div class="mini-cart-footer">
            <div class="border-t border-secondary-200 bg-secondary-50 px-4 py-4">
                <div class="mb-4 flex items-center justify-between">
                    <span class="text-sm font-medium text-secondary-900"><?php esc_html_e('Subtotal', 'sage'); ?></span>
                    <span class="mini-cart-subtotal text-base font-semibold text-secondary-900"><?php echo $subtotal; ?></span>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <a href="<?php echo esc_url($cart_url); ?>" class="inline-flex items-center justify-center rounded-md border border-secondary-300 bg-white px-4 py-2 text-sm font-medium text-secondary-700 shadow-sm transition-colors hover:bg-secondary-50 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                        <?php esc_html_e('View Cart', 'sage'); ?>
                    </a>
                    <a href="<?php echo esc_url($checkout_url); ?>" class="inline-flex items-center justify-center rounded-md border border-transparent bg-primary-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition-colors hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                        <?php esc_html_e('Checkout', 'sage'); ?>
                    </a>
                </div>
            </div>
        </div>
        <?php
    }
    $fragments['.mini-cart-footer'] = ob_get_clean();

    // Update cart page totals if on cart page
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
    // Debug logging (can be removed in production)
    error_log('Cart AJAX: update_cart_item_qty called');
    error_log('Cart AJAX POST data: ' . print_r($_POST, true));

    $cart_item_key = isset($_POST['cart_item_key']) ? sanitize_text_field(wp_unslash($_POST['cart_item_key'])) : '';
    $quantity = isset($_POST['quantity']) ? absint($_POST['quantity']) : 1;

    if (! $cart_item_key) {
        error_log('Cart AJAX: Invalid cart item key');
        wp_send_json_error(['message' => __('Invalid cart item.', 'sage')]);
    }

    error_log('Cart AJAX: Processing cart_item_key=' . $cart_item_key . ', quantity=' . $quantity);

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
        '<span class="mini-cart-subtotal text-base font-semibold text-secondary-900">%s</span>',
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

    error_log('Cart AJAX: Success - sending response');
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
 * Enqueues necessary WooCommerce scripts on the cart page.
 */
add_action('wp_enqueue_scripts', function () {
    if (is_cart()) {
        // Ensure cart and cart fragments scripts are loaded
        wp_enqueue_script('wc-cart');
        wp_enqueue_script('wc-cart-fragments');

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
}, 20);
