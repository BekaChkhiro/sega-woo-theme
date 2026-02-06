<?php

/**
 * Theme setup.
 */

namespace App;

use Illuminate\Support\Facades\Vite;

/**
 * Inject styles into the block editor.
 *
 * @return array
 */
add_filter('block_editor_settings_all', function ($settings) {
    $style = Vite::asset('resources/css/editor.css');

    $settings['styles'][] = [
        'css' => "@import url('{$style}')",
    ];

    return $settings;
});

/**
 * Inject scripts into the block editor.
 *
 * @return void
 */
add_filter('admin_head', function () {
    if (! get_current_screen()?->is_block_editor()) {
        return;
    }

    $dependencies = json_decode(Vite::content('editor.deps.json'));

    foreach ($dependencies as $dependency) {
        if (! wp_script_is($dependency)) {
            wp_enqueue_script($dependency);
        }
    }

    echo Vite::withEntryPoints([
        'resources/js/editor.js',
    ])->toHtml();
});

/**
 * Use the generated theme.json file.
 *
 * @return string
 */
add_filter('theme_file_path', function ($path, $file) {
    return $file === 'theme.json'
        ? public_path('build/assets/theme.json')
        : $path;
}, 10, 2);

/**
 * Register the initial theme setup.
 *
 * @return void
 */
add_action('after_setup_theme', function () {
    /**
     * Disable full-site editing support.
     *
     * @link https://wptavern.com/gutenberg-10-5-embeds-pdfs-adds-verse-block-color-options-and-introduces-new-patterns
     */
    remove_theme_support('block-templates');

    /**
     * Register the navigation menus.
     *
     * @link https://developer.wordpress.org/reference/functions/register_nav_menus/
     */
    register_nav_menus([
        'primary_navigation' => __('Primary Navigation', 'sage'),
        'footer_navigation' => __('Footer Navigation', 'sage'),
        'mega_menu' => __('Mega Menu (Homepage)', 'sage'),
    ]);

    /**
     * Disable the default block patterns.
     *
     * @link https://developer.wordpress.org/block-editor/developers/themes/theme-support/#disabling-the-default-block-patterns
     */
    remove_theme_support('core-block-patterns');

    /**
     * Enable plugins to manage the document title.
     *
     * @link https://developer.wordpress.org/reference/functions/add_theme_support/#title-tag
     */
    add_theme_support('title-tag');

    /**
     * Enable post thumbnail support.
     *
     * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
     */
    add_theme_support('post-thumbnails');

    /**
     * Enable responsive embed support.
     *
     * @link https://developer.wordpress.org/block-editor/how-to-guides/themes/theme-support/#responsive-embedded-content
     */
    add_theme_support('responsive-embeds');

    /**
     * Enable HTML5 markup support.
     *
     * @link https://developer.wordpress.org/reference/functions/add_theme_support/#html5
     */
    add_theme_support('html5', [
        'caption',
        'comment-form',
        'comment-list',
        'gallery',
        'search-form',
        'script',
        'style',
    ]);

    /**
     * Enable selective refresh for widgets in customizer.
     *
     * @link https://developer.wordpress.org/reference/functions/add_theme_support/#customize-selective-refresh-widgets
     */
    add_theme_support('customize-selective-refresh-widgets');

    /**
     * Enable WooCommerce theme support.
     *
     * @link https://woocommerce.com/document/woocommerce-theme-developer-handbook/
     */
    add_theme_support('woocommerce', [
        'thumbnail_image_width' => 300,
        'single_image_width' => 600,
        'product_grid' => [
            'default_rows' => 3,
            'min_rows' => 1,
            'max_rows' => 8,
            'default_columns' => 4,
            'min_columns' => 1,
            'max_columns' => 6,
        ],
    ]);

    /**
     * Enable WooCommerce product gallery features.
     *
     * We use a custom Alpine.js lightbox instead of PhotoSwipe,
     * so we only enable zoom (disabled - we have custom zoom).
     * The slider is also disabled since we have custom navigation.
     *
     * @link https://woocommerce.com/document/woocommerce-theme-developer-handbook/#product-gallery-features
     */
    // Disabled - using custom Alpine.js gallery with built-in lightbox
    // add_theme_support('wc-product-gallery-zoom');
    // add_theme_support('wc-product-gallery-lightbox');
    // add_theme_support('wc-product-gallery-slider');
}, 20);

