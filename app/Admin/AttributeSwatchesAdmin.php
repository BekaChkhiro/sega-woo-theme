<?php

/**
 * Attribute Swatches Admin
 *
 * Adds admin UI for configuring WooCommerce product attribute display types
 * directly in the Products > Attributes screen.
 *
 * @package Sage
 */

namespace App\Admin;

/**
 * Class AttributeSwatchesAdmin
 *
 * Handles the admin interface for attribute swatches configuration.
 */
class AttributeSwatchesAdmin
{
    /**
     * Available display types for attributes.
     */
    public const DISPLAY_TYPES = [
        'select' => 'Dropdown Select',
        'button' => 'Button/Label',
        'color'  => 'Color Swatch',
    ];

    /**
     * Initialize the admin hooks.
     */
    public function init(): void
    {
        // Add display type field to attribute add/edit forms
        add_action('woocommerce_after_add_attribute_fields', [$this, 'addAttributeTypeField']);
        add_action('woocommerce_after_edit_attribute_fields', [$this, 'editAttributeTypeField']);

        // Save display type when attribute is saved
        add_action('woocommerce_attribute_added', [$this, 'saveAttributeType'], 10, 2);
        add_action('woocommerce_attribute_updated', [$this, 'saveAttributeType'], 10, 2);

        // Add color field to attribute terms (for color type attributes)
        add_action('admin_init', [$this, 'registerTermColorFields']);

        // Enqueue admin scripts and styles
        add_action('admin_enqueue_scripts', [$this, 'enqueueAdminAssets']);

        // Add column to attributes list
        add_filter('woocommerce_attribute_columns', [$this, 'addDisplayTypeColumn']);
        add_action('woocommerce_attribute_column_display_type', [$this, 'renderDisplayTypeColumn'], 10, 1);

        // Add AJAX handler for quick type change
        add_action('wp_ajax_sega_update_attribute_type', [$this, 'ajaxUpdateAttributeType']);
    }

    /**
     * Add display type field to the "Add new attribute" form.
     */
    public function addAttributeTypeField(): void
    {
        ?>
        <div class="form-field">
            <label for="attribute_display_type"><?php esc_html_e('Display Type', 'sage'); ?></label>
            <select name="attribute_display_type" id="attribute_display_type" class="sega-display-type-select">
                <?php foreach (self::DISPLAY_TYPES as $value => $label): ?>
                    <option value="<?php echo esc_attr($value); ?>">
                        <?php echo esc_html($label); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <p class="description">
                <?php esc_html_e('Choose how this attribute will be displayed on the product page.', 'sage'); ?>
            </p>
        </div>
        <?php
    }

