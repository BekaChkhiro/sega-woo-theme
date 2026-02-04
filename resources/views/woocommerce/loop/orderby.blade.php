{{--
  WooCommerce Product Sorting Dropdown

  This template displays the product sorting/ordering dropdown on shop archive pages.

  Available variables from Shop Composer:
  - $sortingOptions: Array of sorting options with value, label, selected, url
  - $currentOrderby: Current selected orderby value
--}}

<form method="get" class="woocommerce-ordering" aria-label="{{ __('Product sorting', 'sage') }}">
  <label for="orderby" class="sr-only">
    {{ __('Shop order', 'sage') }}
  </label>

  <select
    name="orderby"
    id="orderby"
    class="orderby rounded-lg border border-secondary-300 bg-white px-3 py-2 text-sm text-secondary-700 transition-colors focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
    aria-label="{{ __('Shop order', 'sage') }}"
    onchange="this.form.submit()"
  >
    @foreach ($sortingOptions as $option)
      <option
        value="{{ esc_attr($option['value']) }}"
        @selected($option['selected'])
      >
        {{ esc_html($option['label']) }}
      </option>
    @endforeach
  </select>

  {{-- Preserve existing query parameters --}}
  @foreach (request()->query() as $key => $value)
    @if ($key !== 'orderby' && $key !== 'submit')
      @if (is_array($value))
        @foreach ($value as $innerKey => $innerValue)
          <input
            type="hidden"
            name="{{ esc_attr($key) }}[{{ esc_attr($innerKey) }}]"
            value="{{ esc_attr($innerValue) }}"
          >
        @endforeach
      @else
        <input
          type="hidden"
          name="{{ esc_attr($key) }}"
          value="{{ esc_attr($value) }}"
        >
      @endif
    @endif
  @endforeach

  <noscript>
    <button
      type="submit"
      class="ml-2 rounded-lg bg-primary-600 px-3 py-2 text-sm font-medium text-white transition-colors hover:bg-primary-700"
    >
      {{ __('Sort', 'sage') }}
    </button>
  </noscript>
</form>
