@php
  // Test PHP block
  $productsArray = [];
  if (is_array($products)) {
    $productsArray = $products;
  } elseif (is_object($products)) {
    if (method_exists($products, 'all')) {
      $productsArray = $products->all();
    } elseif (method_exists($products, 'toArray')) {
      $productsArray = $products->toArray();
    }
  }
  $productCount = count($productsArray);
@endphp

<div class="bg-green-500 text-white p-4">
  <p>SIMPLE TEST WORKS! Products passed: {{ isset($products) ? 'YES' : 'NO' }}</p>
  <p>Product count: {{ $productCount }}</p>
  <p>Is array: {{ is_array($productsArray) ? 'YES' : 'NO' }}</p>
  @if ($productCount > 0)
    <p>First product: {{ $productsArray[0]->get_name() }}</p>
  @endif
</div>