    /**
     * Add display type field to the "Edit attribute" form.
     */
    public function editAttributeTypeField(): void
    {
        // Get the attribute being edited
        $attribute_id = isset($_GET['edit']) ? absint($_GET['edit']) : 0;

        if (!$attribute_id) {
            return;
        }

        $attribute = wc_get_attribute($attribute_id);

        if (!$attribute) {
            return;
        }

        $current_type = get_option("sega_attribute_{$attribute->slug}_type", 'select');
        ?>
        <tr class="form-field">
            <th scope="row" valign="top">
                <label for="attribute_display_type"><?php esc_html_e('Display Type', 'sage'); ?></label>
            </th>
            <td>
                <select name="attribute_display_type" id="attribute_display_type" class="sega-display-type-select">
                    <?php foreach (self::DISPLAY_TYPES as $value => $label): ?>
                        <option value="<?php echo esc_attr($value); ?>" <?php selected($current_type, $value); ?>>
                            <?php echo esc_html($label); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <p class="description">
                    <?php esc_html_e('Choose how this attribute will be displayed on the product page.', 'sage'); ?>
                </p>

                <div class="sega-display-type-preview" style="margin-top: 15px;">
                    <strong><?php esc_html_e('Preview:', 'sage'); ?></strong>
                    <div class="preview-container" style="margin-top: 10px; padding: 15px; background: #f9f9f9; border-radius: 4px;">
                        <div class="preview-select" <?php echo $current_type !== 'select' ? 'style="display:none;"' : ''; ?>>
                            <select disabled style="min-width: 150px;">
                                <option><?php esc_html_e('Choose an option', 'sage'); ?></option>
                                <option>Option 1</option>
                                <option>Option 2</option>
                            </select>
                        </div>
                        <div class="preview-button" <?php echo $current_type !== 'button' ? 'style="display:none;"' : ''; ?>>
                            <span class="sega-preview-btn active">S</span>
                            <span class="sega-preview-btn">M</span>
                            <span class="sega-preview-btn">L</span>
                            <span class="sega-preview-btn">XL</span>
                        </div>
                        <div class="preview-color" <?php echo $current_type !== 'color' ? 'style="display:none;"' : ''; ?>>
                            <span class="sega-preview-color" style="background-color: #000000;"></span>
                            <span class="sega-preview-color active" style="background-color: #dc2626;"></span>
                            <span class="sega-preview-color" style="background-color: #2563eb;"></span>
                            <span class="sega-preview-color" style="background-color: #16a34a;"></span>
                        </div>
                    </div>
                </div>

                <?php if ($current_type === 'color'): ?>
                    <div class="sega-color-terms-notice" style="margin-top: 15px; padding: 12px; background: #fff3cd; border-left: 4px solid #ffc107; border-radius: 4px;">
                        <strong><?php esc_html_e('Color Configuration:', 'sage'); ?></strong>
                        <p style="margin: 5px 0 0;">
                            <?php
                            printf(
                                /* translators: %s: link to configure terms */
                                esc_html__('To set colors for each term, go to %s and edit individual terms.', 'sage'),
                                '<a href="' . esc_url(admin_url('edit-tags.php?taxonomy=pa_' . $attribute->slug . '&post_type=product')) . '">' .
                                esc_html__('Configure Terms', 'sage') . '</a>'
                            );
                            ?>
                        </p>
                    </div>
                <?php endif; ?>
            </td>
        </tr>
        <?php
    }

    /**
     * Save the display type when attribute is added or updated.
     *
     * @param int   $id   Attribute ID.
     * @param array $data Attribute data.
     */
    public function saveAttributeType(int $id, array $data): void
    {
        if (!isset($_POST['attribute_display_type'])) {
            return;
        }

        $type = sanitize_text_field($_POST['attribute_display_type']);

        // Validate type
        if (!array_key_exists($type, self::DISPLAY_TYPES)) {
            $type = 'select';
        }

        // Get the attribute slug
        $slug = isset($data['attribute_name']) ? sanitize_title($data['attribute_name']) : '';

        if (!$slug) {
            // Try to get from existing attribute
            $attribute = wc_get_attribute($id);
            if ($attribute) {
                $slug = $attribute->slug;
            }
        }

        if ($slug) {
            update_option("sega_attribute_{$slug}_type", $type);
        }
    }

    /**
     * Register color fields for attribute terms.
     */
    public function registerTermColorFields(): void
    {
        $taxonomies = wc_get_attribute_taxonomies();

        foreach ($taxonomies as $taxonomy) {
            $taxonomy_name = wc_attribute_taxonomy_name($taxonomy->attribute_name);

            // Add color field to term add form
            add_action("{$taxonomy_name}_add_form_fields", [$this, 'addTermColorField']);

            // Add color field to term edit form
            add_action("{$taxonomy_name}_edit_form_fields", [$this, 'editTermColorField'], 10, 2);

            // Save term color
            add_action("created_{$taxonomy_name}", [$this, 'saveTermColor'], 10, 2);
            add_action("edited_{$taxonomy_name}", [$this, 'saveTermColor'], 10, 2);

            // Add color column to terms list
            add_filter("manage_edit-{$taxonomy_name}_columns", [$this, 'addTermColorColumn']);
            add_filter("manage_{$taxonomy_name}_custom_column", [$this, 'renderTermColorColumn'], 10, 3);
        }
    }