/**
 * Conditional Script Loading for WooCommerce.
 *
 * Optimizes performance by only loading scripts on pages that need them.
 * This reduces JavaScript payload and improves page load times.
 *
 * @return void
 */
add_action('wp_enqueue_scripts', function () {
    if (! function_exists('WC')) {
        return;
    }

    // Determine page context
    $is_shop_page = is_shop() || is_product_category() || is_product_tag() || is_product_taxonomy();
    $is_product_page = is_product();
    $is_cart_page = is_cart();
    $is_checkout_page = is_checkout();
    $is_account_page = is_account_page();
    $is_front_page = is_front_page(); // Homepage with product carousels needs WC scripts
    $is_woocommerce_page = $is_shop_page || $is_product_page || $is_cart_page || $is_checkout_page || $is_account_page || $is_front_page;

    /**
     * Cart Fragments - Only needed for mini-cart AJAX updates.
     * Load on: Shop and Product pages only.
     *
     * Optimizations:
     * - Not loaded on Cart/Checkout (full cart UI handles updates)
     * - Not loaded on Account sub-pages (orders, downloads, edit-account)
     *   where cart changes are unlikely and fragments add overhead
     * - Only loaded where add-to-cart actions are likely to occur
     */
    $needs_cart_fragments = false;

    if ($is_shop_page || $is_product_page) {
        // Shop and product pages need cart fragments for add-to-cart updates
        $needs_cart_fragments = true;
    } elseif ($is_account_page) {
        // Only load on account dashboard, not on sub-pages
        // Sub-pages like orders, downloads, addresses don't need cart fragments
        global $wp;
        $account_subpages = ['orders', 'downloads', 'edit-account', 'edit-address', 'payment-methods', 'view-order'];
        $is_account_subpage = false;

        foreach ($account_subpages as $subpage) {
            if (isset($wp->query_vars[$subpage]) || strpos($_SERVER['REQUEST_URI'], '/' . $subpage) !== false) {
                $is_account_subpage = true;
                break;
            }
        }

        // Only load cart fragments on account dashboard (not sub-pages)
        if (! $is_account_subpage) {
            $needs_cart_fragments = true;
        }
    }

    if ($needs_cart_fragments) {
        wp_enqueue_script('wc-cart-fragments');
    }

    /**
     * Add-to-Cart Script - Only needed where products can be added to cart.
     * Load on: Shop (archive) pages and Single Product pages
     */
    if ($is_shop_page || $is_product_page) {
        wp_enqueue_script('wc-add-to-cart');
    }

    /**
     * Single Add-to-Cart Variation - Only on single product pages with variations.
     * This handles variation selection and price updates.
     */
    if ($is_product_page) {
        global $product;
        if (! $product) {
            $product = wc_get_product(get_the_ID());
        }
        if ($product && $product->is_type('variable')) {
            wp_enqueue_script('wc-add-to-cart-variation');
        }
    }

    /**
     * Product Gallery Scripts - Only on single product pages.
     * Includes: Flexslider, PhotoSwipe (lightbox), Zoom
     */
    if ($is_product_page) {
        // These are registered by WooCommerce when gallery support is declared
        wp_enqueue_script('flexslider');
        wp_enqueue_script('photoswipe');
        wp_enqueue_script('photoswipe-ui-default');
        wp_enqueue_script('zoom');
        wp_enqueue_script('wc-single-product');
    }

    /**
     * Cart Scripts - Only on cart page.
     */
    if ($is_cart_page) {
        wp_enqueue_script('wc-cart');
        wp_enqueue_script('wc-cart-fragments');
    }

    /**
     * Checkout Scripts - Only on checkout page.
     */
    if ($is_checkout_page) {
        wp_enqueue_script('wc-checkout');
        wp_enqueue_script('wc-address-i18n');
    }

    /**
     * Account Scripts - Only on account pages.
     */
    if ($is_account_page) {
        // Password strength meter on edit-account and registration
        if (is_user_logged_in() || 'yes' === get_option('woocommerce_registration_generate_password')) {
            wp_enqueue_script('wc-password-strength-meter');
        }
    }

    /**
     * Dequeue unnecessary scripts on non-WooCommerce pages.
     * This prevents loading WC scripts on blog posts, regular pages, etc.
     */
    if (! $is_woocommerce_page) {
        // Remove WooCommerce scripts from non-shop pages
        wp_dequeue_script('wc-add-to-cart');
        wp_dequeue_script('wc-cart-fragments');
        wp_dequeue_script('wc-add-to-cart-variation');
        wp_dequeue_script('wc-single-product');
        wp_dequeue_script('wc-cart');
        wp_dequeue_script('wc-checkout');
        wp_dequeue_script('wc-address-i18n');
        wp_dequeue_script('wc-password-strength-meter');
        wp_dequeue_script('flexslider');
        wp_dequeue_script('photoswipe');
        wp_dequeue_script('photoswipe-ui-default');
        wp_dequeue_script('zoom');
        wp_dequeue_script('select2');
        wp_dequeue_script('selectWoo');

        // Also dequeue their styles
        wp_dequeue_style('select2');
        wp_dequeue_style('photoswipe');
        wp_dequeue_style('photoswipe-default-skin');
    }
}, 20);

