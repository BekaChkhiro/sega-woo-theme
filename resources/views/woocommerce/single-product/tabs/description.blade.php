{{--
  Product Description Tab Content

  Displays the product's full description with proper typography styling.

  @param string $description - The product's full description HTML
--}}

@if (!empty($description))
  <div class="woocommerce-Tabs-panel woocommerce-Tabs-panel--description">
    <div class="prose prose-secondary max-w-none prose-headings:font-semibold prose-headings:text-secondary-900 prose-p:text-secondary-600 prose-a:text-primary-600 prose-a:no-underline hover:prose-a:underline prose-strong:text-secondary-900 prose-ul:text-secondary-600 prose-ol:text-secondary-600 prose-li:marker:text-secondary-400">
      {!! $description !!}
    </div>
  </div>
@else
  <p class="text-secondary-500 italic">
    {{ __('No description available for this product.', 'sage') }}
  </p>
@endif