    /**
     * Add color field to term add form.
     *
     * @param string $taxonomy The taxonomy slug.
     */
    public function addTermColorField(string $taxonomy): void
    {
        // Get attribute slug from taxonomy name
        $attr_slug = str_replace('pa_', '', $taxonomy);
        $display_type = get_option("sega_attribute_{$attr_slug}_type", 'select');

        // Only show color field for color-type attributes
        if ($display_type !== 'color') {
            return;
        }
        ?>
        <div class="form-field sega-term-color-field">
            <label for="sega_term_color"><?php esc_html_e('Swatch Color', 'sage'); ?></label>
            <div class="sega-color-picker-wrapper">
                <input type="color" name="sega_term_color" id="sega_term_color" value="#808080" class="sega-color-input">
                <input type="text" name="sega_term_color_hex" id="sega_term_color_hex" value="#808080"
                       class="sega-color-hex-input" placeholder="#000000" pattern="^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$">
            </div>
            <p class="description">
                <?php esc_html_e('Choose the color to display as a swatch for this term.', 'sage'); ?>
            </p>
        </div>
        <?php
    }

    /**
     * Add color field to term edit form.
     *
     * @param \WP_Term $term     The term object.
     * @param string   $taxonomy The taxonomy slug.
     */
    public function editTermColorField(\WP_Term $term, string $taxonomy): void
    {
        // Get attribute slug from taxonomy name
        $attr_slug = str_replace('pa_', '', $taxonomy);
        $display_type = get_option("sega_attribute_{$attr_slug}_type", 'select');

        // Only show color field for color-type attributes
        if ($display_type !== 'color') {
            return;
        }

        $color = get_option("sega_attribute_{$attr_slug}_{$term->slug}_color", '#808080');
        ?>
        <tr class="form-field sega-term-color-field">
            <th scope="row" valign="top">
                <label for="sega_term_color"><?php esc_html_e('Swatch Color', 'sage'); ?></label>
            </th>
            <td>
                <div class="sega-color-picker-wrapper">
                    <input type="color" name="sega_term_color" id="sega_term_color"
                           value="<?php echo esc_attr($color); ?>" class="sega-color-input">
                    <input type="text" name="sega_term_color_hex" id="sega_term_color_hex"
                           value="<?php echo esc_attr($color); ?>" class="sega-color-hex-input"
                           placeholder="#000000" pattern="^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$">
                </div>
                <p class="description">
                    <?php esc_html_e('Choose the color to display as a swatch for this term.', 'sage'); ?>
                </p>

                <div class="sega-color-preview" style="margin-top: 10px;">
                    <strong><?php esc_html_e('Preview:', 'sage'); ?></strong>
                    <span class="sega-preview-swatch" style="display: inline-block; width: 40px; height: 40px; border-radius: 50%; background-color: <?php echo esc_attr($color); ?>; border: 2px solid #ddd; vertical-align: middle; margin-left: 10px;"></span>
                </div>
            </td>
        </tr>
        <?php
    }

    /**
     * Save term color.
     *
     * @param int $term_id Term ID.
     * @param int $tt_id   Term taxonomy ID.
     */
    public function saveTermColor(int $term_id, int $tt_id): void
    {
        if (!isset($_POST['sega_term_color'])) {
            return;
        }

        $color = sanitize_hex_color($_POST['sega_term_color']);

        if (!$color) {
            $color = '#808080';
        }

        // Get the term to find the attribute slug
        $term = get_term($term_id);

        if (!$term || is_wp_error($term)) {
            return;
        }

        $attr_slug = str_replace('pa_', '', $term->taxonomy);

        update_option("sega_attribute_{$attr_slug}_{$term->slug}_color", $color);
    }

    /**
     * Add color column to terms list.
     *
     * @param array $columns Existing columns.
     * @return array Modified columns.
     */
    public function addTermColorColumn(array $columns): array
    {
        // Get current taxonomy
        $taxonomy = $_GET['taxonomy'] ?? '';

        if (!$taxonomy) {
            return $columns;
        }

        $attr_slug = str_replace('pa_', '', $taxonomy);
        $display_type = get_option("sega_attribute_{$attr_slug}_type", 'select');

        // Only add column for color-type attributes
        if ($display_type !== 'color') {
            return $columns;
        }

        $new_columns = [];

        foreach ($columns as $key => $value) {
            $new_columns[$key] = $value;

            // Insert color column after name
            if ($key === 'name') {
                $new_columns['sega_color'] = __('Color', 'sage');
            }
        }

        return $new_columns;
    }

