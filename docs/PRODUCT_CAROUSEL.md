# Product Carousel Component

A reusable, touch-friendly product carousel component built with Swiper.js, Alpine.js, and Blade templates.

## Features

✅ **Responsive Design** - Adapts from 1 slide on mobile to 4+ on desktop
✅ **Touch/Swipe Support** - Native touch gestures on mobile and tablets
✅ **Keyboard Navigation** - Arrow keys for accessibility
✅ **Autoplay** - Optional auto-scrolling with pause on hover
✅ **Loop Mode** - Infinite scrolling when enabled
✅ **Navigation Arrows** - Previous/Next buttons (desktop)
✅ **Pagination Dots** - Optional slide indicators
✅ **Lazy Loading** - ProductCard components use lazy image loading
✅ **Accessibility** - ARIA labels and semantic HTML
✅ **Customizable** - Extensive props for configuration

## Installation

The component is already installed and registered. Files created:

```
app/View/Components/ProductCarousel.php
resources/views/components/product-carousel.blade.php
resources/js/components/product-carousel.js
resources/css/app.css (styles added)
```

JavaScript component registered in `resources/js/app.js`:
```javascript
Alpine.data('productCarousel', productCarousel);
```

## Basic Usage

```blade
@php
  $products = wc_get_products([
    'limit' => 8,
    'orderby' => 'date',
    'order' => 'DESC',
  ]);
@endphp

<x-product-carousel
  :products="$products"
  title="New Arrivals"
/>
```

## Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `products` | `array\|Collection` | `[]` | WC_Product objects or product IDs |
| `title` | `string` | `''` | Section title |
| `id` | `string\|null` | auto | Unique carousel ID |
| `slidesPerView` | `int` | `4` | Slides visible (desktop) |
| `spaceBetween` | `int` | `24` | Space between slides (px) |
| `autoplay` | `bool` | `false` | Enable autoplay |
| `loop` | `bool` | `true` | Enable loop mode |
| `navigation` | `bool` | `true` | Show arrow buttons |
| `pagination` | `bool` | `false` | Show pagination dots |
| `viewAllUrl` | `string\|null` | `null` | "View All" link URL |
| `viewAllText` | `string\|null` | `'View All'` | "View All" link text |
| `showHeader` | `bool` | `true` | Show section header |
| `headerClass` | `string` | `''` | Additional header classes |
| `containerClass` | `string` | `''` | Additional container classes |

## Examples

### New Products

```blade
@php
  $new_products = wc_get_products([
    'limit' => 8,
    'orderby' => 'date',
    'order' => 'DESC',
  ]);
@endphp

<x-product-carousel
  :products="$new_products"
  title="New Arrivals"
  view-all-url="/shop/?orderby=date"
/>
```

### On Sale Products

```blade
@php
  $sale_products = wc_get_products([
    'limit' => 12,
    'on_sale' => true,
  ]);
@endphp

<x-product-carousel
  :products="$sale_products"
  title="On Sale Now"
  :slides-per-view="5"
  :autoplay="true"
  view-all-url="/shop/?on_sale=1"
  view-all-text="View All Sales"
/>
```

### Bestsellers

```blade
@php
  $bestsellers = wc_get_products([
    'limit' => 10,
    'orderby' => 'popularity',
  ]);
@endphp

<x-product-carousel
  :products="$bestsellers"
  title="Bestsellers"
  :pagination="true"
  view-all-url="/shop/?orderby=popularity"
/>
```

### Category Products

```blade
@php
  $category_products = wc_get_products([
    'limit' => 8,
    'category' => ['electronics'],
  ]);
@endphp

<x-product-carousel
  :products="$category_products"
  title="Electronics"
  view-all-url="/product-category/electronics/"
/>
```

### Related Products (Single Product Page)

```blade
@php
  $related_ids = wc_get_related_products($product->get_id(), 8);
  $related_products = array_map('wc_get_product', $related_ids);
@endphp

<x-product-carousel
  :products="$related_products"
  title="You May Also Like"
/>
```