/**
 * Remove default WooCommerce styles.
 *
 * Since we're using Tailwind CSS for all styling, we dequeue
 * WooCommerce's built-in stylesheets to prevent conflicts and
 * reduce unnecessary CSS loading.
 *
 * @return void
 */
add_action('wp_enqueue_scripts', function () {
    // Remove core WooCommerce stylesheets
    wp_dequeue_style('woocommerce-general');
    wp_dequeue_style('woocommerce-layout');
    wp_dequeue_style('woocommerce-smallscreen');

    // Remove WooCommerce inline styles
    wp_dequeue_style('woocommerce-inline');

    // Remove WooCommerce block styles (if using Gutenberg blocks)
    wp_dequeue_style('wc-blocks-style');
    wp_dequeue_style('wc-blocks-vendors-style');

    // Remove Select2 styles (we use custom dropdowns)
    wp_dequeue_style('select2');

    // Remove PhotoSwipe styles (we have custom lightbox)
    wp_dequeue_style('photoswipe');
    wp_dequeue_style('photoswipe-default-skin');

    // Remove PhotoSwipe scripts
    wp_dequeue_script('photoswipe');
    wp_dequeue_script('photoswipe-ui-default');
}, 100); // High priority to run after WooCommerce enqueues its styles

/**
 * Remove PhotoSwipe HTML template from footer.
 *
 * Since we use a custom Alpine.js lightbox, we don't need the PhotoSwipe
 * HTML dialog that WooCommerce adds to the footer.
 */
add_action('init', function () {
    // Remove PhotoSwipe template from footer (priority 15 is WooCommerce default)
    remove_action('wp_footer', 'woocommerce_photoswipe', 15);
}, 20);

/**
 * Optionally disable WooCommerce stylesheets entirely.
 *
 * This is a more aggressive approach that prevents WooCommerce
 * from even registering its stylesheets.
 *
 * @param array $enqueue_styles Array of styles to enqueue.
 * @return array Empty array to disable all WooCommerce styles.
 */
add_filter('woocommerce_enqueue_styles', '__return_empty_array');

/**
 * Disable Select2 on WooCommerce pages.
 *
 * Since we use custom Tailwind-styled dropdowns, we don't need Select2.
 * This saves ~20KB of JavaScript and ~5KB of CSS.
 *
 * @return void
 */
