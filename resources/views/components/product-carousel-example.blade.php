{{--
  Product Carousel Component - Usage Examples

  This file demonstrates various ways to use the product-carousel component.
  DO NOT include this file in production - it's for documentation only.
--}}

{{-- Example 1: Basic usage with product query --}}
@php
  $new_products = wc_get_products([
    'limit' => 8,
    'orderby' => 'date',
    'order' => 'DESC',
    'status' => 'publish',
  ]);
@endphp

<x-product-carousel
  :products="$new_products"
  title="New Arrivals"
/>

{{-- Example 2: On Sale products with custom configuration --}}
@php
  $sale_products = wc_get_products([
    'limit' => 12,
    'on_sale' => true,
    'status' => 'publish',
  ]);
@endphp

<x-product-carousel
  :products="$sale_products"
  title="On Sale Now"
  :slides-per-view="5"
  :space-between="20"
  :autoplay="true"
  :loop="true"
  view-all-url="/shop/?on_sale=1"
  view-all-text="View All Sales"
/>

{{-- Example 3: Featured products with pagination --}}
@php
  $featured_products = wc_get_products([
    'limit' => 8,
    'featured' => true,
    'status' => 'publish',
  ]);
@endphp

<x-product-carousel
  :products="$featured_products"
  title="Featured Products"
  :pagination="true"
  :navigation="false"
/>

{{-- Example 4: Bestsellers with custom ID --}}
@php
  $bestsellers = wc_get_products([
    'limit' => 10,
    'orderby' => 'popularity',
    'order' => 'DESC',
    'status' => 'publish',
  ]);
@endphp

<x-product-carousel
  :products="$bestsellers"
  title="Bestsellers"
  id="bestsellers-carousel"
  :slides-per-view="4"
  :space-between="24"
  view-all-url="/shop/?orderby=popularity"
/>

{{-- Example 5: Category products --}}
@php
  $category_products = wc_get_products([
    'limit' => 8,
    'category' => ['electronics'], // Replace with actual category slug
    'status' => 'publish',
  ]);
@endphp

<x-product-carousel
  :products="$category_products"
  title="Electronics"
  view-all-url="/product-category/electronics/"
/>

{{-- Example 6: Related products (for single product page) --}}
@php
  // Assuming $product is the current product
  // $related_ids = wc_get_related_products($product->get_id(), 8);
  // $related_products = array_map('wc_get_product', $related_ids);
@endphp

{{--
<x-product-carousel
  :products="$related_products"
  title="You May Also Like"
  :slides-per-view="4"
  :show-header="true"
/>
--}}

{{-- Example 7: Without header --}}
<x-product-carousel
  :products="$new_products"
  :show-header="false"
  :slides-per-view="3"
/>

{{-- Example 8: Custom container classes --}}
<x-product-carousel
  :products="$sale_products"
  title="Flash Sale"
  container-class="bg-red-50 p-6 rounded-2xl"
  header-class="border-b pb-4"
/>

{{-- Example 9: Manual product array by IDs --}}
@php
  $product_ids = [123, 456, 789]; // Replace with actual product IDs
@endphp

<x-product-carousel
  :products="$product_ids"
  title="Handpicked for You"
/>

{{-- Example 10: With WP_Query products --}}
@php
  $query = new WP_Query([
    'post_type' => 'product',
    'posts_per_page' => 8,
    'meta_key' => 'total_sales',
    'orderby' => 'meta_value_num',
    'order' => 'DESC',
  ]);

  $trending_products = [];
  if ($query->have_posts()) {
    while ($query->have_posts()) {
      $query->the_post();
      $trending_products[] = wc_get_product(get_the_ID());
    }
    wp_reset_postdata();
  }
@endphp

<x-product-carousel
  :products="$trending_products"
  title="Trending Now"
  :autoplay="true"
/>

{{--
  ========================================
  Component Props Reference
  ========================================

  @param array|Collection $products       - Array of WC_Product objects or product IDs
  @param string $title                    - Section title (default: '')
  @param string|null $id                  - Unique ID for carousel (auto-generated if null)
  @param int $slidesPerView               - Slides visible at once on desktop (default: 4)
  @param int $spaceBetween                - Space between slides in pixels (default: 24)
  @param bool $autoplay                   - Enable autoplay (default: false)
  @param bool $loop                       - Enable loop mode (default: true)
  @param bool $navigation                 - Show navigation arrows (default: true)
  @param bool $pagination                 - Show pagination dots (default: false)
  @param string|null $viewAllUrl          - URL for "View All" link
  @param string|null $viewAllText         - Text for "View All" link (default: "View All")
  @param bool $showHeader                 - Show section header (default: true)
  @param string $headerClass              - Additional classes for header
  @param string $containerClass           - Additional classes for container

  ========================================
  Responsive Breakpoints
  ========================================

  - Mobile (<480px):     1 slide
  - Mobile (≥480px):     2 slides
  - Tablet (≥768px):     3 slides
  - Desktop (≥1024px):   4 slides (or slidesPerView if less)
  - Large (≥1280px):     slidesPerView

  ========================================
  Methods (via Alpine.js)
  ========================================

  Access via: x-ref="carousel" on parent element, then:
  - $refs.carousel.nextSlide()
  - $refs.carousel.prevSlide()
  - $refs.carousel.goToSlide(index)
  - $refs.carousel.toggleAutoplay()
  - $refs.carousel.destroy()
--}}
