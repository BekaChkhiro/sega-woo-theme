{{--
  Shop Sidebar - Unified Filters for WooCommerce archive pages
  Includes: Price range filter, Category filter
  Features: Staged changes with Apply/Clear buttons
  Responsive: Collapsible on mobile
--}}

@php
  // Convert InvokableComponentVariable objects to plain values
  // Use filterCategories() which returns subcategories on category pages
  $categories = is_callable($filterCategories) ? $filterCategories() : (is_array($filterCategories) ? $filterCategories : []);
  $priceRangeData = is_callable($priceRange) ? $priceRange() : (is_array($priceRange) ? $priceRange : ['min' => 0, 'max' => 0]);
  $priceFilterData = is_callable($priceFilter) ? $priceFilter() : (is_array($priceFilter) ? $priceFilter : ['min' => null, 'max' => null]);
  $shopPageUrl = is_callable($shopUrl) ? $shopUrl() : ($shopUrl ?? get_permalink(wc_get_page_id('shop')));
  $isCategoryPage = is_callable($isCategory) ? $isCategory() : ($isCategory ?? false);
  $totalProducts = is_callable($totalAllProducts) ? $totalAllProducts() : ($totalAllProducts ?? 0);

  // Get parent category info when on a category page
  $parentCategory = is_callable($parentCategoryInfo) ? $parentCategoryInfo() : ($parentCategoryInfo ?? null);

  // Get all selected category IDs (supports multi-select)
  $selectedCategoryIds = [];
  if (isset($_GET['cat_ids']) && $_GET['cat_ids'] !== '') {
    $rawIds = array_filter(array_map('trim', explode(',', wc_clean(wp_unslash($_GET['cat_ids'])))));
    foreach ($rawIds as $rawId) {
      $termId = absint($rawId);
      if ($termId > 0 && !in_array($termId, $selectedCategoryIds, true)) {
        // Verify term exists
        $term = get_term($termId, 'product_cat');
        if ($term && !is_wp_error($term)) {
          $selectedCategoryIds[] = $termId;
        }
      }
    }
  }

  // Build category tree for smart subcategory logic (using IDs)
  $categoryTree = [];
  foreach ($categories as $cat) {
    if (!empty($cat['children'])) {
      $categoryTree[$cat['id']] = array_values(array_map(fn($child) => $child['id'], $cat['children']));
    }
  }

  // Price values
  $minPrice = floor($priceRangeData['min']);
  $maxPrice = ceil($priceRangeData['max']);
  $currentMin = $priceFilterData['min'] ?? null;
  $currentMax = $priceFilterData['max'] ?? null;
  $currencySymbol = get_woocommerce_currency_symbol();

  // Availability state
  $onSaleActive = isset($_GET['on_sale']);
  $inStockActive = isset($_GET['in_stock']);
@endphp

<div
  class="shop-sidebar"
  x-data="shopFilters({
    shopUrl: '{{ esc_url($shopPageUrl) }}',
    ajaxUrl: '{{ admin_url('admin-ajax.php') }}',
    nonce: '{{ wp_create_nonce('filter_products_nonce') }}',
    initialCategories: {{ json_encode(array_values($selectedCategoryIds)) }},
    categoryTree: {{ json_encode($categoryTree) }},
    priceMin: {{ $minPrice }},
    priceMax: {{ $maxPrice }},
    currentMinPrice: {{ $currentMin !== null ? $currentMin : 'null' }},
    currentMaxPrice: {{ $currentMax !== null ? $currentMax : 'null' }},
    priceStep: 1,
    currencySymbol: '{!! $currencySymbol !!}',
    onSale: {{ $onSaleActive ? 'true' : 'false' }},
    inStock: {{ $inStockActive ? 'true' : 'false' }},
    totalPages: {{ $totalPages ?? 1 }},
    isCategoryPage: {{ $isCategoryPage ? 'true' : 'false' }},
    parentCategoryId: {{ $parentCategory ? $parentCategory['id'] : 'null' }},
    parentCategoryUrl: '{{ $parentCategory ? esc_url($parentCategory['url']) : '' }}'
  })"
  x-init="init()"
