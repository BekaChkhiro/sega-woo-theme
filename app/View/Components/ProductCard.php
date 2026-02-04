<?php

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use WC_Product;

class ProductCard extends Component
{
    public WC_Product $product;

    public function __construct(WC_Product|int|null $product = null)
    {
        if ($product instanceof WC_Product) {
            $this->product = $product;
        } elseif (is_int($product)) {
            $this->product = wc_get_product($product);
        } else {
            $this->product = wc_get_product(get_the_ID());
        }
    }

    public function permalink(): string
    {
        return get_permalink($this->product->get_id());
    }

    public function title(): string
    {
        return $this->product->get_name();
    }

    public function isOnSale(): bool
    {
        return $this->product->is_on_sale();
    }

    public function isInStock(): bool
    {
        return $this->product->is_in_stock();
    }

    public function isSimple(): bool
    {
        return $this->product->is_type('simple');
    }

    public function thumbnail(): string
    {
        $id = $this->product->get_id();

        if (! has_post_thumbnail($id)) {
            return '';
        }

        return get_the_post_thumbnail($id, 'woocommerce_thumbnail', [
            'class' => 'h-full w-full object-cover transition-transform duration-300 group-hover:scale-105',
            'loading' => 'lazy',
        ]);
    }

    public function hasThumbnail(): bool
    {
        return has_post_thumbnail($this->product->get_id());
    }

    public function category(): ?string
    {
        $categories = get_the_terms($this->product->get_id(), 'product_cat');

        if (! $categories || is_wp_error($categories)) {
            return null;
        }

        return $categories[0]->name;
    }

    public function averageRating(): float
    {
        return (float) $this->product->get_average_rating();
    }

    public function ratingCount(): int
    {
        return (int) $this->product->get_rating_count();
    }

    public function regularPrice(): string
    {
        $price = $this->product->get_regular_price();

        return $price ? wc_price($price) : '';
    }

    public function salePrice(): string
    {
        $price = $this->product->get_sale_price();

        return $price ? wc_price($price) : '';
    }

    public function currentPrice(): string
    {
        $price = $this->product->get_price();

        return $price ? wc_price($price) : '';
    }

    public function priceHtml(): string
    {
        return $this->product->get_price_html();
    }

    public function addToCartUrl(): string
    {
        return esc_url($this->product->add_to_cart_url());
    }

    public function salePercentage(): int
    {
        $regular = (float) $this->product->get_regular_price();
        $sale = (float) $this->product->get_sale_price();

        if ($regular <= 0 || $sale <= 0) {
            return 0;
        }

        return (int) round((($regular - $sale) / $regular) * 100);
    }

    public function isFeatured(): bool
    {
        return $this->product->is_featured();
    }

    public function render(): View
    {
        return view('components.product-card');
    }
}