    /**
     * Render color column in terms list.
     *
     * @param string $content     Column content.
     * @param string $column_name Column name.
     * @param int    $term_id     Term ID.
     * @return string Modified content.
     */
    public function renderTermColorColumn(string $content, string $column_name, int $term_id): string
    {
        if ($column_name !== 'sega_color') {
            return $content;
        }

        $term = get_term($term_id);

        if (!$term || is_wp_error($term)) {
            return $content;
        }

        $attr_slug = str_replace('pa_', '', $term->taxonomy);
        $color = get_option("sega_attribute_{$attr_slug}_{$term->slug}_color", '#808080');

        return sprintf(
            '<span class="sega-color-swatch" style="display: inline-block; width: 28px; height: 28px; border-radius: 50%%; background-color: %s; border: 2px solid #ddd; vertical-align: middle;" title="%s"></span>',
            esc_attr($color),
            esc_attr($color)
        );
    }

    /**
     * Add display type column to attributes list.
     *
     * @param array $columns Existing columns.
     * @return array Modified columns.
     */
    public function addDisplayTypeColumn(array $columns): array
    {
        $new_columns = [];

        foreach ($columns as $key => $value) {
            $new_columns[$key] = $value;

            // Insert display type column after name
            if ($key === 'name') {
                $new_columns['display_type'] = __('Display Type', 'sage');
            }
        }

        return $new_columns;
    }

    /**
     * Render display type column in attributes list.
     *
     * @param object $attribute The attribute object.
     */
    public function renderDisplayTypeColumn(object $attribute): void
    {
        $type = get_option("sega_attribute_{$attribute->attribute_name}_type", 'select');
        $label = self::DISPLAY_TYPES[$type] ?? 'Select';

        $badge_classes = [
            'select' => 'background: #e0e7ff; color: #3730a3;',
            'button' => 'background: #fef3c7; color: #92400e;',
            'color'  => 'background: #d1fae5; color: #065f46;',
        ];

        $style = $badge_classes[$type] ?? $badge_classes['select'];

        printf(
            '<span class="sega-type-badge" style="display: inline-block; padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight: 500; %s">%s</span>',
            esc_attr($style),
            esc_html($label)
        );
    }

    /**
     * Enqueue admin scripts and styles.
     *
     * @param string $hook The current admin page.
     */
    public function enqueueAdminAssets(string $hook): void
    {
        // Only load on relevant pages
        $allowed_hooks = [
            'product_page_product_attributes',
            'edit-tags.php',
            'term.php',
        ];

        // Check if we're on an attribute-related page
        $is_attribute_page = in_array($hook, $allowed_hooks, true);
        $is_product_attribute = isset($_GET['taxonomy']) && strpos($_GET['taxonomy'], 'pa_') === 0;

        if (!$is_attribute_page && !$is_product_attribute) {
            return;
        }

        // Inline styles
        wp_add_inline_style('woocommerce_admin_styles', $this->getAdminStyles());

        // Inline script
        wp_add_inline_script('jquery', $this->getAdminScript());
    }