>
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

    {{-- ==================== PRICE FILTER ==================== --}}
    @if ($priceRangeData['max'] > 0)
      <div class="rounded-lg bg-white p-4 shadow-sm ring-1 ring-secondary-100">
        <h3 class="mb-4 border-b border-secondary-200 pb-2 text-lg font-semibold text-secondary-900">
          {{ __('Price', 'sage') }}
        </h3>

        {{-- Current Range Display --}}
        <div class="mb-4 flex items-center justify-between gap-2 text-sm">
          <span class="font-medium text-secondary-700">{!! $currencySymbol !!}<span x-text="stagedMinPrice.toLocaleString()"></span></span>
          <span class="text-secondary-400">-</span>
          <span class="font-medium text-secondary-700">{!! $currencySymbol !!}<span x-text="stagedMaxPrice.toLocaleString()"></span></span>
        </div>

        {{-- Slider Track --}}
        <div
          class="price-range-slider relative mb-6 h-2 cursor-pointer"
          x-ref="priceTrack"
          @click="onPriceTrackClick($event)"
        >
          {{-- Background Track --}}
          <div class="absolute inset-0 rounded-full bg-secondary-200"></div>

          {{-- Active Range --}}
          <div
            class="absolute top-0 h-full rounded-full bg-primary-500"
            :style="`left: ${minPercent}%; right: ${100 - maxPercent}%`"
          ></div>

          {{-- Min Handle --}}
          <div
            class="price-range-handle absolute top-1/2 z-10 h-5 w-5 -translate-x-1/2 -translate-y-1/2 cursor-grab rounded-full border-2 border-primary-500 bg-white shadow-md transition-shadow hover:shadow-lg active:cursor-grabbing active:shadow-lg"
            :class="{ 'ring-2 ring-primary-300': isDragging === 'min' }"
            :style="`left: ${minPercent}%`"
            data-handle="min"
            @mousedown="startPriceDrag('min', $event)"
            @touchstart.prevent="startPriceDrag('min', $event)"
            role="slider"
            :aria-valuenow="stagedMinPrice"
            :aria-valuemin="priceMin"
            :aria-valuemax="stagedMaxPrice"
            aria-label="{{ __('Minimum price', 'sage') }}"
            tabindex="0"
            @keydown.left.prevent="stagedMinPrice = Math.max(priceMin, stagedMinPrice - priceStep)"
            @keydown.right.prevent="stagedMinPrice = Math.min(stagedMaxPrice - priceStep, stagedMinPrice + priceStep)"
          ></div>

          {{-- Max Handle --}}
          <div
            class="price-range-handle absolute top-1/2 z-10 h-5 w-5 -translate-x-1/2 -translate-y-1/2 cursor-grab rounded-full border-2 border-primary-500 bg-white shadow-md transition-shadow hover:shadow-lg active:cursor-grabbing active:shadow-lg"
            :class="{ 'ring-2 ring-primary-300': isDragging === 'max' }"
            :style="`left: ${maxPercent}%`"
            data-handle="max"
            @mousedown="startPriceDrag('max', $event)"
            @touchstart.prevent="startPriceDrag('max', $event)"
            role="slider"
            :aria-valuenow="stagedMaxPrice"
            :aria-valuemin="stagedMinPrice"
            :aria-valuemax="priceMax"
            aria-label="{{ __('Maximum price', 'sage') }}"
            tabindex="0"
            @keydown.left.prevent="stagedMaxPrice = Math.max(stagedMinPrice + priceStep, stagedMaxPrice - priceStep)"
            @keydown.right.prevent="stagedMaxPrice = Math.min(priceMax, stagedMaxPrice + priceStep)"
          ></div>
        </div>

        {{-- Range Info --}}
        <p class="text-xs text-secondary-500">
          {{ __('Range:', 'sage') }}
          {!! wc_price($minPrice) !!} - {!! wc_price($maxPrice) !!}
        </p>
      </div>
    @endif

    {{-- ==================== CATEGORY FILTER ==================== --}}
    @if (!empty($categories) || $isCategoryPage)
      <div class="rounded-lg bg-white p-4 shadow-sm ring-1 ring-secondary-100">
        <div class="mb-4 flex items-center justify-between border-b border-secondary-200 pb-2">
          <h3 class="text-lg font-semibold text-secondary-900">
            @if ($isCategoryPage && $parentCategory)
              {{ __('Subcategories', 'sage') }}
            @else
              {{ __('Categories', 'sage') }}
            @endif
          </h3>
          <span
            x-show="stagedCategories.length > 0"
            x-text="stagedCategories.length + ' {{ __('selected', 'sage') }}'"
            class="rounded-full bg-primary-100 px-2 py-0.5 text-xs font-medium text-primary-700"
          ></span>
        </div>

        <div class="space-y-1">
          {{-- All Products Option (or All in Category on category pages) --}}
          <div
            class="group flex cursor-pointer items-center gap-3 rounded-lg px-3 py-2.5 transition-all duration-200"
            :class="stagedCategories.length === 0 ? 'bg-primary-50 ring-1 ring-primary-200' : 'hover:bg-secondary-50'"
            @click="stagedCategories = []"
            role="checkbox"
            :aria-checked="stagedCategories.length === 0"
            tabindex="0"
            @keydown.enter="stagedCategories = []"
            @keydown.space.prevent="stagedCategories = []"
          >
            <span class="relative flex h-5 w-5 items-center justify-center">
              <span
                class="absolute inset-0 rounded border-2 transition-all duration-200"
                :class="stagedCategories.length === 0 ? 'border-primary-500 bg-primary-500' : 'border-secondary-300 bg-white group-hover:border-secondary-400'"
              ></span>
              <svg
                class="relative h-3 w-3 text-white transition-transform duration-200"
                :class="stagedCategories.length === 0 ? 'scale-100' : 'scale-0'"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                stroke-width="3"
              >
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
              </svg>
            </span>
            <span
              class="flex-1 text-sm font-medium"
              :class="stagedCategories.length === 0 ? 'text-primary-700' : 'text-secondary-700'"
            >
              @if ($isCategoryPage && $parentCategory)
                {{ sprintf(__('All in %s', 'sage'), $parentCategory['name']) }}
              @else
                {{ __('All Products', 'sage') }}
              @endif
            </span>
            <span class="rounded-full bg-secondary-100 px-2 py-0.5 text-xs font-medium text-secondary-600">
              @if ($isCategoryPage && $parentCategory)
                {{ $parentCategory['count'] }}
              @else
                {{ $totalProducts }}
              @endif
            </span>
          </div>

          {{-- Back to Shop link on category pages --}}
          @if ($isCategoryPage)
            <a
              href="{{ $shopPageUrl }}"
              class="group flex items-center gap-3 rounded-lg px-3 py-2 text-sm text-secondary-500 transition-all duration-200 hover:bg-secondary-50 hover:text-secondary-700"
            >
              <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
              </svg>
              <span>{{ __('View all categories', 'sage') }}</span>
            </a>
          @endif

          {{-- Separator --}}
          <div class="my-2 border-t border-secondary-100"></div>

          {{-- Category List --}}
          @if (empty($categories) && $isCategoryPage)
            {{-- No subcategories message for category pages --}}
            <p class="px-3 py-2 text-sm text-secondary-500 italic">
              {{ __('No subcategories in this category', 'sage') }}
            </p>
          @endif

          @foreach ($categories as $category)
            @php
              $hasChildren = !empty($category['children']);
              $catId = $category['id'];
            @endphp
            <div>
              <div class="flex items-center gap-1">
                {{-- Category Item - using ID for filtering --}}
                <div
                  class="group flex flex-1 cursor-pointer items-center gap-3 rounded-lg px-3 py-2.5 transition-all duration-200"
                  :class="stagedCategories.includes({{ $catId }}) ? 'bg-primary-50 ring-1 ring-primary-200' : 'hover:bg-secondary-50'"
                  @click="
                    if (stagedCategories.includes({{ $catId }})) {
                      stagedCategories = stagedCategories.filter(c => c !== {{ $catId }});
                    } else {
                      stagedCategories = [...new Set([...stagedCategories, {{ $catId }}])];
                    }
                  "
                  role="checkbox"
                  :aria-checked="stagedCategories.includes({{ $catId }})"
                  tabindex="0"
                >
                  <span class="relative flex h-5 w-5 items-center justify-center">
                    <span
                      class="absolute inset-0 rounded border-2 transition-all duration-200"
                      :class="stagedCategories.includes({{ $catId }}) ? 'border-primary-500 bg-primary-500' : 'border-secondary-300 bg-white group-hover:border-secondary-400'"
                    ></span>
                    <svg
                      class="relative h-3 w-3 text-white transition-transform duration-200"
                      :class="stagedCategories.includes({{ $catId }}) ? 'scale-100' : 'scale-0'"
                      fill="none"
                      viewBox="0 0 24 24"
                      stroke="currentColor"
                      stroke-width="3"
                    >
                      <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                  </span>
                  <span
                    class="flex-1 text-sm"
                    :class="stagedCategories.includes({{ $catId }}) ? 'font-medium text-primary-700' : 'text-secondary-700'"
                  >
                    {{ $category['name'] }}
                  </span>
                  <span
                    class="rounded-full px-2 py-0.5 text-xs font-medium"
                    :class="stagedCategories.includes({{ $catId }}) ? 'bg-primary-100 text-primary-700' : 'bg-secondary-100 text-secondary-600'"
                  >
                    {{ $category['count'] }}
                  </span>
                </div>

                @if ($hasChildren)
                  <button
                    type="button"
                    class="flex h-8 w-8 items-center justify-center rounded-lg text-secondary-400 transition-colors hover:bg-secondary-100 hover:text-secondary-600"
                    @click.stop="expandedParents[{{ $catId }}] = !expandedParents[{{ $catId }}]"
                    :aria-expanded="expandedParents[{{ $catId }}]"
                  >
                    <svg
                      class="h-4 w-4 transition-transform duration-200"
                      :class="{ 'rotate-90': expandedParents[{{ $catId }}] }"
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
              @if ($hasChildren)
                <div
                  class="ml-4 mt-1 space-y-1 border-l-2 border-secondary-100 pl-3"
                  x-show="expandedParents[{{ $catId }}]"
                  x-collapse
                >
                  @foreach ($category['children'] as $child)
                    @php $childId = $child['id']; @endphp
                    <div
                      class="group flex cursor-pointer items-center gap-3 rounded-lg px-3 py-2 transition-all duration-200"
                      :class="stagedCategories.includes({{ $childId }}) ? 'bg-primary-50 ring-1 ring-primary-200' : 'hover:bg-secondary-50'"
                      @click="
                        if (stagedCategories.includes({{ $childId }})) {
                          stagedCategories = stagedCategories.filter(c => c !== {{ $childId }});
                        } else {
                          stagedCategories = [...new Set([...stagedCategories.filter(c => c !== {{ $catId }}), {{ $childId }}])];
                        }
                      "
                      role="checkbox"
                      :aria-checked="stagedCategories.includes({{ $childId }})"
                      tabindex="0"
                    >
                      <span class="relative flex h-4 w-4 items-center justify-center">
                        <span
                          class="absolute inset-0 rounded border-2 transition-all duration-200"
                          :class="stagedCategories.includes({{ $childId }}) ? 'border-primary-500 bg-primary-500' : 'border-secondary-300 bg-white group-hover:border-secondary-400'"
                        ></span>
                        <svg
                          class="relative h-2.5 w-2.5 text-white transition-transform duration-200"
                          :class="stagedCategories.includes({{ $childId }}) ? 'scale-100' : 'scale-0'"
                          fill="none"
                          viewBox="0 0 24 24"
                          stroke="currentColor"
                          stroke-width="3"
                        >
                          <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                      </span>
                      <span
                        class="flex-1 text-sm"
                        :class="stagedCategories.includes({{ $childId }}) ? 'font-medium text-primary-700' : 'text-secondary-600'"
                      >
                        {{ $child['name'] }}
                      </span>
                      <span
                        class="rounded-full px-1.5 py-0.5 text-xs font-medium"
                        :class="stagedCategories.includes({{ $childId }}) ? 'bg-primary-100 text-primary-700' : 'bg-secondary-100 text-secondary-500'"
                      >
                        {{ $child['count'] }}
                      </span>
                    </div>
                  @endforeach
                </div>
              @endif
            </div>
          @endforeach
        </div>
      </div>
    @endif

    {{-- ==================== APPLY & CLEAR BUTTONS ==================== --}}
    <div class="space-y-3 rounded-lg bg-white p-4 shadow-sm ring-1 ring-secondary-100">
      {{-- Apply Button --}}
      <button
        type="button"
        @click="applyStagedFilters()"
        :disabled="isLoading"
        class="flex w-full items-center justify-center gap-2 rounded-lg bg-primary-600 px-4 py-3 text-sm font-semibold text-white shadow-sm transition-all hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
      >
        <svg x-show="isLoading" class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <svg x-show="!isLoading" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
        </svg>
        <span x-text="isLoading ? '{{ __('Loading...', 'sage') }}' : '{{ __('Apply Filters', 'sage') }}'"></span>
      </button>

      {{-- Clear Button --}}
      <button
        type="button"
        @click="clearAllFilters()"
        x-show="hasActiveFilters || hasStagedFilters"
        :disabled="isLoading"
        class="flex w-full items-center justify-center gap-2 rounded-lg border border-secondary-300 bg-white px-4 py-2.5 text-sm font-medium text-secondary-700 shadow-sm transition-all hover:bg-secondary-50 focus:outline-none focus:ring-2 focus:ring-secondary-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
      >
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
        </svg>
        {{ __('Clear All', 'sage') }}
      </button>

      {{-- Pending Changes Indicator --}}
      <p
        x-show="hasPendingChanges"
        class="text-center text-xs text-amber-600"
      >
        {{ __('You have unapplied changes', 'sage') }}
      </p>
    </div>

    {{-- WordPress Widgets (if any) --}}
    @if (is_active_sidebar('sidebar-shop'))
      <div class="wordpress-widgets space-y-6">
        @php(dynamic_sidebar('sidebar-shop'))
      </div>
    @endif
  </div>
</div>
