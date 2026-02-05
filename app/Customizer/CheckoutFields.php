<?php

/**
 * Checkout Fields Customizer
 *
 * Adds WordPress Customizer controls for managing WooCommerce checkout fields.
 * Features accordion UI, drag-drop sorting, and width controls.
 *
 * @package Sage
 */

namespace App\Customizer;

use WP_Customize_Manager;
use WP_Customize_Control;

/**
 * Custom control for the entire field manager.
 */
class Checkout_Fields_Manager_Control extends WP_Customize_Control
{
    public $type = 'checkout_fields_manager';
    public $field_group = 'billing';
    public $fields = [];

    public function render_content()
    {
        $group = $this->field_group;
        ?>
        <div class="checkout-fields-manager" data-group="<?php echo esc_attr($group); ?>">

            <!-- Enabled Fields Section -->
            <div class="fields-section enabled-section">
                <div class="section-header">
                    <span class="section-icon">✓</span>
                    <span class="section-title"><?php _e('Enabled Fields', 'sage'); ?></span>
                    <span class="section-count enabled-count">0</span>
                </div>
                <div class="fields-list sortable-fields" data-status="enabled">
                    <!-- Fields will be rendered here by JS -->
                </div>
            </div>

            <!-- Disabled Fields Section -->
            <div class="fields-section disabled-section">
                <div class="section-header">
                    <span class="section-icon">✗</span>
                    <span class="section-title"><?php _e('Disabled Fields', 'sage'); ?></span>
                    <span class="section-count disabled-count">0</span>
                </div>
                <div class="fields-list sortable-fields" data-status="disabled">
                    <!-- Fields will be rendered here by JS -->
                </div>
                <div class="empty-state">
                    <span><?php _e('Drag fields here to disable them', 'sage'); ?></span>
                </div>
            </div>

            <!-- Hidden fields data -->
            <script type="application/json" class="fields-data">
                <?php echo json_encode($this->fields); ?>
            </script>
        </div>
        <?php
    }
}

class CheckoutFields
{
    /**
     * Register Customizer settings and controls.
     */
    public function register(WP_Customize_Manager $wp_customize): void
    {
        // Add custom CSS and JS
        add_action('customize_controls_print_styles', [$this, 'customizer_styles']);
        add_action('customize_controls_print_footer_scripts', [$this, 'customizer_scripts']);

        // Add WooCommerce Panel
        $wp_customize->add_panel('woocommerce_panel', [
            'title'       => __('WooCommerce', 'sage'),
            'description' => __('Customize your store settings.', 'sage'),
            'priority'    => 150,
        ]);

        // Register sections and fields
        $this->register_billing_section($wp_customize);
        $this->register_shipping_section($wp_customize);
        $this->register_order_section($wp_customize);
    }

    /**
     * Register billing fields section.
     */
    protected function register_billing_section(WP_Customize_Manager $wp_customize): void
    {
        $wp_customize->add_section('checkout_fields_billing', [
            'title'       => __('Billing Fields', 'sage'),
            'description' => $this->get_section_intro('billing'),
            'panel'       => 'woocommerce_panel',
            'priority'    => 10,
        ]);

        // Register individual field settings
        foreach ($this->get_billing_fields() as $key => $field) {
            $this->register_field_settings($wp_customize, 'billing', $key, $field);
        }

        // Add the visual manager control
        $wp_customize->add_setting('checkout_billing_manager', [
            'sanitize_callback' => 'sanitize_text_field',
        ]);

        $wp_customize->add_control(new Checkout_Fields_Manager_Control(
            $wp_customize,
            'checkout_billing_manager',
            [
                'section'     => 'checkout_fields_billing',
                'priority'    => 1,
                'field_group' => 'billing',
                'fields'      => $this->get_billing_fields(),
            ]
        ));
    }

