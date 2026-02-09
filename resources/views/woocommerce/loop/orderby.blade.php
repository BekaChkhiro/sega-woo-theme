{{--
  WooCommerce Product Sorting Dropdown

  Uses the global shopFiltersAPI for unified state management.

  Available variables from Shop Composer:
  - $sortingOptions: Array of sorting options with value, label, selected, url
  - $currentOrderby: Current selected orderby value
--}}

<div
  class="woocommerce-ordering"
  aria-label="{{ __('Product sorting', 'sega-woo-theme') }}"
  x-data="{ currentOrderby: '{{ $currentOrderby ?? 'menu_order' }}', isLoading: false }"
  @shop-filters-updated.window="currentOrderby = $event.detail.orderby"
>
  <label for="orderby" class="sr-only">
    {{ __('Shop order', 'sega-woo-theme') }}
  </label>

  <select
    name="orderby"
    id="orderby"
    class="orderby rounded-lg border border-secondary-300 bg-white px-3 py-2 text-sm text-secondary-700 transition-colors focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
    aria-label="{{ __('Shop order', 'sega-woo-theme') }}"
    x-model="currentOrderby"
    @change="
      if (window.shopFiltersAPI) {
        window.shopFiltersAPI.changeOrderby($event.target.value);
      }
    "
    :disabled="isLoading"
  >
    @foreach ($sortingOptions as $option)
      <option value="{{ esc_attr($option['value']) }}">
        {{ esc_html($option['label']) }}
      </option>
    @endforeach
  </select>
</div>
