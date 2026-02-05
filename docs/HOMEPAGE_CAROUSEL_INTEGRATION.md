# Homepage Carousel Integration Guide

This guide shows how to integrate the ProductCarousel component into the homepage for tasks T9.6, T9.7, and T9.8.

## Overview

The homepage will feature three product carousel sections:
1. **New Products** (T9.6) - Latest arrivals
2. **On Sale** (T9.7) - Discounted products
3. **Bestsellers** (T9.8) - Most popular products

## Implementation

### Step 1: Create Homepage View Composer (T9.9)

Create `app/View/Composers/Homepage.php`:

```php
<?php

namespace App\View\Composers;

use Roots\Acorn\View\Composer;

class Homepage extends Composer
{
    protected static $views = [
        'front-page',
    ];

    public function with()
    {
        return [
            'newProducts' => $this->newProducts(),
            'saleProducts' => $this->saleProducts(),
            'bestsellers' => $this->bestsellers(),
        ];
    }

    protected function newProducts()
    {
        return wc_get_products([
            'limit' => 12,
            'orderby' => 'date',
            'order' => 'DESC',
            'status' => 'publish',
        ]);
    }

    protected function saleProducts()
    {
        return wc_get_products([
            'limit' => 12,
            'on_sale' => true,
            'status' => 'publish',
            'orderby' => 'date',
            'order' => 'DESC',
        ]);
    }

    protected function bestsellers()
    {
        return wc_get_products([
            'limit' => 12,
            'orderby' => 'popularity',
            'order' => 'DESC',
            'status' => 'publish',
        ]);
    }
}
```

### Step 2: Register View Composer

In `app/Providers/ThemeServiceProvider.php`, add:

```php
use App\View\Composers\Homepage;

public function boot()
{
    // ... existing composers

    Homepage::register();
}
```

### Step 3: Update front-page.blade.php

Add the three carousel sections:

```blade
@extends('layouts.app')

@section('content')
  {{-- Hero Section (mega-menu + slider) --}}
  <section class="hero-section mb-12 sm:mb-16">
    {{-- Existing hero content --}}
    <x-hero-slider />
  </section>

  <div class="container mx-auto px-4 sm:px-6 lg:px-8">
    {{-- T9.6: New Products Carousel --}}
    @if ($newProducts && count($newProducts) > 0)
      <x-product-carousel
        :products="$newProducts"
        title="New Arrivals"
        :slides-per-view="4"
        :space-between="24"
        :autoplay="false"
        :loop="true"
        :navigation="true"
        view-all-url="/shop/?orderby=date"
        view-all-text="View All New Products"
        class="mb-12 sm:mb-16"
      />
    @endif

    {{-- T9.7: On Sale Products Carousel --}}
    @if ($saleProducts && count($saleProducts) > 0)
      <x-product-carousel
        :products="$saleProducts"
        title="On Sale Now"
        :slides-per-view="5"
        :space-between="20"
        :autoplay="true"
        :loop="true"
        :navigation="true"
        view-all-url="/shop/?on_sale=1"
        view-all-text="View All Sales"
        container-class="bg-gradient-to-br from-red-50 to-orange-50 p-6 sm:p-8 rounded-2xl"
        class="mb-12 sm:mb-16"
      />
    @endif

    {{-- T9.8: Bestsellers Carousel --}}
    @if ($bestsellers && count($bestsellers) > 0)
      <x-product-carousel
        :products="$bestsellers"
        title="Bestsellers"
        :slides-per-view="4"
        :space-between="24"
        :autoplay="false"
        :loop="true"
        :navigation="true"
        :pagination="true"
        view-all-url="/shop/?orderby=popularity"
        view-all-text="See All Bestsellers"
        class="mb-12 sm:mb-16"
      />
    @endif

    {{-- Additional Homepage Sections --}}
    {{-- Add categories, brands, features, etc. --}}
  </div>
@endsection
```

## Customization Options

### T9.6: New Products
- **Purpose**: Showcase latest arrivals
- **Slides per view**: 4
- **Autoplay**: Disabled (let users browse)
- **Background**: Default (white)
- **Order**: Most recent first

