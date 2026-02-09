{{--
  WooCommerce Products Per Page Selector

  Uses the global shopFiltersAPI for unified state management.

  Available variables from Shop Composer:
  - $perPageOptions: Array of per page options with value, label, selected, url
  - $currentPerPage: Current selected per page value
--}}

<nav
  class="woocommerce-per-page flex items-center gap-1 text-sm text-secondary-600"
  aria-label="{{ __('Products per page', 'sage') }}"
  x-data="{ currentPerPage: {{ $currentPerPage ?? 12 }}, isLoading: false }"
  @shop-filters-updated.window="currentPerPage = $event.detail.perPage"
>
  @foreach ($perPageOptions as $index => $option)
    @if ($index > 0)
      <span class="text-secondary-400">/</span>
    @endif

    <button
      type="button"
      class="transition-colors"
      :class="currentPerPage === {{ $option['value'] }} ? 'font-bold text-secondary-900' : 'hover:text-secondary-900'"
      @click="
        if (window.shopFiltersAPI) {
          window.shopFiltersAPI.changePerPage({{ $option['value'] }});
          currentPerPage = {{ $option['value'] }};
        }
      "
      :disabled="isLoading"
    >
      {{ $option['value'] }}
    </button>
  @endforeach
</nav>
