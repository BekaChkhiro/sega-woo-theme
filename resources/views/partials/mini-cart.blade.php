@php
  $cart = WC()->cart;
  $itemCount = $cart ? $cart->get_cart_contents_count() : 0;
  $subtotal = $cart ? $cart->get_cart_subtotal() : '';
  $isEmpty = $itemCount === 0;
  $cartUrl = wc_get_cart_url();
  $checkoutUrl = wc_get_checkout_url();
@endphp

<div
    x-data="miniCart()"
    @click.away="close()"
    class="relative"
>
    {{-- Cart Toggle Button --}}
    <button
        @click="toggle()"
        type="button"
        class="relative flex h-10 w-10 items-center justify-center rounded-full text-secondary-600 transition-colors hover:bg-secondary-100 hover:text-secondary-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
        :aria-expanded="open"
        aria-label="{{ __('Shopping cart', 'sage') }}"
    >
        {{-- Cart Icon --}}
        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
        </svg>

        {{-- Item Count Badge --}}
        <span
            class="mini-cart-count absolute -right-1 -top-1 flex h-5 w-5 items-center justify-center rounded-full bg-primary-600 text-xs font-medium text-white transition-transform {{ $itemCount === 0 ? 'scale-0' : 'scale-100' }}"
        >{{ $itemCount > 99 ? '99+' : $itemCount }}</span>
    </button>

    {{-- Dropdown Panel --}}
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-1"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-1"
        class="absolute right-0 top-full z-50 mt-2 w-80 rounded-lg bg-white shadow-lg ring-1 ring-black ring-opacity-5 sm:w-96"
        x-cloak
    >
        {{-- Loading Overlay --}}
        <div
            x-show="loading"
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-100"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="absolute inset-0 z-10 flex items-center justify-center rounded-lg bg-white/80 backdrop-blur-sm"
        >
            <svg class="h-8 w-8 animate-spin text-primary-600" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>

        {{-- Header --}}
        <div class="border-b border-secondary-200 px-4 py-3">
            <h3 class="text-sm font-semibold text-secondary-900">
                {{ __('Shopping Cart', 'sage') }}
                <span class="ml-1 text-secondary-500" x-show="itemCount > 0">
                    (<span class="mini-cart-count-text">{{ $itemCount }}</span>)
                </span>
            </h3>
        </div>

        {{-- Cart Items --}}
        <div class="mini-cart-items max-h-80 overflow-y-auto">
            @if ($isEmpty)
                {{-- Empty Cart State --}}
                <div class="flex flex-col items-center justify-center py-8 text-center">
                    <svg class="mb-3 h-12 w-12 text-secondary-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" />
                    </svg>
                    <p class="text-sm text-secondary-500">{{ __('Your cart is empty', 'sage') }}</p>
                    <a
                        href="{{ wc_get_page_permalink('shop') }}"
                        class="mt-3 text-sm font-medium text-primary-600 hover:text-primary-700"
                    >
                        {{ __('Continue Shopping', 'sage') }} &rarr;
                    </a>
                </div>
            @else
                {{-- Cart Items List --}}
                <ul class="divide-y divide-secondary-100 px-4">
                    @foreach ($cart->get_cart() as $cart_item_key => $cart_item)
                        @php
                          $product = $cart_item['data'];
                          if (!$product || !$product->exists()) continue;
                        @endphp
                        <li class="mini-cart-item flex gap-3 py-3" data-key="{{ $cart_item_key }}">
                            {{-- Product Thumbnail --}}
                            <a href="{{ $product->get_permalink() }}" class="flex-shrink-0">
                                <div class="h-16 w-16 overflow-hidden rounded-md bg-secondary-100">
                                    {!! $product->get_image('woocommerce_gallery_thumbnail', ['loading' => 'lazy', 'decoding' => 'async']) !!}
                                </div>
                            </a>

                            {{-- Product Details --}}
                            <div class="flex flex-1 flex-col">
                                <div class="flex justify-between">
                                    <a
                                        href="{{ $product->get_permalink() }}"
                                        class="text-sm font-medium text-secondary-900 hover:text-primary-600 line-clamp-2"
                                    >
                                        {{ $product->get_name() }}
                                    </a>
                                    {{-- Remove Button (uses event delegation via .remove-from-cart class) --}}
                                    <button
                                        type="button"
                                        class="remove-from-cart ml-2 flex-shrink-0 text-secondary-400 hover:text-red-500 transition-colors"
                                        data-cart-item-key="{{ $cart_item_key }}"
                                        aria-label="{{ __('Remove item', 'sage') }}"
                                    >
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                                <div class="mt-1 flex items-center justify-between text-sm">
                                    <span class="text-secondary-500">
                                        {{ __('Qty:', 'sage') }} {{ $cart_item['quantity'] }}
                                    </span>
                                    <span class="font-medium text-secondary-900">
                                        {!! $cart->get_product_subtotal($product, $cart_item['quantity']) !!}
                                    </span>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        {{-- Footer --}}
        <div class="mini-cart-footer {{ $isEmpty ? 'hidden' : '' }}">
            <div class="border-t border-secondary-200 bg-secondary-50 px-4 py-4">
                {{-- Subtotal --}}
                <div class="mb-4 flex items-center justify-between">
                    <span class="text-sm font-medium text-secondary-900">{{ __('Subtotal', 'sage') }}</span>
                    <span class="mini-cart-subtotal text-base font-semibold text-secondary-900">{!! $subtotal !!}</span>
                </div>

                {{-- Action Buttons --}}
                <div class="grid grid-cols-2 gap-3">
                    <a
                        href="{{ $cartUrl }}"
                        class="inline-flex items-center justify-center rounded-md border border-secondary-300 bg-white px-4 py-2 text-sm font-medium text-secondary-700 shadow-sm transition-colors hover:bg-secondary-50 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
                    >
                        {{ __('View Cart', 'sage') }}
                    </a>
                    <a
                        href="{{ $checkoutUrl }}"
                        class="inline-flex items-center justify-center rounded-md border border-transparent bg-primary-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition-colors hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
                    >
                        {{ __('Checkout', 'sage') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
