{{--
  Search Popup Component

  A full-screen overlay search popup with AJAX product search capabilities.
  Shows categories and products as user types.

  Usage: <x-search-popup />

  Related tasks:
  - T8.1: Create component (this file)
  - T8.2: Add search icon trigger in header
  - T8.3: Implement AJAX product search with WP REST API
  - T8.4: Create search results display (categories + products)
  - T8.5: Add popup animation
  - T8.6: Implement keyboard navigation (ESC to close)
  - T8.7: Style search popup with Tailwind
--}}

<div
  x-data="searchPopup"
  x-cloak
  @open-search-popup.window="open()"
  @keydown.escape.window="close()"
  class="search-popup"
>
  {{-- Backdrop overlay --}}
  <div
    x-show="isOpen"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    @click="handleBackdropClick($event)"
    class="search-popup-backdrop fixed inset-0 z-50 flex items-start justify-center bg-black/60 backdrop-blur-sm px-4 pt-16 sm:pt-24 lg:pt-32 pb-8"
    aria-hidden="true"
  >
    {{-- Search popup content --}}
    <div
      x-show="isOpen"
      x-transition:enter="transition ease-out duration-300"
      x-transition:enter-start="opacity-0 -translate-y-full"
      x-transition:enter-end="opacity-100 translate-y-0"
      x-transition:leave="transition ease-in duration-200"
      x-transition:leave-start="opacity-100 translate-y-0"
      x-transition:leave-end="opacity-0 -translate-y-full"
      @click.stop
      class="search-popup-modal w-full max-w-2xl bg-white rounded-2xl shadow-2xl ring-1 ring-black/5 overflow-hidden"
      role="dialog"
      aria-modal="true"
      aria-labelledby="search-popup-title"
    >
      {{-- Search header with input --}}
      <div class="p-4 sm:p-6 border-b border-secondary-100 bg-gradient-to-b from-secondary-50/50 to-white">
        <div class="search-popup-input-wrapper flex items-center gap-3 px-4 py-3 bg-white rounded-xl border border-secondary-200 shadow-sm focus-within:border-primary-300 focus-within:ring-2 focus-within:ring-primary-100 transition-all duration-200">
          {{-- Search icon --}}
          <div class="flex-shrink-0 text-secondary-400 search-popup-icon-pulse">
            <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
          </div>

          {{-- Search input --}}
          <form @submit.prevent="handleSubmit()" class="flex-1 min-w-0">
            <input
              type="search"
              x-ref="searchInput"
              x-model="query"
              @input="handleInput()"
              placeholder="{{ __('Search products...', 'sage') }}"
              class="w-full text-base sm:text-lg text-secondary-900 placeholder-secondary-400 bg-transparent border-none outline-none focus:ring-0 p-0"
              autocomplete="off"
              autocapitalize="off"
              spellcheck="false"
            >
          </form>

          {{-- Loading spinner --}}
          <div x-show="isLoading" x-cloak class="flex-shrink-0">
            <svg class="w-5 h-5 text-primary-600 search-popup-spinner" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
          </div>

          {{-- Clear button (when query exists) --}}
          <button
            type="button"
            x-show="query.length > 0"
            x-cloak
            @click="query = ''; results = { categories: [], products: [] }; hasSearched = false; $refs.searchInput.focus()"
            class="flex-shrink-0 p-1.5 text-secondary-400 hover:text-secondary-600 transition-colors rounded-full hover:bg-secondary-100"
            aria-label="{{ __('Clear search', 'sage') }}"
          >
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>

          {{-- Close button --}}
          <button
            type="button"
            @click="close()"
            class="search-popup-close flex-shrink-0 p-2 -mr-1 text-secondary-400 hover:text-secondary-600 transition-all duration-200 rounded-lg hover:bg-secondary-100"
            aria-label="{{ __('Close search', 'sage') }}"
          >
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        {{-- Search hint --}}
        <p x-show="query.length === 0" class="mt-3 text-xs sm:text-sm text-secondary-500 flex items-center gap-1.5">
          <svg class="w-4 h-4 text-secondary-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          {{ __('Type at least 2 characters to search', 'sage') }}
        </p>
      </div>

      {{-- Search results area --}}
      <div class="search-popup-results max-h-[60vh] overflow-y-auto">
        {{-- Initial state: Quick links / Popular categories --}}
        <div x-show="query.length < 2 && !hasSearched" class="p-4 sm:p-6">
          <h3 id="search-popup-title" class="search-section-title text-xs sm:text-sm font-semibold text-secondary-500 uppercase tracking-wider mb-4 flex items-center gap-2">
            <svg class="w-4 h-4 text-secondary-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
            </svg>
            {{ __('Popular Categories', 'sage') }}
          </h3>

          {{-- Category quick links --}}
          <div class="flex flex-wrap gap-2">
            @php
              $categories = get_terms([
                'taxonomy' => 'product_cat',
                'hide_empty' => true,
                'number' => 6,
                'orderby' => 'count',
                'order' => 'DESC',
              ]);
            @endphp

            @if (!is_wp_error($categories) && !empty($categories))
              @foreach ($categories as $category)
                <a
                  href="{{ get_term_link($category) }}"
                  class="search-category-link group inline-flex items-center gap-1.5 px-3.5 py-2 text-sm font-medium text-secondary-700 bg-secondary-50 border border-secondary-200 rounded-full hover:bg-primary-50 hover:border-primary-200 hover:text-primary-700 transition-all duration-200"
                >
                  <span>{{ $category->name }}</span>
                  <svg class="w-3.5 h-3.5 text-secondary-400 group-hover:text-primary-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                  </svg>
                </a>
              @endforeach
            @endif
          </div>
        </div>

        {{-- Loading state with skeleton --}}
        <div x-show="isLoading && query.length >= 2" x-cloak class="p-4 sm:p-6">
          {{-- Skeleton categories --}}
          <div class="mb-6">
            <div class="h-4 w-24 bg-secondary-200 rounded animate-pulse mb-3"></div>
            <div class="space-y-2">
              @for ($i = 0; $i < 2; $i++)
                <div class="flex items-center gap-3 p-2">
                  <div class="w-10 h-10 rounded-lg bg-secondary-200 animate-pulse"></div>
                  <div class="flex-1">
                    <div class="h-4 w-32 bg-secondary-200 rounded animate-pulse mb-1"></div>
                    <div class="h-3 w-20 bg-secondary-100 rounded animate-pulse"></div>
                  </div>
                </div>
              @endfor
            </div>
          </div>

          {{-- Skeleton products --}}
          <div>
            <div class="h-4 w-20 bg-secondary-200 rounded animate-pulse mb-3"></div>
            <div class="space-y-2">
              @for ($i = 0; $i < 3; $i++)
                <div class="flex items-center gap-3 p-2">
                  <div class="w-14 h-14 rounded-lg bg-secondary-200 animate-pulse"></div>
                  <div class="flex-1">
                    <div class="h-4 w-40 bg-secondary-200 rounded animate-pulse mb-1.5"></div>
                    <div class="h-3 w-24 bg-secondary-100 rounded animate-pulse"></div>
                  </div>
                </div>
              @endfor
            </div>
          </div>
        </div>

        {{-- No results state --}}
        <div x-show="showNoResults" x-cloak class="p-6 sm:p-8">
          <div class="flex flex-col items-center justify-center py-8 text-center">
            <div class="search-no-results-icon w-20 h-20 mb-5 rounded-full bg-gradient-to-br from-secondary-100 to-secondary-50 flex items-center justify-center shadow-inner">
              <svg class="w-10 h-10 text-secondary-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 10l4 4m0-4l-4 4" />
              </svg>
            </div>
            <h4 class="text-lg font-semibold text-secondary-900 mb-2">{{ __('No results found', 'sage') }}</h4>
            <p class="text-sm text-secondary-500 max-w-xs">
              {{ __('We couldn\'t find any products matching your search. Try different keywords or browse our categories.', 'sage') }}
            </p>
          </div>
        </div>

        {{-- Search results - Categories section --}}
        <div x-show="hasResults && results.categories.length > 0" x-cloak class="border-b border-secondary-100">
          <div class="p-4 sm:p-6">
            <h3 class="search-section-title text-xs sm:text-sm font-semibold text-secondary-500 uppercase tracking-wider mb-4 flex items-center gap-2">
              <svg class="w-4 h-4 text-secondary-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
              </svg>
              {{ __('Categories', 'sage') }}
              <span class="text-secondary-400 font-normal normal-case" x-text="'(' + results.categories.length + ')'"></span>
            </h3>

            <div class="space-y-1">
              <template x-for="(category, index) in results.categories" :key="category.id">
                <a
                  :href="category.url"
                  class="search-result-item group flex items-center gap-3 p-3 -mx-2 rounded-xl hover:bg-gradient-to-r hover:from-secondary-50 hover:to-transparent transition-all duration-200"
                  :style="'animation-delay: ' + (index * 50) + 'ms'"
                >
                  <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-secondary-100 to-secondary-50 flex items-center justify-center flex-shrink-0 group-hover:from-primary-100 group-hover:to-primary-50 transition-all duration-200 shadow-sm">
                    <svg class="w-5 h-5 text-secondary-500 group-hover:text-primary-600 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                    </svg>
                  </div>
                  <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-secondary-900 truncate group-hover:text-primary-700 transition-colors" x-text="category.name"></p>
                    <p class="text-xs text-secondary-500" x-text="category.count + ' {{ __('products', 'sage') }}'"></p>
                  </div>
                  <svg class="w-4 h-4 text-secondary-300 flex-shrink-0 group-hover:text-primary-500 group-hover:translate-x-0.5 transition-all duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                  </svg>
                </a>
              </template>
            </div>
          </div>
        </div>

        {{-- Search results - Products section --}}
        <div x-show="hasResults && results.products.length > 0" x-cloak>
          <div class="p-4 sm:p-6">
            <h3 class="search-section-title text-xs sm:text-sm font-semibold text-secondary-500 uppercase tracking-wider mb-4 flex items-center gap-2">
              <svg class="w-4 h-4 text-secondary-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
              </svg>
              {{ __('Products', 'sage') }}
              <span class="text-secondary-400 font-normal normal-case" x-text="'(' + results.products.length + ')'"></span>
            </h3>

            <div class="space-y-1">
              <template x-for="(product, index) in results.products" :key="product.id">
                <a
                  :href="product.url"
                  class="search-result-item group flex items-center gap-3 p-3 -mx-2 rounded-xl hover:bg-gradient-to-r hover:from-secondary-50 hover:to-transparent transition-all duration-200"
                  :style="'animation-delay: ' + ((results.categories.length + index) * 50) + 'ms'"
                >
                  {{-- Product image --}}
                  <div class="search-result-image w-16 h-16 rounded-xl bg-secondary-100 overflow-hidden flex-shrink-0 ring-1 ring-secondary-200 group-hover:ring-primary-200 transition-all duration-200">
                    <img
                      x-show="product.image"
                      :src="product.image"
                      :alt="product.name"
                      class="w-full h-full object-cover"
                      loading="lazy"
                    >
                    <div x-show="!product.image" x-cloak class="w-full h-full flex items-center justify-center bg-gradient-to-br from-secondary-50 to-secondary-100">
                      <svg class="w-6 h-6 text-secondary-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                      </svg>
                    </div>
                  </div>

                  {{-- Product info --}}
                  <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-secondary-900 truncate group-hover:text-primary-700 transition-colors" x-text="product.name"></p>

                    {{-- SKU --}}
                    <p x-show="product.sku" x-cloak class="text-xs text-secondary-400 mt-0.5 font-mono">
                      SKU: <span x-text="product.sku"></span>
                    </p>

                    <div class="flex items-center flex-wrap gap-x-2 gap-y-1 mt-1.5">
                      {{-- Sale price --}}
                      <template x-if="product.on_sale">
                        <span class="text-sm font-bold text-primary-600" x-text="product.sale_price"></span>
                      </template>
                      {{-- Regular price (with strikethrough if on sale) --}}
                      <span
                        :class="product.on_sale ? 'text-xs text-secondary-400 line-through decoration-secondary-300' : 'text-sm font-bold text-secondary-900'"
                        x-text="product.regular_price"
                      ></span>

                      {{-- Stock status badges --}}
                      <span
                        x-show="product.in_stock"
                        class="inline-flex items-center gap-0.5 text-xs font-medium text-green-700 bg-green-50 px-1.5 py-0.5 rounded"
                      >
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        {{ __('In Stock', 'sage') }}
                      </span>
                      <span
                        x-show="!product.in_stock"
                        x-cloak
                        class="inline-flex items-center gap-0.5 text-xs font-medium text-red-700 bg-red-50 px-1.5 py-0.5 rounded"
                      >
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        {{ __('Out of Stock', 'sage') }}
                      </span>
                    </div>
                  </div>

                  {{-- Sale badge --}}
                  <div x-show="product.on_sale" x-cloak class="flex-shrink-0">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-gradient-to-r from-red-500 to-rose-500 text-white shadow-sm">
                      {{ __('Sale', 'sage') }}
                    </span>
                  </div>

                  {{-- Arrow --}}
                  <svg class="w-4 h-4 text-secondary-300 flex-shrink-0 group-hover:text-primary-500 group-hover:translate-x-0.5 transition-all duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                  </svg>
                </a>
              </template>
            </div>
          </div>
        </div>

        {{-- View all results link --}}
        <div x-show="hasResults" x-cloak class="p-4 sm:p-6 border-t border-secondary-100 bg-gradient-to-b from-secondary-50/80 to-secondary-50">
          <button
            @click="handleSubmit()"
            class="search-view-all-btn group w-full flex items-center justify-center gap-2 py-3.5 text-sm font-semibold text-white bg-gradient-to-r from-primary-600 to-primary-500 hover:from-primary-700 hover:to-primary-600 transition-all duration-200 rounded-xl shadow-sm hover:shadow-md"
          >
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <span>{{ __('View all results for', 'sage') }} "<span x-text="query" class="font-bold"></span>"</span>
            <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
            </svg>
          </button>
        </div>
      </div>

      {{-- Footer with keyboard shortcuts --}}
      <div class="search-keyboard-hints hidden sm:flex items-center justify-between px-4 sm:px-6 py-3 bg-white border-t border-secondary-100 text-xs text-secondary-500">
        <div class="flex items-center gap-6">
          <span class="flex items-center gap-2">
            <kbd class="inline-flex items-center justify-center min-w-[2rem] px-2 py-1 bg-secondary-100 border border-secondary-200 rounded-md text-secondary-600 font-mono text-[10px] font-medium shadow-sm">ESC</kbd>
            <span class="text-secondary-500">{{ __('to close', 'sage') }}</span>
          </span>
          <span class="flex items-center gap-2">
            <kbd class="inline-flex items-center justify-center min-w-[2rem] px-2 py-1 bg-secondary-100 border border-secondary-200 rounded-md text-secondary-600 font-mono text-[10px] font-medium shadow-sm">↵</kbd>
            <span class="text-secondary-500">{{ __('to search', 'sage') }}</span>
          </span>
        </div>
        <div class="flex items-center gap-1.5 text-secondary-400">
          <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
          </svg>
          <span>{{ __('Powered by SEGA', 'sage') }}</span>
        </div>
      </div>
    </div>
  </div>
</div>
