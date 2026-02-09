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
        aria-label="{{ __('Shopping cart', 'sega-woo-theme') }}"
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
        class="absolute right-0 top-full z-50 mt-2 w-[calc(100vw-2rem)] max-w-80 overflow-hidden rounded-2xl border border-secondary-200 bg-white shadow-xl sm:max-w-96"
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
            class="absolute inset-0 z-10 flex items-center justify-center rounded-2xl bg-white/80 backdrop-blur-sm"
        >
            <svg class="h-8 w-8 animate-spin text-primary-600" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>

        {{-- Header --}}
        <div class="border-b border-secondary-100 bg-secondary-50/50 px-5 py-4">
            <div class="flex items-center justify-between">
                <h3 class="text-base font-bold text-secondary-900">
                    {{ __('Shopping Cart', 'sega-woo-theme') }}
                </h3>
                <span class="mini-cart-count-text text-sm text-secondary-500" x-show="itemCount > 0">
                    {{ sprintf(_n('%d item', '%d items', $itemCount, 'sega-woo-theme'), $itemCount) }}
                </span>
            </div>
        </div>

        {{-- Cart Items --}}
        <div class="mini-cart-items max-h-80 overflow-y-auto">
            @if ($isEmpty)
                {{-- Empty Cart State --}}
                <div class="flex flex-col items-center justify-center px-6 py-10 text-center">
                    <div class="mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-gradient-to-br from-secondary-100 to-secondary-50">
                        <svg class="h-10 w-10 text-secondary-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" />
                        </svg>
                    </div>
                    <p class="mb-1 text-sm font-medium text-secondary-900">{{ __('Your cart is empty', 'sega-woo-theme') }}</p>
                    <p class="mb-4 text-xs text-secondary-500">{{ __('Add items to get started', 'sega-woo-theme') }}</p>
                    <a
                        href="{{ wc_get_page_permalink('shop') }}"
                        class="inline-flex items-center gap-1.5 rounded-full bg-primary-600 px-5 py-2.5 text-sm font-semibold text-white shadow-md shadow-primary-600/20 transition-all hover:bg-primary-700 hover:shadow-lg hover:shadow-primary-600/30"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                        </svg>
                        {{ __('Start Shopping', 'sega-woo-theme') }}
                    </a>
                </div>
            @else
                {{-- Cart Items List --}}
                <ul class="divide-y divide-secondary-100">
                    @foreach ($cart->get_cart() as $cart_item_key => $cart_item)
                        @php
                          $product = $cart_item['data'];
                          if (!$product || !$product->exists()) continue;
                        @endphp
                        <li class="mini-cart-item group p-4" data-key="{{ $cart_item_key }}">
                            <div class="flex gap-4">
                                {{-- Product Thumbnail --}}
                                <a href="{{ $product->get_permalink() }}" class="flex-shrink-0">
                                    <div class="h-20 w-20 overflow-hidden rounded-xl bg-secondary-100 [&_img]:h-full [&_img]:w-full [&_img]:object-cover">
                                        {!! $product->get_image('woocommerce_gallery_thumbnail', ['loading' => 'lazy', 'decoding' => 'async']) !!}
                                    </div>
                                </a>

                                {{-- Product Details --}}
                                <div class="flex flex-1 flex-col justify-center">
                                    <div class="flex items-start justify-between gap-2">
                                        <a
                                            href="{{ $product->get_permalink() }}"
                                            class="text-sm font-medium text-secondary-900 transition-colors hover:text-primary-600 line-clamp-2"
                                        >
                                            {{ $product->get_name() }}
                                        </a>
                                        {{-- Remove Button --}}
                                        <button
                                            type="button"
                                            class="remove-from-cart flex-shrink-0 rounded-full p-1 text-secondary-400 transition-colors hover:bg-red-50 hover:text-red-500"
                                            data-cart-item-key="{{ $cart_item_key }}"
                                            aria-label="{{ __('Remove item', 'sega-woo-theme') }}"
                                        >
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>

                                    {{-- Variation Data --}}
                                    @php
                                      $item_data = wc_get_formatted_cart_item_data($cart_item);
                                    @endphp
                                    @if ($item_data)
                                      <div class="mt-1 text-xs text-secondary-500 [&_dl]:flex [&_dl]:flex-wrap [&_dl]:gap-x-2 [&_dd]:font-medium [&_dd]:text-secondary-600 [&_dt]:after:content-[':']">
                                        {!! $item_data !!}
                                      </div>
                                    @endif

                                    <div class="mt-2 flex items-center justify-between">
                                        <span class="inline-flex items-center gap-0.5 rounded-full bg-secondary-100 px-2.5 py-1 text-xs font-medium text-secondary-600">
                                            <span class="text-secondary-400">×</span>
                                            {{ $cart_item['quantity'] }}
                                        </span>
                                        <span class="text-base font-bold text-secondary-900">
                                            {!! $cart->get_product_subtotal($product, $cart_item['quantity']) !!}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        {{-- Footer --}}
        <div class="mini-cart-footer {{ $isEmpty ? 'hidden' : '' }}">
            <div class="border-t border-secondary-200 bg-white p-5">
                {{-- Subtotal --}}
                <div class="mb-4 flex items-center justify-between">
                    <span class="text-sm text-secondary-600">{{ __('Subtotal', 'sega-woo-theme') }}</span>
                    <span class="mini-cart-subtotal text-base font-bold text-secondary-900">{!! $subtotal !!}</span>
                </div>

                {{-- Action Buttons --}}
                <div class="grid grid-cols-2 gap-3">
                    <a
                        href="{{ $cartUrl }}"
                        class="inline-flex items-center justify-center gap-1.5 rounded-xl border border-secondary-200 bg-white px-4 py-3 text-sm font-semibold text-secondary-700 shadow-sm transition-all hover:bg-secondary-50 hover:shadow focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" />
                        </svg>
                        {{ __('View Cart', 'sega-woo-theme') }}
                    </a>
                    <a
                        href="{{ $checkoutUrl }}"
                        class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-primary-600 px-4 py-3 text-sm font-semibold text-white shadow-md shadow-primary-600/20 transition-all hover:bg-primary-700 hover:shadow-lg hover:shadow-primary-600/30 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
                    >
                        {{ __('Checkout', 'sega-woo-theme') }}
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
