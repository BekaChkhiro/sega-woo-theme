{{--
  Shop Sidebar - Filters for WooCommerce archive pages
  Includes: Category filter, Price range filter
  Responsive: Collapsible on mobile
--}}

@php
  // Convert InvokableComponentVariable objects to plain values
  $categories = is_callable($productCategories) ? $productCategories() : (is_array($productCategories) ? $productCategories : []);
  $priceRangeData = is_callable($priceRange) ? $priceRange() : (is_array($priceRange) ? $priceRange : ['min' => 0, 'max' => 0]);
  $priceFilterData = is_callable($priceFilter) ? $priceFilter() : (is_array($priceFilter) ? $priceFilter : ['min' => null, 'max' => null]);
  $shopPageUrl = is_callable($shopUrl) ? $shopUrl() : ($shopUrl ?? get_permalink(wc_get_page_id('shop')));
  $isCategoryPage = is_callable($isCategory) ? $isCategory() : ($isCategory ?? false);
  $totalProducts = is_callable($totalAllProducts) ? $totalAllProducts() : ($totalAllProducts ?? 0);
  $selectedCategorySlug = is_callable($selectedCategory ?? null) ? $selectedCategory() : ($selectedCategory ?? null);

  // Check if any category is active (either via archive page or query param)
  $hasCategoryFilter = $isCategoryPage || (isset($_GET['product_cat']) && $_GET['product_cat'] !== '');

  // Build "All Products" URL preserving other filters
  $allProductsUrl = $shopPageUrl;
  $filtersToPreserve = ['min_price', 'max_price', 'on_sale', 'in_stock', 'orderby'];
  foreach ($filtersToPreserve as $filter) {
    if (isset($_GET[$filter]) && $_GET[$filter] !== '') {
      $allProductsUrl = add_query_arg($filter, esc_attr($_GET[$filter]), $allProductsUrl);
    }
  }
@endphp

