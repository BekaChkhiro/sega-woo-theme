{{--
  Product Reviews Tab Content

  Wrapper for WooCommerce's review system with custom styling.
  Uses WooCommerce's built-in comments_template() for review functionality.

  This template provides:
  - Review list with star ratings
  - Review submission form
  - Pagination for reviews

  Note: The actual review content is rendered by WooCommerce's
  single-product-reviews.php template which calls comments_template().
--}}

<div class="woocommerce-Tabs-panel woocommerce-Tabs-panel--reviews">
  <div class="woocommerce-Reviews" id="reviews">
    @php
      // WooCommerce's review template handles:
      // - Review list with star ratings
      // - Average rating display
      // - Review submission form
      // - Pagination
      comments_template();
    @endphp
  </div>
</div>
