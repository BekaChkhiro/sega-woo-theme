{{--
  Upsell Products Section

  @param array $products - Array of upsell product data from View Composer
  @param string $title - Section title (optional)
  @param int $columns - Number of columns on large screens (default: 4)
--}}

@php
  // Default values for variables passed via @include
  $products = $products ?? [];
  $title = $title ?? __('You may also like', 'sega-woo-theme');
  $columns = $columns ?? 4;
@endphp

@if (!empty($products))
  <section class="upsell-products mt-16 pt-16">
    {{-- Section Header --}}
    <div class="mb-8">
      <p class="text-sm font-medium uppercase tracking-wider text-primary-600">{{ __('Recommended', 'sega-woo-theme') }}</p>
      <h2 class="mt-1 text-2xl font-bold tracking-tight text-secondary-900 sm:text-3xl">
        {{ $title }}
      </h2>
    </div>

    {{-- Products Grid --}}
    <div
      @class([
        'grid gap-4 lg:gap-6',
        'grid-cols-2 sm:grid-cols-3' => $columns === 4,
        'grid-cols-2 sm:grid-cols-2' => $columns === 3,
        'grid-cols-2' => $columns === 2,
        'lg:grid-cols-4' => $columns === 4,
        'lg:grid-cols-3' => $columns === 3,
        'lg:grid-cols-2' => $columns === 2,
      ])
    >
      @foreach ($products as $upsellProduct)
        @php
          $upsellWcProduct = wc_get_product($upsellProduct['id']);
        @endphp

        @if ($upsellWcProduct)
          <x-product-card :product="$upsellWcProduct" />
        @endif
      @endforeach
    </div>
  </section>
@endif