    /**
     * Register shipping fields section.
     */
    protected function register_shipping_section(WP_Customize_Manager $wp_customize): void
    {
        $wp_customize->add_section('checkout_fields_shipping', [
            'title'       => __('Shipping Fields', 'sage'),
            'description' => $this->get_section_intro('shipping'),
            'panel'       => 'woocommerce_panel',
            'priority'    => 20,
        ]);

        foreach ($this->get_shipping_fields() as $key => $field) {
            $this->register_field_settings($wp_customize, 'shipping', $key, $field);
        }

        $wp_customize->add_setting('checkout_shipping_manager', [
            'sanitize_callback' => 'sanitize_text_field',
        ]);

        $wp_customize->add_control(new Checkout_Fields_Manager_Control(
            $wp_customize,
            'checkout_shipping_manager',
            [
                'section'     => 'checkout_fields_shipping',
                'priority'    => 1,
                'field_group' => 'shipping',
                'fields'      => $this->get_shipping_fields(),
            ]
        ));
    }

    /**
     * Register order fields section.
     */
    protected function register_order_section(WP_Customize_Manager $wp_customize): void
    {
        $wp_customize->add_section('checkout_fields_order', [
            'title'       => __('Order Fields', 'sage'),
            'description' => $this->get_section_intro('order'),
            'panel'       => 'woocommerce_panel',
            'priority'    => 30,
        ]);

        foreach ($this->get_order_fields() as $key => $field) {
            $this->register_field_settings($wp_customize, 'order', $key, $field);
        }

        $wp_customize->add_setting('checkout_order_manager', [
            'sanitize_callback' => 'sanitize_text_field',
        ]);

        $wp_customize->add_control(new Checkout_Fields_Manager_Control(
            $wp_customize,
            'checkout_order_manager',
            [
                'section'     => 'checkout_fields_order',
                'priority'    => 1,
                'field_group' => 'order',
                'fields'      => $this->get_order_fields(),
            ]
        ));
    }

    /**
     * Register individual field settings (hidden, controlled by JS).
     */
    protected function register_field_settings(
        WP_Customize_Manager $wp_customize,
        string $group,
        string $key,
        array $field
    ): void {
        $full_key = $group . '_' . $key;
        $prefix = 'checkout_field_' . $full_key;

        // Enabled
        $wp_customize->add_setting($prefix . '_enabled', [
            'default'           => '1',
            'transport'         => 'refresh',
            'sanitize_callback' => [$this, 'sanitize_checkbox'],
        ]);

        // Required
        $wp_customize->add_setting($prefix . '_required', [
            'default'           => $field['required'] ? '1' : '',
            'transport'         => 'refresh',
            'sanitize_callback' => [$this, 'sanitize_checkbox'],
        ]);

        // Label
        $wp_customize->add_setting($prefix . '_label', [
            'default'           => $field['label'],
            'transport'         => 'refresh',
            'sanitize_callback' => 'sanitize_text_field',
        ]);

        // Placeholder
        if (isset($field['placeholder'])) {
            $wp_customize->add_setting($prefix . '_placeholder', [
                'default'           => $field['placeholder'],
                'transport'         => 'refresh',
                'sanitize_callback' => 'sanitize_text_field',
            ]);
        }

        // Priority (order)
        $wp_customize->add_setting($prefix . '_priority', [
            'default'           => $field['priority'] ?? 10,
            'transport'         => 'refresh',
            'sanitize_callback' => 'absint',
        ]);

        // Width
        $wp_customize->add_setting($prefix . '_width', [
            'default'           => $field['width'] ?? '100',
            'transport'         => 'refresh',
            'sanitize_callback' => 'sanitize_text_field',
        ]);
    }

    /**
     * Get section intro HTML.
     */
    protected function get_section_intro(string $type): string
    {
        $intros = [
            'billing'  => __('Drag to reorder. Click to edit. Drag to disabled section to hide.', 'sage'),
            'shipping' => __('Manage shipping address fields displayed during checkout.', 'sage'),
            'order'    => __('Manage additional order fields like notes.', 'sage'),
        ];

        return '<div class="cfm-section-intro">' . ($intros[$type] ?? '') . '</div>';
    }