### T9.7: On Sale
- **Purpose**: Highlight discounted products
- **Slides per view**: 5 (show more deals)
- **Autoplay**: Enabled (4s delay)
- **Background**: Red/Orange gradient
- **Order**: Most recent sale first
- **Special styling**: Emphasized with background color

### T9.8: Bestsellers
- **Purpose**: Display popular products
- **Slides per view**: 4
- **Autoplay**: Disabled
- **Pagination**: Enabled (dots)
- **Order**: By popularity/sales

## Performance Optimization

### Caching (Recommended)

Add caching to View Composer:

```php
protected function newProducts()
{
    return cache()->remember('homepage_new_products', 3600, function () {
        return wc_get_products([
            'limit' => 12,
            'orderby' => 'date',
            'order' => 'DESC',
            'status' => 'publish',
        ]);
    });
}
```

### Lazy Loading

Carousels already use lazy loading for images via ProductCard component.

### Conditional Display

Only show sections if products exist:

```blade
@if ($newProducts && count($newProducts) > 0)
  <x-product-carousel ... />
@endif
```

## Responsive Behavior

| Screen | New Products | On Sale | Bestsellers |
|--------|--------------|---------|-------------|
| Mobile (<480px) | 1 slide | 1 slide | 1 slide |
| Mobile (≥480px) | 2 slides | 2 slides | 2 slides |
| Tablet (≥768px) | 3 slides | 3 slides | 3 slides |
| Desktop (≥1024px) | 4 slides | 4 slides | 4 slides |
| Large (≥1280px) | 4 slides | 5 slides | 4 slides |

## Additional Features

### Section Icons

Add icons to section titles:

```blade
<x-product-carousel
  :products="$newProducts"
  class="mb-12"
>
  <x-slot:title>
    <div class="flex items-center gap-2">
      <svg class="h-6 w-6 text-primary-600" ...>...</svg>
      <span>New Arrivals</span>
    </div>
  </x-slot:title>
</x-product-carousel>
```

### Timer for Sales

Add countdown timer to sale section:

```blade
<div class="flex items-center justify-between mb-6">
  <h2 class="text-2xl font-bold">On Sale Now</h2>
  <div class="text-sm text-red-600 font-semibold">
    Ends in: <span id="sale-timer">24h 30m</span>
  </div>
</div>
```

### Category Tabs

Add category filtering:

```blade
<div x-data="{ activeCategory: 'all' }">
  <div class="mb-6 flex gap-2">
    <button @click="activeCategory = 'all'">All</button>
    <button @click="activeCategory = 'electronics'">Electronics</button>
    <button @click="activeCategory = 'clothing'">Clothing</button>
  </div>

  <x-product-carousel ... />
</div>
```

## Testing Checklist

- [ ] All three carousels display correctly
- [ ] Navigation arrows work (desktop)
- [ ] Touch/swipe works (mobile)
- [ ] Autoplay works on Sale section
- [ ] "View All" links work
- [ ] Responsive breakpoints work
- [ ] Images lazy load
- [ ] No console errors
- [ ] Products load from WooCommerce
- [ ] Empty states handled gracefully

## Next Steps

After implementing T9.6, T9.7, T9.8:

1. **T9.9**: Create Homepage View Composer (see Step 1 above)
2. **T9.10**: Style homepage sections with Tailwind
   - Add spacing between sections
   - Add background variations
   - Add section dividers
   - Responsive padding/margins

## Production Checklist

Before deploying:

- [ ] Enable caching for product queries
- [ ] Optimize images (WebP format)
- [ ] Test with 0 products in each section
- [ ] Test with 100+ products in each section
- [ ] Verify autoplay behavior
- [ ] Check mobile performance
- [ ] Validate accessibility
- [ ] Test with slow network (3G)

## Support

For issues, refer to:
- [Product Carousel Documentation](./PRODUCT_CAROUSEL.md)
- [Swiper.js Docs](https://swiperjs.com/)
- [WooCommerce Product Functions](https://woocommerce.github.io/code-reference/)
