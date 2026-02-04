<?php

namespace App\View\Components;

use Illuminate\View\Component;

class MiniCart extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Check if WooCommerce is active.
     */
    public function isWooCommerceActive(): bool
    {
        return function_exists('WC') && WC()->cart;
    }

    /**
     * Get cart items.
     */
    public function items(): array
    {
        if (!$this->isWooCommerceActive()) {
            return [];
        }

        $items = [];
        foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
            $product = $cart_item['data'];
            $items[] = [
                'key' => $cart_item_key,
                'product' => $product,
                'product_id' => $cart_item['product_id'],
                'variation_id' => $cart_item['variation_id'] ?? 0,
                'quantity' => $cart_item['quantity'],
                'name' => $product->get_name(),
                'price' => WC()->cart->get_product_price($product),
                'subtotal' => WC()->cart->get_product_subtotal($product, $cart_item['quantity']),
                'thumbnail' => $product->get_image('woocommerce_gallery_thumbnail'),
                'permalink' => $product->get_permalink(),
                'remove_url' => wc_get_cart_remove_url($cart_item_key),
            ];
        }

        return $items;
    }

    /**
     * Get cart item count.
     */
    public function itemCount(): int
    {
        if (!$this->isWooCommerceActive()) {
            return 0;
        }

        return WC()->cart->get_cart_contents_count();
    }

    /**
     * Get cart subtotal.
     */
    public function subtotal(): string
    {
        if (!$this->isWooCommerceActive()) {
            return '';
        }

        return WC()->cart->get_cart_subtotal();
    }

    /**
     * Check if cart is empty.
     */
    public function isEmpty(): bool
    {
        return $this->itemCount() === 0;
    }

    /**
     * Get cart URL.
     */
    public function cartUrl(): string
    {
        return function_exists('wc_get_cart_url') ? wc_get_cart_url() : '#';
    }

    /**
     * Get checkout URL.
     */
    public function checkoutUrl(): string
    {
        return function_exists('wc_get_checkout_url') ? wc_get_checkout_url() : '#';
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('components.mini-cart');
    }
}