    /**
     * Output Customizer CSS.
     */
    public function customizer_styles(): void
    {
        ?>
        <style>
            /* Section intro */
            .cfm-section-intro {
                background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
                color: #fff;
                padding: 12px 15px;
                border-radius: 8px;
                margin: -12px -12px 15px -12px;
                font-size: 12px;
                line-height: 1.5;
            }

            /* Main manager container */
            .checkout-fields-manager {
                margin: 0 -12px;
            }

            /* Section styling */
            .fields-section {
                margin-bottom: 15px;
            }

            .section-header {
                display: flex;
                align-items: center;
                gap: 8px;
                padding: 10px 12px;
                background: #f1f5f9;
                border-radius: 8px 8px 0 0;
                border: 1px solid #e2e8f0;
                border-bottom: none;
            }

            .enabled-section .section-header {
                background: linear-gradient(to right, #ecfdf5, #f0fdf4);
                border-color: #bbf7d0;
            }

            .disabled-section .section-header {
                background: linear-gradient(to right, #fef2f2, #fef2f2);
                border-color: #fecaca;
            }

            .section-icon {
                width: 24px;
                height: 24px;
                display: flex;
                align-items: center;
                justify-content: center;
                border-radius: 50%;
                font-size: 12px;
                font-weight: bold;
            }

            .enabled-section .section-icon {
                background: #22c55e;
                color: white;
            }

            .disabled-section .section-icon {
                background: #ef4444;
                color: white;
            }

            .section-title {
                flex: 1;
                font-weight: 600;
                font-size: 13px;
                color: #374151;
            }

            .section-count {
                background: #fff;
                padding: 2px 8px;
                border-radius: 10px;
                font-size: 11px;
                font-weight: 600;
                color: #6b7280;
                border: 1px solid #e5e7eb;
            }

            /* Fields list */
            .fields-list {
                min-height: 50px;
                background: #fff;
                border: 1px solid #e2e8f0;
                border-top: none;
                border-radius: 0 0 8px 8px;
                padding: 8px;
            }

            .enabled-section .fields-list {
                border-color: #bbf7d0;
            }

            .disabled-section .fields-list {
                border-color: #fecaca;
            }

            /* Empty state */
            .empty-state {
                display: none;
                padding: 20px;
                text-align: center;
                color: #9ca3af;
                font-size: 12px;
                font-style: italic;
            }

            .fields-list:empty + .empty-state {
                display: block;
            }

            /* Field accordion item */
            .field-accordion {
                background: #fff;
                border: 1px solid #e5e7eb;
                border-radius: 8px;
                margin-bottom: 6px;
                overflow: hidden;
                transition: all 0.2s ease;
                cursor: grab;
            }

            .field-accordion:hover {
                border-color: #c7d2fe;
                box-shadow: 0 2px 8px rgba(99, 102, 241, 0.1);
            }

            .field-accordion.dragging {
                opacity: 0.5;
                cursor: grabbing;
            }

            .field-accordion.drag-over {
                border-color: #6366f1;
                border-style: dashed;
            }

            /* Field header */
            .field-header {
                display: flex;
                align-items: center;
                gap: 10px;
                padding: 10px 12px;
                cursor: pointer;
                user-select: none;
            }

            .field-drag-handle {
                color: #9ca3af;
                cursor: grab;
                padding: 4px;
            }

            .field-drag-handle:hover {
                color: #6366f1;
            }

            .field-icon {
                width: 32px;
                height: 32px;
                display: flex;
                align-items: center;
                justify-content: center;
                background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
                border-radius: 6px;
                font-size: 14px;
                flex-shrink: 0;
            }

            .field-info {
                flex: 1;
                min-width: 0;
            }

            .field-name {
                font-weight: 600;
                font-size: 13px;
                color: #1f2937;
                display: block;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .field-meta {
                display: flex;
                gap: 8px;
                margin-top: 2px;
            }

            .field-badge {
                font-size: 10px;
                padding: 1px 6px;
                border-radius: 4px;
                font-weight: 500;
            }

            .badge-required {
                background: #fef3c7;
                color: #d97706;
            }

            .badge-optional {
                background: #e0e7ff;
                color: #4f46e5;
            }

            .badge-width {
                background: #f3f4f6;
                color: #6b7280;
            }

            .field-toggle {
                color: #9ca3af;
                transition: transform 0.2s ease;
            }

            .field-accordion.open .field-toggle {
                transform: rotate(180deg);
            }

            /* Field content (accordion body) */
            .field-content {
                display: none;
                padding: 0 12px 12px 12px;
                border-top: 1px solid #f3f4f6;
                background: #fafafa;
            }

            .field-accordion.open .field-content {
                display: block;
            }

            /* Form controls inside accordion */
            .field-row {
                margin-top: 12px;
            }

            .field-row label {
                display: block;
                font-size: 11px;
                font-weight: 600;
                color: #6b7280;
                margin-bottom: 4px;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }

            .field-row input[type="text"] {
                width: 100%;
                padding: 8px 10px;
                border: 1px solid #e5e7eb;
                border-radius: 6px;
                font-size: 13px;
                transition: all 0.2s;
            }

            .field-row input[type="text"]:focus {
                border-color: #6366f1;
                outline: none;
                box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
            }

            .field-row select {
                width: 100%;
                padding: 8px 10px;
                border: 1px solid #e5e7eb;
                border-radius: 6px;
                font-size: 13px;
                background: #fff;
                cursor: pointer;
            }

            .field-row-inline {
                display: flex;
                gap: 12px;
            }

            .field-row-inline > div {
                flex: 1;
            }

            /* Toggle switch in accordion */
            .toggle-row {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 8px 0;
            }

            .toggle-row label {
                margin: 0;
                font-size: 12px;
                color: #374151;
                text-transform: none;
                letter-spacing: normal;
                font-weight: 500;
            }

            .toggle-switch {
                position: relative;
                width: 40px;
                height: 22px;
            }

            .toggle-switch input {
                opacity: 0;
                width: 0;
                height: 0;
            }

            .toggle-slider {
                position: absolute;
                cursor: pointer;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: #d1d5db;
                border-radius: 22px;
                transition: 0.3s;
            }

            .toggle-slider:before {
                position: absolute;
                content: "";
                height: 16px;
                width: 16px;
                left: 3px;
                bottom: 3px;
                background: white;
                border-radius: 50%;
                transition: 0.3s;
                box-shadow: 0 1px 3px rgba(0,0,0,0.2);
            }

            .toggle-switch input:checked + .toggle-slider {
                background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            }

            .toggle-switch input:checked + .toggle-slider:before {
                transform: translateX(18px);
            }

            /* Sortable placeholder */
            .sortable-placeholder {
                height: 50px;
                background: #e0e7ff;
                border: 2px dashed #6366f1;
                border-radius: 8px;
                margin-bottom: 6px;
            }

            /* Hide default controls */
            #customize-control-checkout_billing_manager .customize-control-title,
            #customize-control-checkout_billing_manager .customize-control-description,
            #customize-control-checkout_shipping_manager .customize-control-title,
            #customize-control-checkout_shipping_manager .customize-control-description,
            #customize-control-checkout_order_manager .customize-control-title,
            #customize-control-checkout_order_manager .customize-control-description {
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

            // Wait for customizer to be ready
            wp.customize.bind('ready', function() {
                initCheckoutFieldsManager();
            });

            function initCheckoutFieldsManager() {
                $('.checkout-fields-manager').each(function() {
                    const $manager = $(this);
                    const group = $manager.data('group');
                    const fieldsData = JSON.parse($manager.find('.fields-data').text());

                    renderFields($manager, group, fieldsData);
                    initSortable($manager, group);
                    initAccordions($manager);
                    updateCounts($manager);
                });
            }

            function renderFields($manager, group, fieldsData) {
                const $enabledList = $manager.find('.fields-list[data-status="enabled"]');
                const $disabledList = $manager.find('.fields-list[data-status="disabled"]');

                // Sort fields by priority
                const sortedFields = Object.entries(fieldsData).map(([key, field]) => {
                    const fullKey = group + '_' + key;
                    const priority = parseInt(wp.customize('checkout_field_' + fullKey + '_priority').get()) || field.priority || 10;
                    return { key, field, priority, fullKey };
                }).sort((a, b) => a.priority - b.priority);

                sortedFields.forEach(({ key, field, fullKey }) => {
                    const enabled = wp.customize('checkout_field_' + fullKey + '_enabled').get();
                    const isEnabled = enabled === '1' || enabled === true || enabled === 1;

                    const $fieldHtml = createFieldAccordion(key, field, group, fullKey);

                    if (isEnabled) {
                        $enabledList.append($fieldHtml);
                    } else {
                        $disabledList.append($fieldHtml);
                    }
                });
            }

            function createFieldAccordion(key, field, group, fullKey) {
                const settingPrefix = 'checkout_field_' + fullKey;

                const label = wp.customize(settingPrefix + '_label').get() || field.label;
                const placeholder = field.placeholder ? (wp.customize(settingPrefix + '_placeholder')?.get() || field.placeholder) : '';
                const required = wp.customize(settingPrefix + '_required').get();
                const isRequired = required === '1' || required === true || required === 1;
                const width = wp.customize(settingPrefix + '_width').get() || '100';

                return $(`
                    <div class="field-accordion" data-key="${key}" data-full-key="${fullKey}">
                        <div class="field-header">
                            <span class="field-drag-handle">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="9" cy="5" r="1"/><circle cx="9" cy="12" r="1"/><circle cx="9" cy="19" r="1"/>
                                    <circle cx="15" cy="5" r="1"/><circle cx="15" cy="12" r="1"/><circle cx="15" cy="19" r="1"/>
                                </svg>
                            </span>
                            <span class="field-icon">${field.icon || '📝'}</span>
                            <div class="field-info">
                                <span class="field-name">${label}</span>
                                <div class="field-meta">
                                    <span class="field-badge ${isRequired ? 'badge-required' : 'badge-optional'}">
                                        ${isRequired ? 'Required' : 'Optional'}
                                    </span>
                                    <span class="field-badge badge-width">${width}%</span>
                                </div>
                            </div>
                            <span class="field-toggle">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="6 9 12 15 18 9"></polyline>
                                </svg>
                            </span>
                        </div>
                        <div class="field-content">
                            <div class="field-row">
                                <label>Label</label>
                                <input type="text" class="field-label-input" value="${label}" data-setting="${settingPrefix}_label">
                            </div>
                            ${placeholder ? `
                            <div class="field-row">
                                <label>Placeholder</label>
                                <input type="text" class="field-placeholder-input" value="${placeholder}" data-setting="${settingPrefix}_placeholder">
                            </div>
                            ` : ''}
                            <div class="field-row field-row-inline">
                                <div>
                                    <label>Width</label>
                                    <select class="field-width-select" data-setting="${settingPrefix}_width">
                                        <option value="25" ${width === '25' ? 'selected' : ''}>25%</option>
                                        <option value="33" ${width === '33' ? 'selected' : ''}>33%</option>
                                        <option value="50" ${width === '50' ? 'selected' : ''}>50%</option>
                                        <option value="66" ${width === '66' ? 'selected' : ''}>66%</option>
                                        <option value="75" ${width === '75' ? 'selected' : ''}>75%</option>
                                        <option value="100" ${width === '100' ? 'selected' : ''}>100%</option>
                                    </select>
                                </div>
                            </div>
                            <div class="field-row">
                                <div class="toggle-row">
                                    <label>Required Field</label>
                                    <label class="toggle-switch">
                                        <input type="checkbox" class="field-required-toggle" data-setting="${settingPrefix}_required" ${isRequired ? 'checked' : ''}>
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                `);
            }

            function initSortable($manager, group) {
                const $lists = $manager.find('.sortable-fields');

                $lists.each(function() {
                    const $list = $(this);
                    const status = $list.data('status');

                    // Make draggable
                    $list.on('dragstart', '.field-accordion', function(e) {
                        $(this).addClass('dragging');
                        e.originalEvent.dataTransfer.setData('text/plain', $(this).data('full-key'));
                    });

                    $list.on('dragend', '.field-accordion', function() {
                        $(this).removeClass('dragging');
                        $('.field-accordion').removeClass('drag-over');
                        updatePriorities($manager, group);
                        updateCounts($manager);
                    });

                    $list.on('dragover', function(e) {
                        e.preventDefault();
                        const $dragging = $('.field-accordion.dragging');
                        const $afterElement = getDragAfterElement($list[0], e.originalEvent.clientY);

                        if ($afterElement == null) {
                            $list.append($dragging);
                        } else {
                            $dragging.insertBefore($afterElement);
                        }
                    });

                    // Set draggable attribute
                    $list.find('.field-accordion').attr('draggable', true);
                });

                // Handle dropping between lists (enable/disable)
                $manager.find('.fields-list').on('drop', function(e) {
                    e.preventDefault();
                    const $target = $(this);
                    const targetStatus = $target.data('status');
                    const $dragging = $('.field-accordion.dragging');
                    const fullKey = $dragging.data('full-key');

                    // Update enabled setting based on which list it's dropped in
                    const newEnabled = targetStatus === 'enabled' ? '1' : '';
                    wp.customize('checkout_field_' + fullKey + '_enabled').set(newEnabled);

                    updateCounts($manager);
                });
            }

            function getDragAfterElement(container, y) {
                const draggableElements = [...container.querySelectorAll('.field-accordion:not(.dragging)')];

                return draggableElements.reduce((closest, child) => {
                    const box = child.getBoundingClientRect();
                    const offset = y - box.top - box.height / 2;

                    if (offset < 0 && offset > closest.offset) {
                        return { offset: offset, element: child };
                    } else {
                        return closest;
                    }
                }, { offset: Number.NEGATIVE_INFINITY }).element;
            }

            function initAccordions($manager) {
                $manager.on('click', '.field-header', function(e) {
                    if ($(e.target).closest('.field-drag-handle').length) return;

                    const $accordion = $(this).closest('.field-accordion');
                    $accordion.toggleClass('open');
                });

                // Handle input changes
                $manager.on('change input', '.field-label-input, .field-placeholder-input', function() {
                    const setting = $(this).data('setting');
                    wp.customize(setting).set($(this).val());

                    // Update visible label
                    const $accordion = $(this).closest('.field-accordion');
                    if ($(this).hasClass('field-label-input')) {
                        $accordion.find('.field-name').text($(this).val());
                    }
                });

                $manager.on('change', '.field-width-select', function() {
                    const setting = $(this).data('setting');
                    const value = $(this).val();
                    wp.customize(setting).set(value);

                    // Update badge
                    const $accordion = $(this).closest('.field-accordion');
                    $accordion.find('.badge-width').text(value + '%');
                });

                $manager.on('change', '.field-required-toggle', function() {
                    const setting = $(this).data('setting');
                    const checked = $(this).is(':checked');
                    wp.customize(setting).set(checked ? '1' : '');

                    // Update badge
                    const $accordion = $(this).closest('.field-accordion');
                    const $badge = $accordion.find('.badge-required, .badge-optional');
                    $badge.removeClass('badge-required badge-optional')
                          .addClass(checked ? 'badge-required' : 'badge-optional')
                          .text(checked ? 'Required' : 'Optional');
                });
            }

            function updatePriorities($manager, group) {
                let priority = 10;
                $manager.find('.fields-list[data-status="enabled"] .field-accordion').each(function() {
                    const fullKey = $(this).data('full-key');
                    wp.customize('checkout_field_' + fullKey + '_priority').set(priority);
                    priority += 10;
                });

                // Disabled fields get high priority (end of list)
                priority = 1000;
                $manager.find('.fields-list[data-status="disabled"] .field-accordion').each(function() {
                    const fullKey = $(this).data('full-key');
                    wp.customize('checkout_field_' + fullKey + '_priority').set(priority);
                    priority += 10;
                });
            }

            function updateCounts($manager) {
                const enabledCount = $manager.find('.fields-list[data-status="enabled"] .field-accordion').length;
                const disabledCount = $manager.find('.fields-list[data-status="disabled"] .field-accordion').length;

                $manager.find('.enabled-count').text(enabledCount);
                $manager.find('.disabled-count').text(disabledCount);
            }

        })(jQuery);
        </script>
        <?php
    }

    /**
     * Get billing fields.
     */
    protected function get_billing_fields(): array
    {
        return [
            'email' => [
                'label'       => __('Email Address', 'sage'),
                'placeholder' => __('your@email.com', 'sage'),
                'required'    => true,
                'priority'    => 10,
                'width'       => '100',
                'icon'        => '📧',
            ],
            'phone' => [
                'label'       => __('Phone Number', 'sage'),
                'placeholder' => __('+1 (555) 000-0000', 'sage'),
                'required'    => true,
                'priority'    => 20,
                'width'       => '100',
                'icon'        => '📱',
            ],
            'first_name' => [
                'label'       => __('First Name', 'sage'),
                'placeholder' => __('John', 'sage'),
                'required'    => true,
                'priority'    => 30,
                'width'       => '50',
                'icon'        => '👤',
            ],
            'last_name' => [
                'label'       => __('Last Name', 'sage'),
                'placeholder' => __('Doe', 'sage'),
                'required'    => true,
                'priority'    => 40,
                'width'       => '50',
                'icon'        => '👤',
            ],
            'company' => [
                'label'       => __('Company', 'sage'),
                'placeholder' => __('Company name', 'sage'),
                'required'    => false,
                'priority'    => 50,
                'width'       => '100',
                'icon'        => '🏢',
            ],
            'country' => [
                'label'       => __('Country / Region', 'sage'),
                'required'    => true,
                'priority'    => 60,
                'width'       => '100',
                'icon'        => '🌍',
            ],
            'address_1' => [
                'label'       => __('Street Address', 'sage'),
                'placeholder' => __('House number and street', 'sage'),
                'required'    => true,
                'priority'    => 70,
                'width'       => '100',
                'icon'        => '🏠',
            ],
            'address_2' => [
                'label'       => __('Address Line 2', 'sage'),
                'placeholder' => __('Apartment, suite, etc.', 'sage'),
                'required'    => false,
                'priority'    => 80,
                'width'       => '100',
                'icon'        => '📍',
            ],
            'city' => [
                'label'       => __('City', 'sage'),
                'placeholder' => __('City', 'sage'),
                'required'    => true,
                'priority'    => 90,
                'width'       => '50',
                'icon'        => '🏙️',
            ],
            'state' => [
                'label'       => __('State / Province', 'sage'),
                'required'    => true,
                'priority'    => 100,
                'width'       => '50',
                'icon'        => '📍',
            ],
            'postcode' => [
                'label'       => __('ZIP / Postal Code', 'sage'),
                'placeholder' => __('ZIP / Postal Code', 'sage'),
                'required'    => true,
                'priority'    => 110,
                'width'       => '50',
                'icon'        => '📮',
            ],
        ];
    }

    /**
     * Get shipping fields.
     */
    protected function get_shipping_fields(): array
    {
        return [
            'first_name' => [
                'label'       => __('First Name', 'sage'),
                'placeholder' => __('John', 'sage'),
                'required'    => true,
                'priority'    => 10,
                'width'       => '50',
                'icon'        => '👤',
            ],
            'last_name' => [
                'label'       => __('Last Name', 'sage'),
                'placeholder' => __('Doe', 'sage'),
                'required'    => true,
                'priority'    => 20,
                'width'       => '50',
                'icon'        => '👤',
            ],
            'company' => [
                'label'       => __('Company', 'sage'),
                'placeholder' => __('Company name', 'sage'),
                'required'    => false,
                'priority'    => 30,
                'width'       => '100',
                'icon'        => '🏢',
            ],
            'country' => [
                'label'       => __('Country / Region', 'sage'),
                'required'    => true,
                'priority'    => 40,
                'width'       => '100',
                'icon'        => '🌍',
            ],
            'address_1' => [
                'label'       => __('Street Address', 'sage'),
                'placeholder' => __('House number and street', 'sage'),
                'required'    => true,
                'priority'    => 50,
                'width'       => '100',
                'icon'        => '🏠',
            ],
            'address_2' => [
                'label'       => __('Address Line 2', 'sage'),
                'placeholder' => __('Apartment, suite, etc.', 'sage'),
                'required'    => false,
                'priority'    => 60,
                'width'       => '100',
                'icon'        => '📍',
            ],
            'city' => [
                'label'       => __('City', 'sage'),
                'placeholder' => __('City', 'sage'),
                'required'    => true,
                'priority'    => 70,
                'width'       => '50',
                'icon'        => '🏙️',
            ],
            'state' => [
                'label'       => __('State / Province', 'sage'),
                'required'    => true,
                'priority'    => 80,
                'width'       => '50',
                'icon'        => '📍',
            ],
            'postcode' => [
                'label'       => __('ZIP / Postal Code', 'sage'),
                'placeholder' => __('ZIP / Postal Code', 'sage'),
                'required'    => true,
                'priority'    => 90,
                'width'       => '50',
                'icon'        => '📮',
            ],
        ];
    }

    /**
     * Get order fields.
     */
    protected function get_order_fields(): array
    {
        return [
            'comments' => [
                'label'       => __('Order Notes', 'sage'),
                'placeholder' => __('Special instructions...', 'sage'),
                'required'    => false,
                'priority'    => 10,
                'width'       => '100',
                'icon'        => '📝',
            ],
        ];
    }

    /**
     * Sanitize checkbox.
     */
    public function sanitize_checkbox($value): string
    {
        return $value ? '1' : '';
    }
}
