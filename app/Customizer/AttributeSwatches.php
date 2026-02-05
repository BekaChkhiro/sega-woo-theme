<?php

/**
 * Attribute Swatches Customizer
 *
 * Adds WordPress Customizer controls for configuring WooCommerce product attribute
 * display types: select dropdown, button style, or color swatches.
 *
 * @package Sage
 */

namespace App\Customizer;

use WP_Customize_Manager;
use WP_Customize_Control;

/**
 * Custom control for color picker with hex input.
 */
class Attribute_Color_Control extends WP_Customize_Control
{
    public $type = 'attribute_color';
    public $term_slug = '';
    public $term_name = '';

    public function render_content()
    {
        ?>
        <div class="attribute-color-control" data-term="<?php echo esc_attr($this->term_slug); ?>">
            <span class="customize-control-title"><?php echo esc_html($this->term_name); ?></span>
            <div class="color-input-wrapper">
                <input
                    type="color"
                    value="<?php echo esc_attr($this->value() ?: '#808080'); ?>"
                    class="color-picker-input"
                    data-customize-setting-link="<?php echo esc_attr($this->setting->id); ?>"
                />
                <input
                    type="text"
                    value="<?php echo esc_attr($this->value() ?: '#808080'); ?>"
                    class="color-hex-input"
                    placeholder="#000000"
                    pattern="^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$"
                    data-customize-setting-link="<?php echo esc_attr($this->setting->id); ?>"
                />
            </div>
        </div>
        <?php
    }
}

/**
 * Custom control for the attribute swatches manager.
 */
class Attribute_Swatches_Manager_Control extends WP_Customize_Control
{
    public $type = 'attribute_swatches_manager';
    public $attributes = [];

    public function render_content()
    {
        ?>
        <div class="attribute-swatches-manager">
            <div class="attributes-intro">
                <p><?php _e('Configure how product attributes are displayed on the product page. Choose from dropdown, buttons, or color swatches.', 'sage'); ?></p>
            </div>

            <?php if (empty($this->attributes)): ?>
                <div class="no-attributes-notice">
                    <p><?php _e('No product attributes found. Create attributes in Products > Attributes first.', 'sage'); ?></p>
                </div>
            <?php else: ?>
                <div class="attributes-list">
                    <?php foreach ($this->attributes as $attribute): ?>
                        <div class="attribute-card" data-attribute="<?php echo esc_attr($attribute['slug']); ?>">
                            <div class="attribute-header">
                                <span class="attribute-name"><?php echo esc_html($attribute['label']); ?></span>
                                <span class="attribute-slug"><?php echo esc_html($attribute['slug']); ?></span>
                            </div>
                            <div class="attribute-controls">
                                <label class="display-type-label">
                                    <?php _e('Display Type', 'sage'); ?>
                                </label>
                                <div class="display-type-buttons" data-attribute="<?php echo esc_attr($attribute['slug']); ?>">
                                    <button type="button" class="type-btn" data-type="select" title="<?php esc_attr_e('Dropdown Select', 'sage'); ?>">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <rect x="3" y="5" width="18" height="14" rx="2"/>
                                            <polyline points="8 12 12 16 16 12"/>
                                        </svg>
                                        <span><?php _e('Select', 'sage'); ?></span>
                                    </button>
                                    <button type="button" class="type-btn" data-type="button" title="<?php esc_attr_e('Button Style', 'sage'); ?>">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <rect x="3" y="8" width="18" height="8" rx="2"/>
                                            <line x1="8" y1="12" x2="16" y2="12"/>
                                        </svg>
                                        <span><?php _e('Button', 'sage'); ?></span>
                                    </button>
                                    <button type="button" class="type-btn" data-type="color" title="<?php esc_attr_e('Color Swatch', 'sage'); ?>">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <circle cx="12" cy="12" r="8"/>
                                            <circle cx="12" cy="12" r="3" fill="currentColor"/>
                                        </svg>
                                        <span><?php _e('Color', 'sage'); ?></span>
                                    </button>
                                </div>
                            </div>
                            <div class="color-terms-section" style="display: none;">
                                <label class="terms-label"><?php _e('Term Colors', 'sage'); ?></label>
                                <div class="color-terms-list">
                                    <?php foreach ($attribute['terms'] as $term): ?>
                                        <div class="term-color-row" data-term="<?php echo esc_attr($term['slug']); ?>">
                                            <span class="term-name"><?php echo esc_html($term['name']); ?></span>
                                            <div class="term-color-input">
                                                <input
                                                    type="color"
                                                    class="term-color-picker"
                                                    value="#808080"
                                                    data-attribute="<?php echo esc_attr($attribute['slug']); ?>"
                                                    data-term="<?php echo esc_attr($term['slug']); ?>"
                                                />
                                                <input
                                                    type="text"
                                                    class="term-hex-input"
                                                    value="#808080"
                                                    placeholder="#000000"
                                                    data-attribute="<?php echo esc_attr($attribute['slug']); ?>"
                                                    data-term="<?php echo esc_attr($term['slug']); ?>"
                                                />
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <script type="application/json" class="attributes-data">
                <?php echo json_encode($this->attributes); ?>
            </script>
        </div>
        <?php
    }
}