## Responsive Breakpoints

| Screen Size | Slides Shown |
|-------------|--------------|
| < 480px | 1 slide |
| ≥ 480px | 2 slides |
| ≥ 768px | 3 slides |
| ≥ 1024px | 4 slides |
| ≥ 1280px | `slidesPerView` |

Space between slides also adjusts responsively.

## Advanced Configuration

### Custom Swiper Config

You can override Swiper configuration by modifying the component:

```blade
<x-product-carousel
  :products="$products"
  title="Custom Carousel"
  :slides-per-view="6"
  :space-between="16"
  :autoplay="true"
  :loop="false"
/>
```

### Without Header

```blade
<x-product-carousel
  :products="$products"
  :show-header="false"
/>
```

### With Custom Classes

```blade
<x-product-carousel
  :products="$products"
  title="Featured"
  container-class="bg-gray-50 p-8 rounded-2xl"
  header-class="border-b pb-4 mb-6"
/>
```

### Using Product IDs

```blade
@php
  $product_ids = [123, 456, 789];
@endphp

<x-product-carousel
  :products="$product_ids"
  title="Handpicked for You"
/>
```

## JavaScript API

Access carousel methods via Alpine.js:

```html
<div x-data="{ carousel: null }">
  <x-product-carousel ... x-init="carousel = $el" />

  <button @click="carousel.nextSlide()">Next</button>
  <button @click="carousel.prevSlide()">Previous</button>
  <button @click="carousel.toggleAutoplay()">Toggle Autoplay</button>
</div>
```

Available methods:
- `nextSlide()` - Go to next slide
- `prevSlide()` - Go to previous slide
- `goToSlide(index)` - Go to specific slide
- `toggleAutoplay()` - Toggle autoplay on/off
- `destroy()` - Destroy Swiper instance

## Styling

Styles are in `resources/css/app.css`. Key classes:

- `.product-carousel-section` - Main section wrapper
- `.product-carousel-swiper` - Swiper container
- `.product-carousel-bullet` - Pagination bullet
- `.product-carousel-bullet-active` - Active bullet

### Customizing Pagination

```css
.product-carousel-bullet {
  background-color: var(--color-secondary-300);
}

.product-carousel-bullet-active {
  background-color: var(--color-primary-600);
}
```

## Performance

- ✅ **Lazy Loading** - Images load only when visible
- ✅ **Optimized Animations** - GPU-accelerated transforms
- ✅ **Conditional Loop** - Only loops if enough products
- ✅ **Touch Optimization** - Optimized for mobile performance

## Accessibility

- ✅ **ARIA Labels** - All interactive elements labeled
- ✅ **Keyboard Navigation** - Arrow keys work
- ✅ **Screen Readers** - Proper announcements
- ✅ **Focus Management** - Visible focus indicators

## Browser Support

- ✅ Chrome/Edge (latest)
- ✅ Firefox (latest)
- ✅ Safari (latest)
- ✅ Mobile Safari (iOS 12+)
- ✅ Chrome Mobile (Android 8+)

## Troubleshooting

### Carousel not sliding

**Check:**
1. Products array has items
2. JavaScript is loaded (`npm run dev`)
3. Alpine.js initialized
4. No console errors

### Navigation arrows hidden

Navigation arrows are hidden on mobile/tablet (`lg:flex`). They appear on desktop (≥1024px).

### Loop not working

Loop is disabled if `productCount() <= slidesPerView`. Ensure you have enough products.

### Slides uneven height

Product cards use `h-full` class. Ensure all ProductCard components maintain consistent structure.

## Dependencies

- **Swiper.js** v12+ - Carousel library
- **Alpine.js** v3+ - Reactive components
- **Tailwind CSS** v4+ - Styling
- **WooCommerce** - Product data

## Credits

Created for Sage WooCommerce Theme (T9.5)

## Support

For issues or questions, refer to:
- Swiper documentation: https://swiperjs.com/
- Alpine.js documentation: https://alpinejs.dev/
- WooCommerce documentation: https://woocommerce.com/docs/
