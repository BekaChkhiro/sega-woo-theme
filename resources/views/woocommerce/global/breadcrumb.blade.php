{{--
  WooCommerce Global Breadcrumb Template

  This template overrides the default WooCommerce breadcrumb output.
  It uses the theme's reusable breadcrumbs component for consistency.

  @see woocommerce/templates/global/breadcrumb.php

  Variables available:
  - $breadcrumbs (array): Array of breadcrumb items from View Composer
    Each item has: ['label' => string, 'url' => string]

  Usage in templates:
  @include('woocommerce.global.breadcrumb', ['breadcrumbs' => $breadcrumbItems])

  Or use the component directly:
  <x-breadcrumbs :items="$breadcrumbItems" />
--}}

@php
  // Support both variable naming conventions
  $items = $breadcrumbs ?? $items ?? [];
@endphp

@if (!empty($items))
  <x-breadcrumbs :items="$items" />
@endif