add_action('wp_enqueue_scripts', function () {
    // Dequeue Select2 assets (we use custom dropdowns)
    wp_dequeue_script('selectWoo');
    wp_dequeue_script('select2');
    wp_dequeue_style('select2');
}, 100);

/**
 * Disable WooCommerce Blocks scripts on non-block pages.
 *
 * WooCommerce Blocks loads significant JavaScript even when not using blocks.
 * We disable them since our theme uses Blade templates instead of blocks.
 *
 * @return void
 */
add_action('wp_enqueue_scripts', function () {
    // Remove WooCommerce Blocks scripts (we don't use Gutenberg blocks for WooCommerce)
    wp_dequeue_script('wc-blocks-middleware');
    wp_dequeue_script('wc-blocks-data-store');
    wp_dequeue_script('wc-blocks-vendors');
    wp_dequeue_script('wc-blocks');
    wp_dequeue_script('wc-blocks-registry');
    wp_dequeue_script('wc-settings');
    wp_dequeue_script('wc-blocks-checkout');
    wp_dequeue_script('wc-blocks-cart');

    // Remove WooCommerce Blocks styles
    wp_dequeue_style('wc-blocks-style');
    wp_dequeue_style('wc-blocks-vendors-style');
    wp_dequeue_style('wc-blocks-integration');
}, 100);

/**
 * Disable password strength meter on non-account pages.
 *
 * The password strength meter pulls in wp-password-strength-meter
 * and zxcvbn (~800KB uncompressed). Only load where needed.
 *
 * @return void
 */
add_action('wp_enqueue_scripts', function () {
    if (! is_account_page()) {
        wp_dequeue_script('wc-password-strength-meter');
        wp_dequeue_script('password-strength-meter');
        wp_dequeue_script('zxcvbn-async');
    }
}, 100);

/**
 * Remove WooCommerce generator meta tag.
 *
 * Removes the WooCommerce version from the page source for security.
 */
remove_action('wp_head', 'wc_generator_tag');

/**
 * Add defer attribute to non-critical WooCommerce scripts.
 *
 * This allows the page to render before these scripts execute,
 * improving perceived performance and LCP scores.
 *
 * @param string $tag    The script tag.
 * @param string $handle The script handle.
 * @param string $src    The script source URL.
 * @return string Modified script tag.
 */
add_filter('script_loader_tag', function ($tag, $handle, $src) {
    // Scripts that can be deferred (non-blocking)
    $defer_scripts = [
        'wc-cart-fragments',
        'wc-add-to-cart',
        'flexslider',
        'photoswipe',
        'photoswipe-ui-default',
        'zoom',
    ];

    if (in_array($handle, $defer_scripts, true)) {
        // Don't add defer if already present
        if (strpos($tag, 'defer') === false) {
            $tag = str_replace(' src=', ' defer src=', $tag);
        }
    }

    return $tag;
}, 10, 3);

/**
 * Cart Fragment Optimization Settings
 *
 * Configure WooCommerce cart fragments for optimal performance:
 * - Increase refresh timeout to reduce AJAX calls
 * - Add debouncing to prevent rapid successive refreshes
 * - Use cart hash comparison to skip unnecessary updates
 */
