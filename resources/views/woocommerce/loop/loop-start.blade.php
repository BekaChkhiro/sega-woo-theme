{{--
  Product Loop Start Template

  This template opens the product grid container on archive/shop pages.
  It's automatically called by WooCommerce via the `woocommerce_product_loop_start` filter.

  @see App\View\Composers\Shop::gridClasses()
--}}

<ul class="products grid gap-3 xs:gap-4 sm:gap-5 lg:gap-6 xl:gap-8 {{ $gridClasses }}">
