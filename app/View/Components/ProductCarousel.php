<?php

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\View\Component;
use WC_Product;

class ProductCarousel extends Component
{
    public Collection $products;
    public string $title;
    public string $id;
    public int $slidesPerView;
    public int $spaceBetween;
    public bool $autoplay;
    public bool $loop;
    public bool $navigation;
    public bool $pagination;
    public ?string $viewAllUrl;
    public ?string $viewAllText;

    /**
     * Create a new component instance.
     *
     * @param array|Collection $products Array of WC_Product objects or product IDs
     * @param string $title Section title
     * @param string|null $id Unique ID for the carousel (auto-generated if not provided)
     * @param int $slidesPerView Number of slides visible at once (desktop)
     * @param int $spaceBetween Space between slides in pixels
     * @param bool $autoplay Enable autoplay
     * @param bool $loop Enable loop mode
     * @param bool $navigation Show navigation arrows
     * @param bool $pagination Show pagination dots
     * @param string|null $viewAllUrl URL for "View All" link
     * @param string|null $viewAllText Text for "View All" link
     */
    public function __construct(
        $products = [],
        string $title = '',
        ?string $id = null,
        int $slidesPerView = 4,
        int $spaceBetween = 24,
        bool $autoplay = false,
        bool $loop = true,
        bool $navigation = true,
        bool $pagination = false,
        ?string $viewAllUrl = null,
        ?string $viewAllText = null
    ) {
        // Convert products to WC_Product objects
        $this->products = $this->normalizeProducts($products);
        $this->title = $title;
        $this->id = $id ?? 'product-carousel-' . uniqid();
        $this->slidesPerView = $slidesPerView;
        $this->spaceBetween = $spaceBetween;
        $this->autoplay = $autoplay;
        $this->loop = $loop;
        $this->navigation = $navigation;
        $this->pagination = $pagination;
        $this->viewAllUrl = $viewAllUrl;
        $this->viewAllText = $viewAllText ?? __('View All', 'sega-woo-theme');
    }

    /**
     * Normalize products to Collection of WC_Product objects
     *
     * @param mixed $products
     * @return Collection
     */
    protected function normalizeProducts($products): Collection
    {
        if ($products instanceof Collection) {
            $productsArray = $products->all();
        } elseif (is_array($products)) {
            $productsArray = $products;
        } else {
            return collect();
        }

        $normalized = array_map(function ($product) {
            if ($product instanceof WC_Product) {
                return $product;
            }

            if (is_int($product) || is_string($product)) {
                return wc_get_product($product);
            }

            return null;
        }, $productsArray);

        // Filter out nulls
        $normalized = array_filter($normalized);

        return collect($normalized);
    }

    /**
     * Check if carousel has products
     *
     * @return bool
     */
    public function hasProducts(): bool
    {
        return $this->products->isNotEmpty();
    }

    /**
     * Get total product count
     *
     * @return int
     */
    public function productCount(): int
    {
        return $this->products->count();
    }

    /**
     * Check if there are enough products to enable loop
     *
     * @return bool
     */
    public function shouldLoop(): bool
    {
        // Only loop if we have more products than slides per view
        return $this->loop && $this->productCount() > $this->slidesPerView;
    }

    /**
     * Get Swiper configuration as JSON
     *
     * @return string
     */
    public function swiperConfig(): string
    {
        $config = [
            'slidesPerView' => 1,
            'spaceBetween' => $this->spaceBetween / 2,
            'loop' => $this->shouldLoop(),
            'breakpoints' => [
                // Mobile (>= 480px)
                480 => [
                    'slidesPerView' => 2,
                    'spaceBetween' => $this->spaceBetween / 2,
                ],
                // Tablet (>= 768px)
                768 => [
                    'slidesPerView' => min(3, $this->slidesPerView),
                    'spaceBetween' => $this->spaceBetween,
                ],
                // Desktop (>= 1024px)
                1024 => [
                    'slidesPerView' => min(4, $this->slidesPerView),
                    'spaceBetween' => $this->spaceBetween,
                ],
                // Large Desktop (>= 1280px)
                1280 => [
                    'slidesPerView' => $this->slidesPerView,
                    'spaceBetween' => $this->spaceBetween,
                ],
            ],
        ];

        if ($this->autoplay) {
            $config['autoplay'] = [
                'delay' => 4000,
                'disableOnInteraction' => false,
                'pauseOnMouseEnter' => true,
            ];
        }

        return json_encode($config);
    }

    /**
     * Render the component.
     *
     * @return View
     */
    public function render(): View
    {
        return view('components.product-carousel');
    }
}
