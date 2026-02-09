<?php

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use WC_Product;

class StarRating extends Component
{
    public ?WC_Product $product;
    public float $rating;
    public int $count;
    public int $reviewCount;
    public string $size;
    public bool $showCount;
    public bool $showLink;
    public string $linkUrl;
    public string $linkText;

    /**
     * Create a new component instance.
     *
     * @param WC_Product|int|null $product The product or product ID
     * @param float|null $rating Direct rating value (0-5) - overrides product rating
     * @param int|null $count Direct rating count - overrides product count
     * @param int|null $reviewCount Direct review count - overrides product review count
     * @param string $size Size variant: 'sm', 'md', 'lg', 'xl'
     * @param bool $showCount Whether to show the rating count
     * @param bool $showLink Whether to show the "read reviews" or "write review" link
     * @param string $linkUrl Custom link URL (defaults to #reviews)
     * @param string $linkText Custom link text
     */
    public function __construct(
        WC_Product|int|null $product = null,
        ?float $rating = null,
        ?int $count = null,
        ?int $reviewCount = null,
        string $size = 'md',
        bool $showCount = true,
        bool $showLink = false,
        string $linkUrl = '#reviews',
        string $linkText = ''
    ) {
        // Get product if provided
        if ($product instanceof WC_Product) {
            $this->product = $product;
        } elseif (is_int($product)) {
            $this->product = wc_get_product($product);
        } else {
            global $product;
            $this->product = $product instanceof WC_Product ? $product : null;
        }

        // Set rating - use provided value or get from product
        if ($rating !== null) {
            $this->rating = max(0, min(5, $rating));
        } elseif ($this->product) {
            $this->rating = (float) $this->product->get_average_rating();
        } else {
            $this->rating = 0;
        }

        // Set count - use provided value or get from product
        if ($count !== null) {
            $this->count = max(0, $count);
        } elseif ($this->product) {
            $this->count = (int) $this->product->get_rating_count();
        } else {
            $this->count = 0;
        }

        // Set review count - use provided value or get from product
        if ($reviewCount !== null) {
            $this->reviewCount = max(0, $reviewCount);
        } elseif ($this->product) {
            $this->reviewCount = (int) $this->product->get_review_count();
        } else {
            $this->reviewCount = $this->count;
        }

        $this->size = $size;
        $this->showCount = $showCount;
        $this->showLink = $showLink;
        $this->linkUrl = $linkUrl;
        $this->linkText = $linkText;
    }

    /**
     * Check if there are any ratings.
     */
    public function hasRating(): bool
    {
        return $this->count > 0 && $this->rating > 0;
    }

    /**
     * Check if reviews are enabled for the product.
     */
    public function reviewsEnabled(): bool
    {
        if (! $this->product) {
            return true; // Allow display if using direct values
        }

        return $this->product->get_reviews_allowed();
    }

    /**
     * Get the number of full stars to display.
     */
    public function fullStars(): int
    {
        return (int) floor($this->rating);
    }

    /**
     * Check if we should show a half star.
     */
    public function hasHalfStar(): bool
    {
        $decimal = $this->rating - floor($this->rating);
        return $decimal >= 0.25 && $decimal < 0.75;
    }

    /**
     * Get the number of empty stars to display.
     */
    public function emptyStars(): int
    {
        $fullStars = $this->fullStars();
        $halfStar = $this->hasHalfStar() ? 1 : 0;

        // If rating has high decimal (>= 0.75), it rounds up to a full star
        if (($this->rating - floor($this->rating)) >= 0.75) {
            $fullStars++;
        }

        return max(0, 5 - $fullStars - $halfStar);
    }

    /**
     * Get the rating as a percentage (for CSS width).
     */
    public function ratingPercentage(): float
    {
        return ($this->rating / 5) * 100;
    }

    /**
     * Get the formatted rating value.
     */
    public function formattedRating(): string
    {
        return number_format($this->rating, 1);
    }

    /**
     * Get the link text based on whether there are reviews.
     */
    public function getLinkText(): string
    {
        if ($this->linkText) {
            return $this->linkText;
        }

        if ($this->hasRating()) {
            return __('Read reviews', 'sega-woo-theme');
        }

        return __('Be the first to review', 'sega-woo-theme');
    }

    /**
     * Get size-based CSS classes for the star container.
     */
    public function containerClasses(): string
    {
        return match ($this->size) {
            'sm' => 'gap-0.5',
            'md' => 'gap-0.5 sm:gap-1',
            'lg' => 'gap-1',
            'xl' => 'gap-1',
            default => 'gap-0.5 sm:gap-1',
        };
    }

    /**
     * Get size-based CSS classes for individual stars.
     */
    public function starClasses(): string
    {
        return match ($this->size) {
            'sm' => 'h-3 w-3',
            'md' => 'h-3.5 w-3.5 sm:h-4 sm:w-4',
            'lg' => 'h-4 w-4 sm:h-5 sm:w-5',
            'xl' => 'h-5 w-5 sm:h-6 sm:w-6',
            default => 'h-3.5 w-3.5 sm:h-4 sm:w-4',
        };
    }

    /**
     * Get size-based CSS classes for the count text.
     */
    public function countClasses(): string
    {
        return match ($this->size) {
            'sm' => 'text-[10px]',
            'md' => 'text-[10px] sm:text-xs',
            'lg' => 'text-xs sm:text-sm',
            'xl' => 'text-sm',
            default => 'text-[10px] sm:text-xs',
        };
    }

    /**
     * Get size-based CSS classes for the link text.
     */
    public function linkClasses(): string
    {
        return match ($this->size) {
            'sm' => 'text-[10px]',
            'md' => 'text-xs sm:text-sm',
            'lg' => 'text-sm',
            'xl' => 'text-sm',
            default => 'text-xs sm:text-sm',
        };
    }

    /**
     * Render the component.
     */
    public function render(): View
    {
        return view('components.star-rating');
    }
}