class AttributeSwatches
{
    /**
     * Register Customizer settings and controls.
     */
    public function register(WP_Customize_Manager $wp_customize): void
    {
        add_action('customize_controls_print_styles', [$this, 'customizer_styles']);
        add_action('customize_controls_print_footer_scripts', [$this, 'customizer_scripts']);

        // Ensure WooCommerce panel exists
        if (!$wp_customize->get_panel('woocommerce_panel')) {
            $wp_customize->add_panel('woocommerce_panel', [
                'title'       => __('WooCommerce', 'sage'),
                'description' => __('Customize your store settings.', 'sage'),
                'priority'    => 150,
            ]);
        }

        $this->register_swatches_section($wp_customize);
    }

    /**
     * Register the attribute swatches section.
     */
    protected function register_swatches_section(WP_Customize_Manager $wp_customize): void
    {
        $wp_customize->add_section('attribute_swatches', [
            'title'       => __('Product Attribute Swatches', 'sage'),
            'description' => $this->get_section_intro(),
            'panel'       => 'woocommerce_panel',
            'priority'    => 40,
        ]);

        $attributes = $this->get_product_attributes();

        // Register settings for each attribute
        foreach ($attributes as $attribute) {
            $this->register_attribute_settings($wp_customize, $attribute);
        }

        // Add the visual manager control
        $wp_customize->add_setting('attribute_swatches_manager', [
            'sanitize_callback' => 'sanitize_text_field',
        ]);

        $wp_customize->add_control(new Attribute_Swatches_Manager_Control(
            $wp_customize,
            'attribute_swatches_manager',
            [
                'section'    => 'attribute_swatches',
                'priority'   => 1,
                'attributes' => $attributes,
            ]
        ));
    }

    /**
     * Register settings for an attribute.
     */
    protected function register_attribute_settings(WP_Customize_Manager $wp_customize, array $attribute): void
    {
        $slug = $attribute['slug'];

        // Display type setting - use 'option' type to save in wp_options table
        $wp_customize->add_setting("sega_attribute_{$slug}_type", [
            'type'              => 'option',
            'default'           => 'select',
            'transport'         => 'refresh',
            'sanitize_callback' => [$this, 'sanitize_display_type'],
        ]);

        // Color settings for each term - use 'option' type to save in wp_options table
        foreach ($attribute['terms'] as $term) {
            $term_slug = $term['slug'];
            $wp_customize->add_setting("sega_attribute_{$slug}_{$term_slug}_color", [
                'type'              => 'option',
                'default'           => '#808080',
                'transport'         => 'refresh',
                'sanitize_callback' => 'sanitize_hex_color',
            ]);
        }
    }

    /**
     * Get section intro HTML.
     */
    protected function get_section_intro(): string
    {
        return '<div class="swatches-section-intro">' .
            __('Configure display types for product variation attributes.', 'sage') .
            '</div>';
    }

