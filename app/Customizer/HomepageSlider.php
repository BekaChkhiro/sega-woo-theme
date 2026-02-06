<?php

/**
 * Homepage Slider Customizer
 *
 * Adds WordPress Customizer controls for managing homepage slider banners.
 * Supports dynamic add/remove of slides (max 5) with image and link fields.
 * Features accordion UI, live preview, and drag-and-drop reordering.
 *
 * @package Sage
 */

namespace App\Customizer;

class HomepageSlider
{
    /**
     * Maximum number of slides allowed.
     */
    protected const MAX_SLIDES = 5;

    /**
     * Register Customizer settings and controls.
     */
    public function register(\WP_Customize_Manager $wp_customize): void
    {
        // Add custom CSS and JS
        add_action('customize_controls_print_styles', [$this, 'customizer_styles']);
        add_action('customize_controls_print_footer_scripts', [$this, 'customizer_scripts']);
        add_action('customize_preview_init', [$this, 'preview_scripts']);

        // Ensure WooCommerce panel exists
        if (!$wp_customize->get_panel('woocommerce_panel')) {
            $wp_customize->add_panel('woocommerce_panel', [
                'title'       => __('WooCommerce', 'sage'),
                'description' => __('Customize your store settings.', 'sage'),
                'priority'    => 150,
            ]);
        }

        // Add Homepage Slider section
        $wp_customize->add_section('homepage_slider', [
            'title'       => __('Homepage Slider', 'sage'),
            'description' => $this->get_section_description(),
            'panel'       => 'woocommerce_panel',
            'priority'    => 5,
        ]);

        // Register slides data setting (JSON)
        $wp_customize->add_setting('homepage_slider_data', [
            'default'           => '[]',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this, 'sanitize_slides_data'],
        ]);

        // Hidden control for slides data
        $wp_customize->add_control('homepage_slider_data', [
            'section' => 'homepage_slider',
            'type'    => 'hidden',
            'priority' => 0,
        ]);

