{{--
  WooCommerce Result Count Display

  This template displays the product count on shop archive pages.
  Example: "Showing 1-12 of 45 results"

  Available variables from Shop Composer:
  - $resultCount: Formatted result count string (pre-computed)
  - $hasProducts: Whether there are products to display
--}}

@if ($hasProducts)
  <p class="woocommerce-result-count text-sm text-secondary-600" aria-live="polite">
    {!! $resultCount !!}
  </p>
@endif
