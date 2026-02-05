<?php

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class HeroSlider extends Component
{
    public array $slides;
    public bool $autoplay;
    public int $delay;
    public bool $showNavigation;
    public bool $showPagination;

    public function __construct(
        array $slides = [],
        bool $autoplay = true,
        int $delay = 5000,
        bool $showNavigation = true,
        bool $showPagination = true
    ) {
        $this->autoplay = $autoplay;
        $this->delay = $delay;
        $this->showNavigation = $showNavigation;
        $this->showPagination = $showPagination;

        // Use provided slides or get default slides
        $this->slides = !empty($slides) ? $slides : $this->getDefaultSlides();
    }

    /**
     * Get default slides for the hero slider
     * These can be customized via theme options or ACF fields in the future
     */
    protected function getDefaultSlides(): array
    {
        $shopUrl = $this->getShopUrl();

        return [
            [
                'title' => __('Slide 1', 'sage'),
                'image' => 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=1600&h=600&fit=crop',
                'gradient_from' => 'from-primary-600',
                'gradient_to' => 'to-primary-800',
            ],
            [
                'title' => __('Slide 2', 'sage'),
                'image' => 'https://images.unsplash.com/photo-1472851294608-062f824d29cc?w=1600&h=600&fit=crop',
                'gradient_from' => 'from-rose-600',
                'gradient_to' => 'to-pink-700',
            ],
            [
                'title' => __('Slide 3', 'sage'),
                'image' => 'https://images.unsplash.com/photo-1607082348824-0a96f2a4b9da?w=1600&h=600&fit=crop',
                'gradient_from' => 'from-emerald-600',
                'gradient_to' => 'to-teal-700',
            ],
        ];
    }

    /**
     * Get shop page URL
     */
    protected function getShopUrl(): string
    {
        if (function_exists('wc_get_page_permalink')) {
            return wc_get_page_permalink('shop');
        }

        return '/shop';
    }

    /**
     * Get slides from custom fields (ACF) if available
     * Can be extended to support ACF Repeater fields
     */
    public static function getSlidesFromAcf(int $postId = 0): array
    {
        if (!function_exists('get_field')) {
            return [];
        }

        $postId = $postId ?: get_the_ID();
        $slides = get_field('hero_slides', $postId);

        if (empty($slides) || !is_array($slides)) {
            return [];
        }

        return array_map(function ($slide) {
            return [
                'badge' => $slide['badge'] ?? '',
                'title' => $slide['title'] ?? '',
                'description' => $slide['description'] ?? '',
                'button_text' => $slide['button_text'] ?? __('Shop Now', 'sage'),
                'button_url' => $slide['button_url'] ?? '',
                'gradient_from' => $slide['gradient_from'] ?? 'from-primary-600',
                'gradient_to' => $slide['gradient_to'] ?? 'to-primary-800',
                'image' => $slide['image']['url'] ?? null,
            ];
        }, $slides);
    }

    /**
     * Check if component has slides to display
     */
    public function hasSlides(): bool
    {
        return !empty($this->slides);
    }

    /**
     * Get the total number of slides
     */
    public function slideCount(): int
    {
        return count($this->slides);
    }

    public function render(): View
    {
        return view('components.hero-slider');
    }
}