    /**
     * Get all product attributes with their terms.
     */
    protected function get_product_attributes(): array
    {
        $attributes = [];
        $taxonomies = wc_get_attribute_taxonomies();

        foreach ($taxonomies as $taxonomy) {
            $taxonomy_name = wc_attribute_taxonomy_name($taxonomy->attribute_name);
            $terms = get_terms([
                'taxonomy'   => $taxonomy_name,
                'hide_empty' => false,
            ]);

            $term_data = [];
            if (!is_wp_error($terms)) {
                foreach ($terms as $term) {
                    $term_data[] = [
                        'id'   => $term->term_id,
                        'name' => $term->name,
                        'slug' => $term->slug,
                    ];
                }
            }

            $attributes[] = [
                'id'     => $taxonomy->attribute_id,
                'slug'   => $taxonomy->attribute_name,
                'label'  => $taxonomy->attribute_label,
                'name'   => $taxonomy_name,
                'terms'  => $term_data,
            ];
        }

        return $attributes;
    }

    /**
     * Sanitize display type.
     */
    public function sanitize_display_type($value): string
    {
        $valid = ['select', 'button', 'color'];
        return in_array($value, $valid, true) ? $value : 'select';
    }

    /**
     * Output Customizer CSS.
     */
    public function customizer_styles(): void
    {
        ?>
        <style>
            /* Section intro */
            .swatches-section-intro {
                background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
                color: #fff;
                padding: 12px 15px;
                border-radius: 8px;
                margin: -12px -12px 15px -12px;
                font-size: 12px;
                line-height: 1.5;
            }

            /* Manager container */
            .attribute-swatches-manager {
                margin: 0 -12px;
            }

            .attributes-intro {
                padding: 0 12px 15px;
                font-size: 12px;
                color: #6b7280;
                line-height: 1.5;
            }

            .no-attributes-notice {
                padding: 20px 12px;
                text-align: center;
                color: #9ca3af;
                font-size: 13px;
                background: #f9fafb;
                border-radius: 8px;
                margin: 0 12px;
            }

            /* Attribute card */
            .attribute-card {
                background: #fff;
                border: 1px solid #e5e7eb;
                border-radius: 10px;
                margin: 0 12px 12px;
                overflow: hidden;
                transition: all 0.2s ease;
            }

            .attribute-card:hover {
                border-color: #c7d2fe;
                box-shadow: 0 2px 8px rgba(99, 102, 241, 0.1);
            }

            .attribute-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 12px 14px;
                background: linear-gradient(to right, #f8fafc, #f1f5f9);
                border-bottom: 1px solid #e5e7eb;
            }

            .attribute-name {
                font-weight: 600;
                font-size: 13px;
                color: #1f2937;
            }

            .attribute-slug {
                font-size: 11px;
                color: #9ca3af;
                background: #f3f4f6;
                padding: 2px 8px;
                border-radius: 4px;
                font-family: monospace;
            }

            .attribute-controls {
                padding: 14px;
            }

            .display-type-label,
            .terms-label {
                display: block;
                font-size: 11px;
                font-weight: 600;
                color: #6b7280;
                margin-bottom: 8px;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }

            /* Display type buttons */
            .display-type-buttons {
                display: flex;
                gap: 6px;
            }

            .type-btn {
                flex: 1;
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 4px;
                padding: 10px 8px;
                border: 2px solid #e5e7eb;
                border-radius: 8px;
                background: #fff;
                cursor: pointer;
                transition: all 0.2s ease;
                color: #6b7280;
            }

            .type-btn:hover {
                border-color: #c7d2fe;
                background: #f5f3ff;
                color: #6366f1;
            }

            .type-btn.active {
                border-color: #6366f1;
                background: linear-gradient(135deg, #eef2ff 0%, #f5f3ff 100%);
                color: #6366f1;
            }

            .type-btn svg {
                width: 20px;
                height: 20px;
            }

            .type-btn span {
                font-size: 10px;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }

            /* Color terms section */
            .color-terms-section {
                padding: 0 14px 14px;
                border-top: 1px solid #f3f4f6;
                margin-top: 10px;
                padding-top: 14px;
            }

            .color-terms-list {
                display: flex;
                flex-direction: column;
                gap: 8px;
            }

            .term-color-row {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 8px 10px;
                background: #f9fafb;
                border-radius: 6px;
            }

            .term-name {
                font-size: 12px;
                font-weight: 500;
                color: #374151;
            }

            .term-color-input {
                display: flex;
                align-items: center;
                gap: 6px;
            }

            .term-color-picker {
                width: 32px;
                height: 32px;
                padding: 0;
                border: 2px solid #e5e7eb;
                border-radius: 6px;
                cursor: pointer;
                background: none;
            }

            .term-color-picker::-webkit-color-swatch-wrapper {
                padding: 2px;
            }

            .term-color-picker::-webkit-color-swatch {
                border-radius: 3px;
                border: none;
            }

            .term-hex-input {
                width: 80px;
                padding: 6px 8px;
                border: 1px solid #e5e7eb;
                border-radius: 4px;
                font-size: 12px;
                font-family: monospace;
                text-transform: uppercase;
            }

            .term-hex-input:focus {
                border-color: #6366f1;
                outline: none;
                box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.1);
            }

            /* Hide default control elements */
            #customize-control-attribute_swatches_manager .customize-control-title,
            #customize-control-attribute_swatches_manager .customize-control-description {
                display: none;
            }
        </style>
        <?php
    }

    /**
     * Output Customizer JavaScript.
     */
    public function customizer_scripts(): void
    {
        ?>
        <script>
        (function($) {
            'use strict';

            wp.customize.bind('ready', function() {
                initAttributeSwatches();
            });

            function initAttributeSwatches() {
                const $manager = $('.attribute-swatches-manager');
                if (!$manager.length) return;

                // Initialize display type buttons
                $manager.find('.display-type-buttons').each(function() {
                    const $btnGroup = $(this);
                    const attrSlug = $btnGroup.data('attribute');
                    const settingKey = 'sega_attribute_' + attrSlug + '_type';

                    // Get current value from customizer
                    const currentValue = wp.customize(settingKey)?.get() || 'select';

                    // Set active state
                    $btnGroup.find('.type-btn').removeClass('active');
                    $btnGroup.find('.type-btn[data-type="' + currentValue + '"]').addClass('active');

                    // Show/hide color section
                    const $card = $btnGroup.closest('.attribute-card');
                    if (currentValue === 'color') {
                        $card.find('.color-terms-section').show();
                    }

                    // Button click handler
                    $btnGroup.on('click', '.type-btn', function() {
                        const $btn = $(this);
                        const type = $btn.data('type');

                        // Update active state
                        $btnGroup.find('.type-btn').removeClass('active');
                        $btn.addClass('active');

                        // Update customizer setting
                        wp.customize(settingKey).set(type);

                        // Show/hide color section
                        if (type === 'color') {
                            $card.find('.color-terms-section').slideDown(200);
                        } else {
                            $card.find('.color-terms-section').slideUp(200);
                        }
                    });
                });

                // Initialize color inputs
                $manager.find('.term-color-row').each(function() {
                    const $row = $(this);
                    const $picker = $row.find('.term-color-picker');
                    const $hex = $row.find('.term-hex-input');
                    const attrSlug = $picker.data('attribute');
                    const termSlug = $picker.data('term');
                    const settingKey = 'sega_attribute_' + attrSlug + '_' + termSlug + '_color';

                    // Get current value
                    const currentValue = wp.customize(settingKey)?.get() || '#808080';
                    $picker.val(currentValue);
                    $hex.val(currentValue);

                    // Sync picker to hex input and customizer
                    $picker.on('input change', function() {
                        const color = $(this).val();
                        $hex.val(color);
                        wp.customize(settingKey).set(color);
                    });

                    // Sync hex input to picker and customizer
                    $hex.on('input change', function() {
                        let color = $(this).val().trim();

                        // Add # if missing
                        if (color && !color.startsWith('#')) {
                            color = '#' + color;
                        }

                        // Validate hex color
                        if (/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/.test(color)) {
                            $picker.val(color);
                            wp.customize(settingKey).set(color);
                            $hex.removeClass('invalid');
                        } else {
                            $hex.addClass('invalid');
                        }
                    });
                });
            }

        })(jQuery);
        </script>
        <?php
    }
}