    /**
     * Get admin CSS styles.
     *
     * @return string CSS styles.
     */
    protected function getAdminStyles(): string
    {
        return '
            .sega-display-type-select {
                min-width: 200px;
            }

            .sega-color-picker-wrapper {
                display: flex;
                align-items: center;
                gap: 10px;
            }

            .sega-color-input {
                width: 50px;
                height: 40px;
                padding: 0;
                border: 2px solid #ddd;
                border-radius: 6px;
                cursor: pointer;
                background: none;
            }

            .sega-color-input::-webkit-color-swatch-wrapper {
                padding: 3px;
            }

            .sega-color-input::-webkit-color-swatch {
                border-radius: 3px;
                border: none;
            }

            .sega-color-hex-input {
                width: 100px;
                padding: 8px 10px;
                border: 1px solid #ddd;
                border-radius: 4px;
                font-family: monospace;
                text-transform: uppercase;
            }

            .sega-color-hex-input:focus {
                border-color: #2271b1;
                box-shadow: 0 0 0 1px #2271b1;
                outline: none;
            }

            .sega-color-hex-input.invalid {
                border-color: #dc3232;
                background-color: #fff5f5;
            }

            /* Preview styles */
            .sega-preview-btn {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                min-width: 40px;
                height: 40px;
                padding: 0 12px;
                border: 2px solid #ddd;
                border-radius: 6px;
                background: #fff;
                font-weight: 500;
                font-size: 14px;
                color: #374151;
                margin-right: 6px;
                cursor: default;
            }

            .sega-preview-btn.active {
                border-color: #2271b1;
                background: #f0f6fc;
                color: #2271b1;
            }

            .sega-preview-color {
                display: inline-block;
                width: 36px;
                height: 36px;
                border-radius: 50%;
                border: 3px solid transparent;
                margin-right: 8px;
                cursor: default;
                box-shadow: 0 0 0 1px #ddd;
            }

            .sega-preview-color.active {
                border-color: #2271b1;
                box-shadow: 0 0 0 1px #2271b1;
            }

            /* Type badge in list */
            .sega-type-badge {
                white-space: nowrap;
            }

            /* Color column in terms list */
            .column-sega_color {
                width: 60px;
                text-align: center;
            }
        ';
    }

    /**
     * Get admin JavaScript.
     *
     * @return string JavaScript code.
     */
    protected function getAdminScript(): string
    {
        return '
            jQuery(document).ready(function($) {
                // Display type preview toggle
                var $typeSelect = $(".sega-display-type-select");

                if ($typeSelect.length) {
                    $typeSelect.on("change", function() {
                        var type = $(this).val();
                        var $container = $(this).closest("td, .form-field").find(".preview-container");

                        $container.find("[class^=preview-]").hide();
                        $container.find(".preview-" + type).show();
                    });
                }

                // Color picker synchronization
                var $colorInput = $(".sega-color-input");
                var $hexInput = $(".sega-color-hex-input");

                if ($colorInput.length && $hexInput.length) {
                    // Sync color picker to hex input
                    $colorInput.on("input change", function() {
                        var color = $(this).val();
                        $hexInput.val(color).removeClass("invalid");

                        // Update preview swatch
                        $(".sega-preview-swatch").css("background-color", color);
                    });

                    // Sync hex input to color picker
                    $hexInput.on("input change", function() {
                        var color = $(this).val().trim();

                        // Add # if missing
                        if (color && !color.startsWith("#")) {
                            color = "#" + color;
                        }

                        // Validate hex color
                        if (/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/.test(color)) {
                            $colorInput.val(color);
                            $(this).removeClass("invalid");

                            // Update preview swatch
                            $(".sega-preview-swatch").css("background-color", color);
                        } else if (color.length > 1) {
                            $(this).addClass("invalid");
                        }
                    });
                }
            });
        ';
    }

    /**
     * AJAX handler for quick attribute type update.
     */
    public function ajaxUpdateAttributeType(): void
    {
        check_ajax_referer('sega_attribute_swatches', 'nonce');

        if (!current_user_can('manage_woocommerce')) {
            wp_send_json_error(['message' => __('Permission denied.', 'sage')]);
        }

        $slug = isset($_POST['attribute_slug']) ? sanitize_text_field($_POST['attribute_slug']) : '';
        $type = isset($_POST['display_type']) ? sanitize_text_field($_POST['display_type']) : '';

        if (!$slug || !array_key_exists($type, self::DISPLAY_TYPES)) {
            wp_send_json_error(['message' => __('Invalid parameters.', 'sage')]);
        }

        update_option("sega_attribute_{$slug}_type", $type);

        wp_send_json_success([
            'message' => __('Display type updated.', 'sage'),
            'type'    => $type,
            'label'   => self::DISPLAY_TYPES[$type],
        ]);
    }
}
