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
     * @link https://woocommerce.com/document/woocommerce-theme-developer-handbook/#product-gallery-features
     */
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');
}, 20);

/**
 * Enqueue WooCommerce cart scripts on all pages.
 *
 * This ensures the mini-cart updates via AJAX and
 * AJAX add-to-cart works on any page.
 *
 * @return void
 */
add_action('wp_enqueue_scripts', function () {
    if (function_exists('WC')) {
        // Cart fragments for mini-cart updates
        if (! is_cart() && ! is_checkout()) {
            wp_enqueue_script('wc-cart-fragments');
        }

        // Add-to-cart script for AJAX functionality
        wp_enqueue_script('wc-add-to-cart');
    }
});

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