<div class="shop-sidebar" x-data="{ mobileOpen: false }">
  {{-- Mobile Filter Toggle Button --}}
  <button
    type="button"
    class="mb-4 flex w-full items-center justify-between rounded-lg bg-white px-4 py-3 text-left font-medium text-secondary-900 shadow-sm ring-1 ring-secondary-200 lg:hidden"
    @click="mobileOpen = !mobileOpen"
    :aria-expanded="mobileOpen"
  >
    <span class="flex items-center gap-2">
      <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
      </svg>
      {{ __('Filters', 'sage') }}
    </span>
    <svg
      class="h-5 w-5 transition-transform"
      :class="{ 'rotate-180': mobileOpen }"
      fill="none"
      viewBox="0 0 24 24"
      stroke="currentColor"
      stroke-width="2"
    >
      <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
    </svg>
  </button>

  {{-- Filter Panels Container --}}
  <div
    class="space-y-6"
    :class="{ 'hidden': !mobileOpen }"
    x-init="if (window.innerWidth >= 1024) mobileOpen = true"
    @resize.window="if (window.innerWidth >= 1024) mobileOpen = true"
  >
    {{-- Category Filter --}}
    @if (!empty($categories))
      <div class="rounded-lg bg-white p-4 shadow-sm ring-1 ring-secondary-100">
        <h3 class="mb-4 border-b border-secondary-200 pb-2 text-lg font-semibold text-secondary-900">
          {{ __('Categories', 'sage') }}
        </h3>

        <ul class="space-y-1">
          {{-- All Products Link --}}
          <li>
            <a
              href="{{ $allProductsUrl }}"
              class="flex items-center justify-between rounded-md px-2 py-1.5 text-sm transition-colors {{ !$hasCategoryFilter ? 'bg-primary-50 font-medium text-primary-700' : 'text-secondary-700 hover:bg-secondary-50 hover:text-secondary-900' }}"
            >
              <span>{{ __('All Products', 'sage') }}</span>
              <span class="text-xs text-secondary-500">{{ $totalProducts }}</span>
            </a>
          </li>

          @foreach ($categories as $category)
            @php
              $hasActiveChild = collect($category['children'] ?? [])->contains('active', true);
            @endphp
            <li x-data="{ expanded: {{ $category['active'] || $hasActiveChild ? 'true' : 'false' }} }">
              <div class="flex items-center">
                <a
                  href="{{ $category['url'] }}"
                  class="flex flex-1 items-center justify-between rounded-md px-2 py-1.5 text-sm transition-colors {{ $category['active'] ? 'bg-primary-50 font-medium text-primary-700' : 'text-secondary-700 hover:bg-secondary-50 hover:text-secondary-900' }}"
                >
                  <span>{{ $category['name'] }}</span>
                  <span class="text-xs text-secondary-500">({{ $category['count'] }})</span>
                </a>

                @if (!empty($category['children']))
                  <button
                    type="button"
                    class="ml-1 rounded p-1 text-secondary-400 hover:bg-secondary-100 hover:text-secondary-600"
                    @click="expanded = !expanded"
                    :aria-expanded="expanded"
                  >
                    <svg
                      class="h-4 w-4 transition-transform"
                      :class="{ 'rotate-90': expanded }"
                      fill="none"
                      viewBox="0 0 24 24"
                      stroke="currentColor"
                      stroke-width="2"
                    >
                      <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                  </button>
                @endif
              </div>

              {{-- Child Categories --}}
              @if (!empty($category['children']))
                <ul
                  class="ml-4 mt-1 space-y-1 border-l border-secondary-200 pl-2"
                  x-show="expanded"
                  x-collapse
                >
                  @foreach ($category['children'] as $child)
                    <li>
                      <a
                        href="{{ $child['url'] }}"
                        class="flex items-center justify-between rounded-md px-2 py-1 text-sm transition-colors {{ $child['active'] ? 'bg-primary-50 font-medium text-primary-700' : 'text-secondary-600 hover:bg-secondary-50 hover:text-secondary-900' }}"
                      >
                        <span>{{ $child['name'] }}</span>
                        <span class="text-xs text-secondary-400">({{ $child['count'] }})</span>
                      </a>
                    </li>
                  @endforeach
                </ul>
              @endif
            </li>
          @endforeach
        </ul>
      </div>
    @endif

    {{-- Price Filter --}}
    @if ($priceRangeData['max'] > 0)
      <div class="rounded-lg bg-white p-4 shadow-sm ring-1 ring-secondary-100">
        <h3 class="mb-4 border-b border-secondary-200 pb-2 text-lg font-semibold text-secondary-900">
          {{ __('Price', 'sage') }}
        </h3>

        <form method="get" action="{{ $shopPageUrl }}" class="space-y-4">
          {{-- Preserve existing query parameters --}}
          @if (isset($_GET['orderby']))
            <input type="hidden" name="orderby" value="{{ esc_attr($_GET['orderby']) }}">
          @endif
          @if (isset($_GET['s']))
            <input type="hidden" name="s" value="{{ esc_attr($_GET['s']) }}">
            <input type="hidden" name="post_type" value="product">
          @endif
          @if (isset($_GET['product_cat']) && $_GET['product_cat'] !== '')
            <input type="hidden" name="product_cat" value="{{ esc_attr($_GET['product_cat']) }}">
          @endif
          @if (isset($_GET['on_sale']))
            <input type="hidden" name="on_sale" value="{{ esc_attr($_GET['on_sale']) }}">
          @endif
          @if (isset($_GET['in_stock']))
            <input type="hidden" name="in_stock" value="{{ esc_attr($_GET['in_stock']) }}">
          @endif

          <div class="flex items-center gap-3">
            <div class="flex-1">
              <label for="min_price" class="sr-only">{{ __('Min price', 'sage') }}</label>
              <div class="relative">
                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-secondary-500">
                  {!! get_woocommerce_currency_symbol() !!}
                </span>
                <input
                  type="number"
                  name="min_price"
                  id="min_price"
                  class="w-full rounded-md border-secondary-300 pl-8 pr-3 py-2 text-sm placeholder-secondary-400 focus:border-primary-500 focus:ring-primary-500"
                  placeholder="{{ number_format($priceRangeData['min'], 0) }}"
                  value="{{ $priceFilterData['min'] ?? '' }}"
                  min="{{ floor($priceRangeData['min']) }}"
                  max="{{ ceil($priceRangeData['max']) }}"
                  step="1"
                >
              </div>
            </div>

            <span class="text-secondary-400">-</span>

            <div class="flex-1">
              <label for="max_price" class="sr-only">{{ __('Max price', 'sage') }}</label>
              <div class="relative">
                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-secondary-500">
                  {!! get_woocommerce_currency_symbol() !!}
                </span>
                <input
                  type="number"
                  name="max_price"
                  id="max_price"
                  class="w-full rounded-md border-secondary-300 pl-8 pr-3 py-2 text-sm placeholder-secondary-400 focus:border-primary-500 focus:ring-primary-500"
                  placeholder="{{ number_format($priceRangeData['max'], 0) }}"
                  value="{{ $priceFilterData['max'] ?? '' }}"
                  min="{{ floor($priceRangeData['min']) }}"
                  max="{{ ceil($priceRangeData['max']) }}"
                  step="1"
                >
              </div>
            </div>
          </div>

          {{-- Price Range Display --}}
          <p class="text-xs text-secondary-500">
            {{ __('Range:', 'sage') }}
            {!! wc_price(floor($priceRangeData['min'])) !!} - {!! wc_price(ceil($priceRangeData['max'])) !!}
          </p>

          <button
            type="submit"
            class="w-full rounded-md bg-primary-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
          >
            {{ __('Apply', 'sage') }}
          </button>

          @if ($priceFilterData['min'] !== null || $priceFilterData['max'] !== null)
            <a
              href="{{ remove_query_arg(['min_price', 'max_price']) }}"
              class="block text-center text-sm text-secondary-600 hover:text-primary-600"
            >
              {{ __('Clear price filter', 'sage') }}
            </a>
          @endif
        </form>
      </div>
    @endif

    {{-- Product Status Filter --}}
    <div class="rounded-lg bg-white p-4 shadow-sm ring-1 ring-secondary-100">
      <h3 class="mb-4 border-b border-secondary-200 pb-2 text-lg font-semibold text-secondary-900">
        {{ __('Availability', 'sage') }}
      </h3>

      <ul class="space-y-2">
        <li>
          <label class="flex cursor-pointer items-center gap-3">
            <input
              type="checkbox"
              name="on_sale"
              value="1"
              class="h-4 w-4 rounded border-secondary-300 text-primary-600 focus:ring-primary-500"
              {{ isset($_GET['on_sale']) ? 'checked' : '' }}
              onchange="this.form?.submit() || window.location.href = this.checked ? '{{ add_query_arg('on_sale', '1') }}' : '{{ remove_query_arg('on_sale') }}'"
            >
            <span class="text-sm text-secondary-700">{{ __('On Sale', 'sage') }}</span>
          </label>
        </li>
        <li>
          <label class="flex cursor-pointer items-center gap-3">
            <input
              type="checkbox"
              name="in_stock"
              value="1"
              class="h-4 w-4 rounded border-secondary-300 text-primary-600 focus:ring-primary-500"
              {{ isset($_GET['in_stock']) ? 'checked' : '' }}
              onchange="window.location.href = this.checked ? '{{ add_query_arg('in_stock', '1') }}' : '{{ remove_query_arg('in_stock') }}'"
            >
            <span class="text-sm text-secondary-700">{{ __('In Stock', 'sage') }}</span>
          </label>
        </li>
      </ul>
    </div>

    {{-- WordPress Widgets (if any) --}}
    @if (is_active_sidebar('sidebar-shop'))
      <div class="wordpress-widgets space-y-6">
        @php(dynamic_sidebar('sidebar-shop'))
      </div>
    @endif
  </div>
</div>