add_action('wp_enqueue_scripts', function () {
    if (! function_exists('WC') || ! wp_script_is('wc-cart-fragments', 'enqueued')) {
        return;
    }

    // Override cart fragments settings for better performance
    wp_add_inline_script('wc-cart-fragments', '
        (function() {
            // Extend cart fragment refresh timeout (default is 0, meaning immediate)
            // Setting to 1000ms debounces rapid cart changes
            if (typeof wc_cart_fragments_params !== "undefined") {
                wc_cart_fragments_params.cart_hash_refresh_timeout = 1000;
            }

            // Debounce cart fragment refresh to prevent excessive AJAX calls
            var originalRefresh = null;
            var refreshTimeout = null;
            var DEBOUNCE_DELAY = 300; // 300ms debounce

            jQuery(document).ready(function($) {
                // Store reference to original refresh method if it exists
                if (typeof $.fn.wc_cart_fragment !== "undefined") {
                    return; // Already initialized
                }

                // Intercept fragment refresh events
                var pendingRefresh = false;

                $(document.body).on("wc_fragment_refresh", function(e) {
                    if (pendingRefresh) {
                        e.stopImmediatePropagation();
                        return false;
                    }

                    pendingRefresh = true;

                    clearTimeout(refreshTimeout);
                    refreshTimeout = setTimeout(function() {
                        pendingRefresh = false;
                    }, DEBOUNCE_DELAY);
                });

                // Skip fragment refresh if cart hash matches (already up to date)
                $(document.body).on("wc_fragments_refreshed", function() {
                    var currentHash = sessionStorage.getItem("wc_cart_hash");
                    var newHash = wc_cart_fragments_params.cart_hash;

                    if (currentHash === newHash) {
                        // Cart unchanged, skip DOM updates
                        return;
                    }

                    sessionStorage.setItem("wc_cart_hash", newHash);
                });
            });
        })();
    ', 'after');
}, 25);

/**
 * Disable cart fragment refresh on specific pages.
 *
 * WooCommerce refreshes cart fragments on every page load by default.
 * This filter allows disabling refresh on pages where it's not needed.
 *
 * @param array $params Cart fragment params.
 * @return array Modified params.
 */
add_filter('woocommerce_get_script_data', function ($params, $handle) {
    if ($handle !== 'wc-cart-fragments') {
        return $params;
    }

    // On checkout page, disable fragment refresh (checkout handles its own cart state)
    if (function_exists('is_checkout') && is_checkout()) {
        $params['refresh_on_load'] = false;
    }

    return $params;
}, 10, 2);

/**
 * Register the theme sidebars.
 *
 * @return void
 */
add_action('widgets_init', function () {
    $config = [
        'before_widget' => '<section class="widget %1$s %2$s">',
        'after_widget' => '</section>',
        'before_title' => '<h3>',
        'after_title' => '</h3>',
    ];

    register_sidebar([
        'name' => __('Primary', 'sage'),
        'id' => 'sidebar-primary',
    ] + $config);

    register_sidebar([
        'name' => __('Footer', 'sage'),
        'id' => 'sidebar-footer',
    ] + $config);

    /**
     * Shop Sidebar for WooCommerce product filtering.
     */
    register_sidebar([
        'name' => __('Shop Sidebar', 'sage'),
        'id' => 'sidebar-shop',
        'description' => __('Sidebar displayed on WooCommerce shop and archive pages.', 'sage'),
        'before_widget' => '<div class="widget mb-6 p-4 bg-white rounded-lg shadow-sm %1$s %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="text-lg font-semibold text-gray-900 mb-3 pb-2 border-b border-gray-200">',
        'after_title' => '</h3>',
    ]);
});

/**
 * Register Customizer settings.
 *
 * Adds WooCommerce checkout field customization options to the WordPress Customizer.
 *
 * @return void
 */
add_action('customize_register', function ($wp_customize) {
    // Only load if WooCommerce is active
    if (!function_exists('WC')) {
        return;
    }

    // Register Homepage Slider Customizer
    $homepage_slider = new \App\Customizer\HomepageSlider();
    $homepage_slider->register($wp_customize);

    // Register Checkout Fields Customizer
    $checkout_fields = new \App\Customizer\CheckoutFields();
    $checkout_fields->register($wp_customize);

    // Register Attribute Swatches Customizer
    $attribute_swatches = new \App\Customizer\AttributeSwatches();
    $attribute_swatches->register($wp_customize);
});

/**
 * Initialize Attribute Swatches Admin UI.
 *
 * Adds display type selection to WooCommerce attribute settings
 * and color picker fields to attribute terms.
 *
 * @return void
 */
add_action('init', function () {
    // Only load in admin and if WooCommerce is active
    if (!is_admin() || !function_exists('WC')) {
        return;
    }

    $admin = new \App\Admin\AttributeSwatchesAdmin();
    $admin->init();
}, 20);
