{{--
  Related Products Section

  @param array $products - Array of related product data from View Composer
  @param string $title - Section title (optional)
  @param int $columns - Number of columns on large screens (default: 4)
--}}

@php
  // Default values for variables passed via @include
  $products = $products ?? [];
  $title = $title ?? __('Related Products', 'sage');
  $columns = $columns ?? 4;
@endphp

@if (!empty($products))
  <section class="related-products mt-16 pt-16">
    {{-- Section Header --}}
    <div class="mb-8 flex items-end justify-between">
      <div>
        <p class="text-sm font-medium uppercase tracking-wider text-primary-600">{{ __('Discover More', 'sage') }}</p>
        <h2 class="mt-1 text-2xl font-bold tracking-tight text-secondary-900 sm:text-3xl">
          {{ $title }}
        </h2>
      </div>

      @if (count($products) > $columns)
        <a
          href="{{ wc_get_page_permalink('shop') }}"
          class="hidden items-center gap-2 rounded-lg bg-secondary-100 px-4 py-2 text-sm font-semibold text-secondary-700 transition-colors hover:bg-secondary-200 hover:text-secondary-900 sm:inline-flex"
        >
          {{ __('View all', 'sage') }}
          <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
          </svg>
        </a>
      @endif
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
      @foreach ($products as $relatedProduct)
        @php
          $relatedWcProduct = wc_get_product($relatedProduct['id']);
        @endphp

        @if ($relatedWcProduct)
          <x-product-card :product="$relatedWcProduct" />
        @endif
      @endforeach
    </div>

    {{-- Mobile "View All" Link --}}
    @if (count($products) > $columns)
      <div class="mt-8 text-center sm:hidden">
        <a
          href="{{ wc_get_page_permalink('shop') }}"
          class="inline-flex items-center gap-2 rounded-lg bg-secondary-100 px-6 py-3 text-sm font-semibold text-secondary-700 transition-colors hover:bg-secondary-200"
        >
          {{ __('View all products', 'sage') }}
          <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
          </svg>
        </a>
      </div>
    @endif
  </section>
@endif
