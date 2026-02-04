<?php

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use WC_Product;
use WC_Product_Variable;

class Price extends Component
{
    public WC_Product $product;
    public string $size;
    public bool $showBadge;

    /**
     * Create a new component instance.
     *
     * @param WC_Product|int|null $product The product or product ID
     * @param string $size Size variant: 'sm', 'md', 'lg', 'xl'
     * @param bool $showBadge Whether to show the sale percentage badge
     */
    public function __construct(
        WC_Product|int|null $product = null,
        string $size = 'md',
        bool $showBadge = false
    ) {
        if ($product instanceof WC_Product) {
            $this->product = $product;
        } elseif (is_int($product)) {
            $this->product = wc_get_product($product);
        } else {
            global $product;
            $this->product = $product instanceof WC_Product
                ? $product
                : wc_get_product(get_the_ID());
        }

        $this->size = $size;
        $this->showBadge = $showBadge;
    }

    /**
     * Check if the product is on sale.
     */
    public function isOnSale(): bool
    {
        return $this->product->is_on_sale();
    }

    /**
     * Check if this is a variable product.
     */
    public function isVariable(): bool
    {
        return $this->product instanceof WC_Product_Variable;
    }

    /**
     * Get the regular price (formatted).
     */
    public function regularPrice(): string
    {
        $price = $this->product->get_regular_price();
        return $price ? wc_price($price) : '';
    }

    /**
     * Get the sale price (formatted).
     */
    public function salePrice(): string
    {
        $price = $this->product->get_sale_price();
        return $price ? wc_price($price) : '';
    }

    /**
     * Get the current price (formatted).
     */
    public function currentPrice(): string
    {
        $price = $this->product->get_price();
        return $price ? wc_price($price) : '';
    }

    /**
     * Get the full price HTML from WooCommerce.
     * This handles all edge cases including variable products.
     */
    public function priceHtml(): string
    {
        return $this->product->get_price_html();
    }

    /**
     * Get the sale discount percentage.
     */
    public function salePercentage(): int
    {
        if (! $this->product->is_on_sale()) {
            return 0;
        }

        // For variable products, calculate from the variation with biggest discount
        if ($this->isVariable()) {
            return $this->getVariableMaxDiscount();
        }

        $regular = (float) $this->product->get_regular_price();
        $sale = (float) $this->product->get_sale_price();

        if ($regular <= 0 || $sale <= 0) {
            return 0;
        }

        return (int) round((($regular - $sale) / $regular) * 100);
    }

    /**
     * Get the maximum discount percentage for variable products.
     */
    protected function getVariableMaxDiscount(): int
    {
        if (! $this->product instanceof WC_Product_Variable) {
            return 0;
        }

        $maxDiscount = 0;
        $variations = $this->product->get_available_variations();

        foreach ($variations as $variation) {
            $regularPrice = (float) ($variation['display_regular_price'] ?? 0);
            $salePrice = (float) ($variation['display_price'] ?? 0);

            if ($regularPrice > 0 && $salePrice > 0 && $salePrice < $regularPrice) {
                $discount = (int) round((($regularPrice - $salePrice) / $regularPrice) * 100);
                $maxDiscount = max($maxDiscount, $discount);
            }
        }

        return $maxDiscount;
    }

    /**
     * Get price range for variable products.
     */
    public function priceRange(): array
    {
        if (! $this->product instanceof WC_Product_Variable) {
            return [
                'min' => $this->product->get_price(),
                'max' => $this->product->get_price(),
                'min_formatted' => wc_price($this->product->get_price()),
                'max_formatted' => wc_price($this->product->get_price()),
                'has_range' => false,
            ];
        }

        $minPrice = $this->product->get_variation_price('min');
        $maxPrice = $this->product->get_variation_price('max');

        return [
            'min' => $minPrice,
            'max' => $maxPrice,
            'min_formatted' => wc_price($minPrice),
            'max_formatted' => wc_price($maxPrice),
            'has_range' => $minPrice !== $maxPrice,
        ];
    }

    /**
     * Check if variable product has a price range.
     */
    public function hasPriceRange(): bool
    {
        if (! $this->isVariable()) {
            return false;
        }

        $range = $this->priceRange();
        return $range['has_range'];
    }

    /**
     * Get size-based CSS classes for the regular/current price.
     */
    public function priceClasses(): string
    {
        return match ($this->size) {
            'sm' => 'text-sm font-semibold',
            'md' => 'text-base sm:text-lg font-bold',
            'lg' => 'text-xl sm:text-2xl font-bold',
            'xl' => 'text-2xl sm:text-3xl font-bold',
            default => 'text-base sm:text-lg font-bold',
        };
    }

    /**
     * Get size-based CSS classes for the crossed-out regular price.
     */
    public function regularPriceClasses(): string
    {
        return match ($this->size) {
            'sm' => 'text-xs',
            'md' => 'text-xs sm:text-sm',
            'lg' => 'text-sm sm:text-base',
            'xl' => 'text-base sm:text-lg',
            default => 'text-xs sm:text-sm',
        };
    }

    /**
     * Get size-based CSS classes for the sale badge.
     */
    public function badgeClasses(): string
    {
        return match ($this->size) {
            'sm' => 'text-[10px] px-1.5 py-0.5',
            'md' => 'text-xs px-2 py-0.5',
            'lg' => 'text-xs px-2.5 py-1',
            'xl' => 'text-sm px-2.5 py-0.5',
            default => 'text-xs px-2 py-0.5',
        };
    }

    /**
     * Check if the product has a price.
     */
    public function hasPrice(): bool
    {
        return $this->product->get_price() !== '';
    }

    /**
     * Render the component.
     */
    public function render(): View
    {
        return view('components.price');
    }
}
