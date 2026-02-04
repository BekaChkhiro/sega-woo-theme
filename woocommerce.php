<?php

/**
 * WooCommerce Template
 *
 * This template is used for all WooCommerce pages (shop, product archives, etc.)
 * It renders the appropriate Blade view based on the current context.
 */

// Ensure Acorn is booted
if (! function_exists('view')) {
    return;
}

// Determine which view to render based on context
if (is_singular('product')) {
    // Single product page
    if (view()->exists('woocommerce.single-product')) {
        echo view('woocommerce.single-product')->render();
    } else {
        wc_get_template('single-product.php');
    }
} elseif (is_shop() || is_product_category() || is_product_tag() || is_product_taxonomy()) {
    // Shop and product archive pages
    if (view()->exists('woocommerce.archive-product')) {
        echo view('woocommerce.archive-product')->render();
    } else {
        wc_get_template('archive-product.php');
    }
} elseif (is_checkout()) {
    // Checkout page
    if (view()->exists('woocommerce.checkout.form-checkout')) {
        echo view('woocommerce.checkout.form-checkout')->render();
    } else {
        wc_get_template('checkout/form-checkout.php');
    }
} elseif (is_account_page()) {
    // My Account pages
    if (view()->exists('woocommerce.myaccount.my-account')) {
        echo view('woocommerce.myaccount.my-account')->render();
    } else {
        wc_get_template('myaccount/my-account.php');
    }
} else {
    // Fallback to default WooCommerce template
    woocommerce_content();
}