        // Register global slider settings
        $this->register_global_settings($wp_customize);
    }

    /**
     * Get section description HTML.
     */
    protected function get_section_description(): string
    {
        return '<div class="slider-section-intro">' .
            '<p>' . sprintf(__('Add up to %d banner slides. Drag to reorder.', 'sage'), self::MAX_SLIDES) . '</p>' .
            '<p class="tip">' . __('Recommended image size: 1200x500px', 'sage') . '</p>' .
            '</div>';
    }

    /**
     * Register global slider settings.
     */
    protected function register_global_settings(\WP_Customize_Manager $wp_customize): void
    {
        // Autoplay setting
        $wp_customize->add_setting('homepage_slider_autoplay', [
            'default'           => true,
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this, 'sanitize_checkbox'],
        ]);

        $wp_customize->add_control('homepage_slider_autoplay', [
            'label'   => __('Autoplay', 'sage'),
            'section' => 'homepage_slider',
            'type'    => 'checkbox',
            'priority' => 1,
        ]);

        // Autoplay delay
        $wp_customize->add_setting('homepage_slider_delay', [
            'default'           => 5000,
            'transport'         => 'postMessage',
            'sanitize_callback' => 'absint',
        ]);

        $wp_customize->add_control('homepage_slider_delay', [
            'label'       => __('Delay (ms)', 'sage'),
            'section'     => 'homepage_slider',
            'type'        => 'number',
            'input_attrs' => ['min' => 2000, 'max' => 10000, 'step' => 500],
            'priority'    => 2,
        ]);

        // Navigation arrows
        $wp_customize->add_setting('homepage_slider_navigation', [
            'default'           => true,
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this, 'sanitize_checkbox'],
        ]);

        $wp_customize->add_control('homepage_slider_navigation', [
            'label'   => __('Show Arrows', 'sage'),
            'section' => 'homepage_slider',
            'type'    => 'checkbox',
            'priority' => 3,
        ]);

        // Pagination dots
        $wp_customize->add_setting('homepage_slider_pagination', [
            'default'           => true,
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this, 'sanitize_checkbox'],
        ]);

        $wp_customize->add_control('homepage_slider_pagination', [
            'label'   => __('Show Dots', 'sage'),
            'section' => 'homepage_slider',
            'type'    => 'checkbox',
            'priority' => 4,
        ]);
    }

    /**
     * Sanitize slides data JSON.
     */
    public function sanitize_slides_data($value): string
    {
        $slides = json_decode($value, true);

        if (!is_array($slides)) {
            return '[]';
        }

        // Limit to max slides
        $slides = array_slice($slides, 0, self::MAX_SLIDES);

        // Sanitize each slide
        $sanitized = [];
        foreach ($slides as $slide) {
            if (!is_array($slide)) continue;

            $sanitized[] = [
                'id'    => sanitize_text_field($slide['id'] ?? uniqid()),
                'image' => esc_url_raw($slide['image'] ?? ''),
                'link'  => esc_url_raw($slide['link'] ?? ''),
            ];
        }

        return json_encode($sanitized);
    }

    /**
     * Sanitize checkbox value.
     */
    public function sanitize_checkbox($value): bool
    {
        return (bool) $value;
    }

    /**
     * Output Customizer CSS.
     */
    public function customizer_styles(): void
    {
        ?>
        <style>
            /* Section intro */
            .slider-section-intro {
                background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
                color: #fff;
                padding: 12px 15px;
                border-radius: 8px;
                margin: -12px -12px 15px -12px;
                font-size: 12px;
            }
            .slider-section-intro p { margin: 0 0 4px 0; }
            .slider-section-intro p:last-child { margin: 0; }
            .slider-section-intro .tip { opacity: 0.85; font-size: 11px; }

            /* Hide data control */
            #customize-control-homepage_slider_data { display: none !important; }

            /* Global settings */
            #customize-control-homepage_slider_autoplay,
            #customize-control-homepage_slider_delay,
            #customize-control-homepage_slider_navigation,
            #customize-control-homepage_slider_pagination {
                margin-bottom: 8px !important;
            }
            #customize-control-homepage_slider_pagination {
                margin-bottom: 16px !important;
                padding-bottom: 16px;
                border-bottom: 2px solid #e2e8f0;
            }

            /* Slides container */
            .slides-manager {
                margin-top: 12px;
            }

            /* Add slide button */
            .add-slide-btn {
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
                width: 100%;
                padding: 12px;
                background: #f0fdf4;
                border: 2px dashed #22c55e;
                border-radius: 8px;
                color: #16a34a;
                font-weight: 600;
                font-size: 13px;
                cursor: pointer;
                transition: all 0.2s;
                margin-bottom: 12px;
            }
            .add-slide-btn:hover {
                background: #dcfce7;
                border-color: #16a34a;
            }
            .add-slide-btn:disabled {
                opacity: 0.5;
                cursor: not-allowed;
                background: #f1f5f9;
                border-color: #cbd5e1;
                color: #94a3b8;
            }

            /* Slide accordion */
            .slide-accordion {
                background: #fff;
                border: 1px solid #e2e8f0;
                border-radius: 8px;
                margin-bottom: 8px;
                overflow: hidden;
                transition: all 0.2s;
            }
            .slide-accordion:hover { border-color: #cbd5e1; }
            .slide-accordion.is-open {
                border-color: #6366f1;
                box-shadow: 0 0 0 1px #6366f1;
            }
            .slide-accordion.is-dragging {
                opacity: 0.5;
                border-style: dashed;
            }
            .slide-accordion.drag-over {
                border-color: #22c55e;
                background: #f0fdf4;
            }

            /* Accordion header */
            .slide-accordion-header {
                display: flex;
                align-items: center;
                padding: 10px 12px;
                cursor: pointer;
                user-select: none;
                background: #f8fafc;
                border-bottom: 1px solid transparent;
                transition: all 0.2s;
            }
            .slide-accordion.is-open .slide-accordion-header {
                border-bottom-color: #e2e8f0;
                background: #fff;
            }

            .slide-drag-handle {
                cursor: grab;
                padding: 4px;
                margin-right: 8px;
                color: #94a3b8;
            }
            .slide-drag-handle:hover { color: #64748b; }
            .slide-drag-handle:active { cursor: grabbing; }

            .slide-thumb {
                width: 48px;
                height: 32px;
                border-radius: 4px;
                background: #e2e8f0;
                margin-right: 12px;
                overflow: hidden;
                flex-shrink: 0;
            }
            .slide-thumb img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }
            .slide-thumb-empty {
                width: 100%;
                height: 100%;
                display: flex;
                align-items: center;
                justify-content: center;
                color: #94a3b8;
            }

            .slide-info { flex: 1; }
            .slide-title {
                font-weight: 600;
                font-size: 13px;
                color: #1e293b;
            }

            .slide-toggle-icon {
                color: #94a3b8;
                transition: transform 0.2s;
                margin-left: 8px;
            }
            .slide-accordion.is-open .slide-toggle-icon {
                transform: rotate(180deg);
            }

            /* Accordion content */
            .slide-accordion-content {
                display: none;
                padding: 12px;
                background: #fff;
            }
            .slide-accordion.is-open .slide-accordion-content {
                display: block;
            }

            /* Image preview */
            .slide-image-preview {
                width: 100%;
                height: 140px;
                border-radius: 6px;
                background: #f1f5f9;
                margin-bottom: 12px;
                overflow: hidden;
                display: flex;
                align-items: center;
                justify-content: center;
                position: relative;
            }
            .slide-image-preview img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }
            .slide-image-preview .no-image {
                color: #94a3b8;
                font-size: 12px;
                text-align: center;
            }

            /* Image buttons */
            .slide-image-buttons {
                display: flex;
                gap: 8px;
                margin-bottom: 12px;
            }
            .slide-image-btn {
                flex: 1;
                padding: 8px 12px;
                border: 1px solid #e2e8f0;
                border-radius: 6px;
                background: #fff;
                font-size: 12px;
                font-weight: 500;
                cursor: pointer;
                transition: all 0.2s;
            }
            .slide-image-btn:hover {
                border-color: #6366f1;
                color: #6366f1;
            }
            .slide-image-btn.remove-image {
                color: #dc2626;
                border-color: #fecaca;
            }
            .slide-image-btn.remove-image:hover {
                background: #fef2f2;
                border-color: #dc2626;
            }

            /* Link input */
            .slide-link-group {
                margin-bottom: 12px;
            }
            .slide-link-group label {
                display: block;
                font-size: 12px;
                font-weight: 500;
                color: #374151;
                margin-bottom: 4px;
            }
            .slide-link-group input {
                width: 100%;
                padding: 8px 10px;
                border: 1px solid #e2e8f0;
                border-radius: 6px;
                font-size: 13px;
            }
            .slide-link-group input:focus {
                outline: none;
                border-color: #6366f1;
                box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.1);
            }

            /* Delete slide button */
            .delete-slide-btn {
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 6px;
                width: 100%;
                padding: 8px;
                background: #fef2f2;
                border: 1px solid #fecaca;
                border-radius: 6px;
                color: #dc2626;
                font-size: 12px;
                font-weight: 500;
                cursor: pointer;
                transition: all 0.2s;
            }
            .delete-slide-btn:hover {
                background: #fee2e2;
                border-color: #dc2626;
            }

            /* Empty state */
            .slides-empty {
                text-align: center;
                padding: 24px;
                color: #64748b;
                font-size: 13px;
            }
            .slides-empty svg {
                display: block;
                margin: 0 auto 12px;
                opacity: 0.5;
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

            const MAX_SLIDES = <?php echo self::MAX_SLIDES; ?>;
            let draggedItem = null;
            let mediaFrame = null;

            wp.customize.bind('ready', function() {
                setTimeout(initSlidesManager, 100);
            });

            function initSlidesManager() {
                const section = $('#sub-accordion-section-homepage_slider');
                if (!section.length) return;

                // Create slides manager container
                const $manager = $(`
                    <div class="slides-manager">
                        <button type="button" class="add-slide-btn" id="add-slide-btn">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M12 5v14m-7-7h14"/>
                            </svg>
                            <?php _e('Add Slide', 'sage'); ?>
                        </button>
                        <div id="slides-list"></div>
                    </div>
                `);

                // Insert after pagination control
                $('#customize-control-homepage_slider_pagination').after($manager);

                // Render existing slides
                renderSlides();

                // Bind events
                bindEvents();
            }

            function getSlides() {
                try {
                    const data = wp.customize('homepage_slider_data').get();
                    return JSON.parse(data) || [];
                } catch (e) {
                    return [];
                }
            }

            function saveSlides(slides) {
                wp.customize('homepage_slider_data').set(JSON.stringify(slides));
            }

            function renderSlides() {
                const slides = getSlides();
                const $list = $('#slides-list');
                $list.empty();

                if (slides.length === 0) {
                    $list.html(`
                        <div class="slides-empty">
                            <svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <rect x="3" y="3" width="18" height="18" rx="2"/>
                                <circle cx="8.5" cy="8.5" r="1.5"/>
                                <path d="M21 15l-5-5L5 21"/>
                            </svg>
                            <?php _e('No slides yet. Click "Add Slide" to get started.', 'sage'); ?>
                        </div>
                    `);
                } else {
                    slides.forEach((slide, index) => {
                        $list.append(buildSlideAccordion(slide, index));
                    });
                }

                // Update add button state
                updateAddButton();

                // Init drag-drop
                initDragDrop();
            }

            function buildSlideAccordion(slide, index) {
                const thumbHtml = slide.image
                    ? `<img src="${slide.image}" alt="">`
                    : `<div class="slide-thumb-empty"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/></svg></div>`;

                const previewHtml = slide.image
                    ? `<img src="${slide.image}" alt="">`
                    : `<div class="no-image"><svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/></svg><br><?php _e('No image', 'sage'); ?></div>`;

                return $(`
                    <div class="slide-accordion" data-id="${slide.id}" data-index="${index}">
                        <div class="slide-accordion-header">
                            <div class="slide-drag-handle" title="<?php _e('Drag to reorder', 'sage'); ?>">
                                <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M7 2a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm3 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0zM7 5a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm3 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0zM7 8a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm3 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm-3 3a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm3 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm-3 3a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm3 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"/>
                                </svg>
                            </div>
                            <div class="slide-thumb">${thumbHtml}</div>
                            <div class="slide-info">
                                <span class="slide-title"><?php _e('Slide', 'sage'); ?> ${index + 1}</span>
                            </div>
                            <div class="slide-toggle-icon">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M19 9l-7 7-7-7"/>
                                </svg>
                            </div>
                        </div>
                        <div class="slide-accordion-content">
                            <div class="slide-image-preview" data-id="${slide.id}">${previewHtml}</div>
                            <div class="slide-image-buttons">
                                <button type="button" class="slide-image-btn select-image" data-id="${slide.id}">
                                    ${slide.image ? '<?php _e('Change Image', 'sage'); ?>' : '<?php _e('Select Image', 'sage'); ?>'}
                                </button>
                                ${slide.image ? `<button type="button" class="slide-image-btn remove-image" data-id="${slide.id}"><?php _e('Remove', 'sage'); ?></button>` : ''}
                            </div>
                            <div class="slide-link-group">
                                <label><?php _e('Link URL (optional)', 'sage'); ?></label>
                                <input type="url" class="slide-link-input" data-id="${slide.id}" value="${slide.link || ''}" placeholder="https://">
                            </div>
                            <button type="button" class="delete-slide-btn" data-id="${slide.id}">
                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                <?php _e('Delete Slide', 'sage'); ?>
                            </button>
                        </div>
                    </div>
                `);
            }

            function updateAddButton() {
                const slides = getSlides();
                const $btn = $('#add-slide-btn');
                $btn.prop('disabled', slides.length >= MAX_SLIDES);

                if (slides.length >= MAX_SLIDES) {
                    $btn.html(`<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 5v14m-7-7h14"/></svg> <?php _e('Maximum slides reached', 'sage'); ?>`);
                } else {
                    $btn.html(`<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 5v14m-7-7h14"/></svg> <?php _e('Add Slide', 'sage'); ?> (${slides.length}/${MAX_SLIDES})`);
                }
            }

            function bindEvents() {
                // Add slide
                $(document).on('click', '#add-slide-btn', function() {
                    const slides = getSlides();
                    if (slides.length >= MAX_SLIDES) return;

                    slides.push({
                        id: 'slide_' + Date.now(),
                        image: '',
                        link: ''
                    });
                    saveSlides(slides);
                    renderSlides();

                    // Open the new slide accordion
                    setTimeout(() => {
                        const $newSlide = $('.slide-accordion').last();
                        $newSlide.addClass('is-open');
                    }, 50);
                });

                // Toggle accordion
                $(document).on('click', '.slide-accordion-header', function(e) {
                    if ($(e.target).closest('.slide-drag-handle').length) return;

                    const $accordion = $(this).closest('.slide-accordion');
                    const wasOpen = $accordion.hasClass('is-open');

                    $('.slide-accordion').removeClass('is-open');
                    if (!wasOpen) {
                        $accordion.addClass('is-open');
                    }
                });

                // Select image
                $(document).on('click', '.select-image', function() {
                    const slideId = $(this).data('id');
                    openMediaLibrary(slideId);
                });

                // Remove image
                $(document).on('click', '.remove-image', function() {
                    const slideId = $(this).data('id');
                    updateSlide(slideId, { image: '' });
                });

                // Link input change
                $(document).on('input', '.slide-link-input', function() {
                    const slideId = $(this).data('id');
                    const link = $(this).val();
                    updateSlide(slideId, { link }, false);
                });

                // Delete slide
                $(document).on('click', '.delete-slide-btn', function() {
                    const slideId = $(this).data('id');
                    if (confirm('<?php _e('Delete this slide?', 'sage'); ?>')) {
                        deleteSlide(slideId);
                    }
                });
            }

            function openMediaLibrary(slideId) {
                if (mediaFrame) {
                    mediaFrame.slideId = slideId;
                    mediaFrame.open();
                    return;
                }

                mediaFrame = wp.media({
                    title: '<?php _e('Select Slide Image', 'sage'); ?>',
                    button: { text: '<?php _e('Use Image', 'sage'); ?>' },
                    multiple: false
                });

                mediaFrame.slideId = slideId;

                mediaFrame.on('select', function() {
                    const attachment = mediaFrame.state().get('selection').first().toJSON();
                    updateSlide(mediaFrame.slideId, { image: attachment.url });
                });

                mediaFrame.open();
            }

            function updateSlide(slideId, updates, rerender = true) {
                const slides = getSlides();
                const index = slides.findIndex(s => s.id === slideId);
                if (index === -1) return;

                slides[index] = { ...slides[index], ...updates };
                saveSlides(slides);

                if (rerender) {
                    renderSlides();
                    // Reopen the accordion
                    setTimeout(() => {
                        $(`.slide-accordion[data-id="${slideId}"]`).addClass('is-open');
                    }, 50);
                }
            }

            function deleteSlide(slideId) {
                let slides = getSlides();
                slides = slides.filter(s => s.id !== slideId);
                saveSlides(slides);
                renderSlides();
            }

            function initDragDrop() {
                const $list = $('#slides-list');

                $list.off('dragstart dragend dragover drop dragleave');

                $list.on('dragstart', '.slide-accordion', function(e) {
                    draggedItem = this;
                    $(this).addClass('is-dragging');
                    e.originalEvent.dataTransfer.effectAllowed = 'move';
                });

                $list.on('dragend', '.slide-accordion', function() {
                    $(this).removeClass('is-dragging');
                    $('.slide-accordion').removeClass('drag-over');
                    draggedItem = null;
                });

                $list.on('dragover', '.slide-accordion', function(e) {
                    e.preventDefault();
                    if (this !== draggedItem) {
                        $('.slide-accordion').removeClass('drag-over');
                        $(this).addClass('drag-over');
                    }
                });

                $list.on('dragleave', '.slide-accordion', function() {
                    $(this).removeClass('drag-over');
                });

                $list.on('drop', '.slide-accordion', function(e) {
                    e.preventDefault();
                    if (this === draggedItem) return;

                    $(this).removeClass('drag-over');

                    const fromId = $(draggedItem).data('id');
                    const toId = $(this).data('id');

                    const slides = getSlides();
                    const fromIndex = slides.findIndex(s => s.id === fromId);
                    const toIndex = slides.findIndex(s => s.id === toId);

                    if (fromIndex === -1 || toIndex === -1) return;

                    // Move item
                    const [moved] = slides.splice(fromIndex, 1);
                    slides.splice(toIndex, 0, moved);

                    saveSlides(slides);
                    renderSlides();
                });

                // Make draggable via handle
                $(document).off('mousedown.slidedrag mouseup.slidedrag');
                $(document).on('mousedown.slidedrag', '.slide-drag-handle', function() {
                    $(this).closest('.slide-accordion').attr('draggable', 'true');
                });
                $(document).on('mouseup.slidedrag', '.slide-accordion', function() {
                    $(this).attr('draggable', 'false');
                });
            }

        })(jQuery);
        </script>
        <?php
    }

    /**
     * Enqueue live preview scripts.
     */
    public function preview_scripts(): void
    {
        add_action('wp_footer', [$this, 'preview_js'], 100);
    }

    /**
     * Output live preview JavaScript.
     */
    public function preview_js(): void
    {
        if (!is_customize_preview()) {
            return;
        }
        ?>
        <script>
        (function($) {
            'use strict';

            function rebuildSlider() {
                const $slider = $('.hero-slider');
                if (!$slider.length) return;

                let slides = [];
                try {
                    slides = JSON.parse(wp.customize('homepage_slider_data').get() || '[]');
                } catch (e) {
                    slides = [];
                }

                // Filter slides with images
                slides = slides.filter(s => s.image);

                const $wrapper = $slider.find('.swiper-wrapper');
                if (!$wrapper.length) return;

                $wrapper.empty();

                if (slides.length === 0) {
                    $wrapper.append(`
                        <div class="swiper-slide">
                            <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-primary-500 to-primary-700 text-white">
                                <div class="text-center">
                                    <svg class="mx-auto mb-3 h-12 w-12 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <rect x="3" y="3" width="18" height="18" rx="2"/>
                                        <circle cx="8.5" cy="8.5" r="1.5"/>
                                        <path d="M21 15l-5-5L5 21"/>
                                    </svg>
                                    <p class="text-sm opacity-75"><?php _e('Add slides in Customizer', 'sage'); ?></p>
                                </div>
                            </div>
                        </div>
                    `);
                } else {
                    slides.forEach(function(slide) {
                        const linkStart = slide.link ? `<a href="${slide.link}" class="block h-full w-full">` : '';
                        const linkEnd = slide.link ? '</a>' : '';

                        $wrapper.append(`
                            <div class="swiper-slide">
                                ${linkStart}
                                    <img src="${slide.image}" alt="" class="h-full w-full object-cover">
                                ${linkEnd}
                            </div>
                        `);
                    });
                }

                if (window.heroSliderSwiper) {
                    window.heroSliderSwiper.update();
                    window.heroSliderSwiper.slideTo(0);
                }
            }

            // Watch slides data
            wp.customize('homepage_slider_data', function(value) {
                value.bind(rebuildSlider);
            });

            // Watch global settings
            wp.customize('homepage_slider_autoplay', function(value) {
                value.bind(function(enabled) {
                    if (window.heroSliderSwiper) {
                        if (enabled) {
                            window.heroSliderSwiper.autoplay.start();
                        } else {
                            window.heroSliderSwiper.autoplay.stop();
                        }
                    }
                });
            });

            wp.customize('homepage_slider_navigation', function(value) {
                value.bind(function(show) {
                    $('.hero-slider .hero-slider-nav').toggleClass('hidden', !show);
                });
            });

            wp.customize('homepage_slider_pagination', function(value) {
                value.bind(function(show) {
                    $('.hero-slider .hero-slider-pagination').toggleClass('hidden', !show);
                });
            });

        })(jQuery);
        </script>
        <?php
    }

    /**
     * Get all slider settings for use in templates.
     */
    public static function getSliderSettings(): array
    {
        $settings = [
            'autoplay'   => get_theme_mod('homepage_slider_autoplay', true),
            'delay'      => get_theme_mod('homepage_slider_delay', 5000),
            'navigation' => get_theme_mod('homepage_slider_navigation', true),
            'pagination' => get_theme_mod('homepage_slider_pagination', true),
            'slides'     => [],
        ];

        // Get slides from JSON data
        $slidesJson = get_theme_mod('homepage_slider_data', '[]');
        $slides = json_decode($slidesJson, true);

        if (is_array($slides)) {
            foreach ($slides as $slide) {
                if (!empty($slide['image'])) {
                    $settings['slides'][] = [
                        'image' => $slide['image'],
                        'link'  => $slide['link'] ?? '',
                    ];
                }
            }
        }

        return $settings;
    }

    /**
     * Get slides array for the hero-slider component.
     */
    public static function getSlides(): array
    {
        $settings = self::getSliderSettings();
        return $settings['slides'];
    }

    /**
     * Check if slider has any active slides.
     */
    public static function hasSlides(): bool
    {
        return !empty(self::getSlides());
    }
}
