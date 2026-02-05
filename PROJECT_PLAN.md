# Sage WooCommerce Theme - Project Plan

> **Project Type:** Full-Stack WordPress/WooCommerce Theme
> **Framework:** Sage 10 (Roots)
> **Created:** 2026-02-02
> **Last Updated:** 2026-02-05
> **Current Focus:** T11.10 - Style and test variable product attribute selectors
> **Status:** In Progress
> **Last Updated:** 2026-02-05
> **Plugin Version:** 1.1.1

---

## Project Overview

### Description
A modern, high-performance WooCommerce theme built with Sage 10 (Roots) framework for a general marketplace store. The theme utilizes Blade templating, View Composers for clean architecture, and Tailwind CSS for styling.

### Target Users
- Store Administrators
- Registered Customers
- Guest Shoppers

### Deployment
- Development: Local Sites (LocalWP)
- Production: Hostinger

---

## Tech Stack

| Layer | Technology |
|-------|------------|
| **Theme Framework** | Sage 11 |
| **PHP Components** | Acorn (Laravel for WordPress) |
| **Templating** | Blade |
| **Build Tool** | Vite |
| **CSS Framework** | Tailwind CSS v4 |
| **Package Managers** | Composer (PHP), npm (Node.js) |
| **PHP Version** | 8.1+ |
| **WordPress** | 6.0+ |
| **WooCommerce** | 8.0+ |

---

## Project Phases

### Phase 1: Foundation & Setup
> **Goal:** Project initialization, dependencies, and base structure

| ID | Task | Complexity | Status | Dependencies |
|----|------|------------|--------|--------------|
| T1.1 | Initialize Sage theme with Composer | Low | DONE ✅ | - |
| T1.2 | Install PHP dependencies (composer install) | Low | DONE ✅ | T1.1 |
| T1.3 | Install Node dependencies (npm install) | Low | DONE ✅ | T1.1 |
| T1.4 | Configure vite.config.js for local development | Medium | DONE ✅ | T1.3 |
| T1.5 | Configure Tailwind CSS (tailwind.config.js) | Medium | DONE ✅ | T1.3 |
| T1.6 | Setup theme supports for WooCommerce (app/setup.php) | Medium | DONE ✅ | T1.2 |
| T1.7 | Create base layout (layouts/app.blade.php) | Medium | DONE ✅ | T1.2 |
| T1.8 | Create header partial (partials/header.blade.php) | Medium | DONE ✅ | T1.7 |
| T1.9 | Create footer partial (partials/footer.blade.php) | Medium | DONE ✅ | T1.7 |
| T1.10 | Register navigation menus | Low | DONE ✅ | T1.6 |
| T1.11 | Register sidebars (shop sidebar) | Low | DONE ✅ | T1.6 |

---

### Phase 2: Shop/Archive Pages (MVP Core)
> **Goal:** Implement product listing, filtering, and sorting

| ID | Task | Complexity | Status | Dependencies |
|----|------|------------|--------|--------------|
| T2.1 | Create Shop View Composer (app/View/Composers/Shop.php) | High | DONE ✅ | T1.6 |
| T2.2 | Create archive-product.blade.php template | High | DONE ✅ | T2.1 |
| T2.3 | Create ProductCard Blade Component | High | DONE ✅ | T1.7 |
| T2.4 | Implement product grid layout with Tailwind | Medium | DONE ✅ | T2.3 |
| T2.5 | Create loop-start.blade.php and loop-end.blade.php | Low | DONE ✅ | T2.2 |
| T2.6 | Implement sorting dropdown (orderby.blade.php) | Medium | DONE ✅ | T2.2 |
| T2.7 | Implement result count display | Low | DONE ✅ | T2.2 |
| T2.8 | Create pagination template (pagination.blade.php) | Medium | DONE ✅ | T2.2 |
| T2.9 | Create shop sidebar with filters | Medium | DONE ✅ | T1.11 |
| T2.10 | Implement category filtering | Medium | DONE ✅ | T2.9 |
| T2.11 | Style sale badges and product badges | Low | DONE ✅ | T2.3 |
| T2.12 | Implement responsive grid for products | Medium | DONE ✅ | T2.4 |

---

### Phase 3: Single Product Pages (MVP Core)
> **Goal:** Implement product detail pages with gallery and add-to-cart

| ID | Task | Complexity | Status | Dependencies |
|----|------|------------|--------|--------------|
| T3.1 | Create Product View Composer (app/View/Composers/Product.php) | High | DONE ✅ | T1.6 |
| T3.2 | Create single-product.blade.php template | High | DONE ✅ | T3.1 |
| T3.3 | Implement product gallery (product-image.blade.php) | High | DONE ✅ | T3.2 |
| T3.4 | Create price display component | Medium | DONE ✅ | T3.2 |
| T3.5 | Implement star rating display | Medium | DONE ✅ | T3.2 |
| T3.6 | Create add-to-cart form for simple products | High | DONE ✅ | T3.2 |
| T3.7 | Create add-to-cart form for variable products | High | DONE ✅ | T3.6 |
| T3.8 | Implement product tabs (description, reviews, etc.) | Medium | DONE ✅ | T3.2 |
| T3.9 | Create related products section | Medium | DONE ✅ | T3.2, T2.3 |
| T3.10 | Implement breadcrumbs | Low | DONE ✅ | T3.2 |
| T3.11 | Add product meta (SKU, categories, tags) | Low | DONE ✅ | T3.2 |
| T3.12 | Style single product layout with Tailwind | Medium | DONE ✅ | T3.2 |

---

### Phase 4: Cart & Checkout (MVP Core)
> **Goal:** Shopping cart and checkout flow implementation

| ID | Task | Complexity | Status | Dependencies |
|----|------|------------|--------|--------------|
| T4.1 | Create mini-cart.blade.php for header | High | DONE ✅ | T1.8 |
| T4.2 | Implement AJAX cart update functionality | High | DONE ✅ | T4.1 |
| T4.3 | Create cart.blade.php page template | High | DONE ✅ | T1.7 |
| T4.4 | Create cart-empty.blade.php template | Low | DONE ✅ | T4.3 |
| T4.5 | Implement quantity update in cart | Medium | DONE ✅ | T4.3 |
| T4.6 | Create form-checkout.blade.php template | High | DONE ✅ | T1.7 |
| T4.7 | Create form-billing.blade.php fields | Medium | DONE ✅ | T4.6 |
| T4.8 | Create form-shipping.blade.php fields | Medium | DONE ✅ | T4.6 |
| T4.9 | Create review-order.blade.php | Medium | DONE ✅ | T4.6 |
| T4.10 | Create thankyou.blade.php template | Medium | DONE ✅ | T4.6 |
| T4.11 | Style cart with Tailwind | Medium | DONE ✅ | T4.3 |
| T4.12 | Style checkout with Tailwind | Medium | DONE ✅ | T4.6 |
| T4.13 | Create Button Blade Component | Low | DONE ✅ | T1.7 |

---

### Phase 5: User Account Pages (MVP Core)
> **Goal:** My Account dashboard and order management

| ID | Task | Complexity | Status | Dependencies |
|----|------|------------|--------|--------------|
| T5.1 | Create my-account.blade.php template | Medium | DONE ✅ | T1.7 |
| T5.2 | Create form-login.blade.php template | Medium | DONE ✅ | T5.1 |
| T5.3 | Create orders.blade.php template | Medium | DONE ✅ | T5.1 |
| T5.4 | Create view-order.blade.php template | Medium | DONE ✅ | T5.3 |
| T5.5 | Create account navigation component | Medium | DONE ✅ | T5.1 |
| T5.6 | Style account pages with Tailwind | Medium | DONE ✅ | T5.1 |
| T5.7 | Implement account dashboard widgets | Medium | DONE ✅ | T5.1 |

---

### Phase 6: Performance & Polish
> **Goal:** Optimization, hooks, and final polish

| ID | Task | Complexity | Status | Dependencies |
|----|------|------------|--------|--------------|
| T6.1 | Remove default WooCommerce styles | Low | DONE ✅ | T2.2 |
| T6.2 | Implement conditional script loading | Medium | DONE ✅ | T4.2 |
| T6.3 | Add lazy loading for images | Low | DONE ✅ | T2.3 |
| T6.4 | Implement transient caching for queries | Medium | DONE ✅ | T2.1 |
| T6.5 | Optimize cart fragments | Medium | DONE ✅ | T4.1 |
| T6.6 | Configure Vite production build | Medium | DONE ✅ | T1.4 |
| T6.7 | Run wp acorn view:cache | Low | DONE ✅ | T6.6 |
| T6.8 | Customize WooCommerce hooks in app/filters.php | Medium | DONE ✅ | T1.6 |
| T6.9 | Add custom sale badge with percentage | Low | DONE ✅ | T6.8 |
| T6.10 | Customize checkout fields | Medium | DONE ✅ | T4.6 |

---

### Phase 7: Deployment
> **Goal:** Deploy theme to production (Hostinger)

| ID | Task | Complexity | Status | Dependencies |
|----|------|------------|--------|--------------|
| T7.1 | Update vite.config.js for production | Low | DONE ✅ | T6.6 |
| T7.2 | Build production assets (npm run build) | Low | DONE ✅ | T7.1 |
| T7.3 | Upload theme to Hostinger | Medium | DONE ✅ | T7.2 |
| T7.4 | Run wp acorn optimize on production | Low | DONE ✅ | T7.3 |
| T7.5 | Test all WooCommerce functionality | High | DONE ✅ | T7.4 |
| T7.6 | Configure SSL and permalinks | Low | DONE ✅ | T7.3 |

---

### Phase 8: Search Popup (ძიების პოპაპი)
> **Goal:** Implement AJAX search with popup overlay showing categories and products

| ID | Task | Complexity | Status | Dependencies |
|----|------|------------|--------|--------------|
| T8.1 | Create search-popup.blade.php component | Medium | DONE ✅ | T1.8 |
| T8.2 | Add search icon trigger in header | Low | DONE ✅ | T8.1 |
| T8.3 | Implement AJAX product search with WP REST API | High | DONE ✅ | T8.1 |
| T8.4 | Create search results display (categories + products) | High | DONE ✅ | T8.3 |
| T8.5 | Add popup animation (center screen overlay) | Medium | DONE ✅ | T8.1 |
| T8.6 | Implement keyboard navigation (ESC to close) | Low | DONE ✅ | T8.5 |
| T8.7 | Style search popup with Tailwind | Medium | DONE ✅ | T8.4 |

---

### Phase 9: Homepage Design (მთავარი გვერდი)
> **Goal:** Create homepage with mega-menu (20%) + slider (80%) hero section and product carousels

| ID | Task | Complexity | Status | Dependencies |
|----|------|------------|--------|--------------|
| T9.1 | Create front-page.blade.php template | High | DONE ✅ | T1.7 |
| T9.2 | Create mega-menu component (20% width) | High | DONE ✅ | T9.1 |
| T9.3 | Integrate/Create slider component (80% width) | High | DONE ✅ | T9.1 |
| T9.4 | Create Hero section layout (mega-menu + slider) | Medium | DONE ✅ | T9.2, T9.3 |
| T9.5 | Create ProductCarousel Blade Component | High | DONE ✅ | T2.3 |
| T9.6 | Create "New Products" carousel section | Medium | DONE ✅ | T9.5 |
| T9.7 | Create "On Sale" products carousel section | Medium | DONE ✅ | T9.5 |
| T9.8 | Create "Bestsellers" carousel section | Medium | DONE ✅ | T9.5 |
| T9.9 | Create Homepage View Composer for data | High | DONE ✅ | T9.1 |
| T9.10 | Style homepage sections with Tailwind | Medium | DONE ✅ | T9.8 |

---

### Phase 10: Cart & Checkout Redesign (კალათა და ჩექაუთი)
> **Goal:** Modernize cart and checkout pages with improved UX and design

| ID | Task | Complexity | Status | Dependencies |
|----|------|------------|--------|--------------|
| T10.1 | Redesign cart.blade.php layout | Medium | DONE ✅ | T4.3 |
| T10.2 | Add cart summary sidebar | Medium | DONE ✅ | T10.1 |
| T10.3 | Improve quantity controls design | Low | DONE ✅ | T10.1 |
| T10.4 | Redesign checkout form-checkout.blade.php | High | DONE ✅ | T4.6 |
| T10.5 | Create two-column checkout layout | Medium | DONE ✅ | T10.4 |
| T10.6 | Improve form field styling | Medium | DONE ✅ | T10.4 |
| T10.7 | Add order review section improvements | Medium | DONE ✅ | T10.4 |
| T10.8 | Style cart & checkout with modern Tailwind | Medium | DONE ✅ | T10.7 |

---

### Phase 11: Product Page Improvements (პროდუქტის გვერდი)
> **Goal:** Enhance variable products, gallery, tabs, and attribute display types

| ID | Task | Complexity | Status | Dependencies |
|----|------|------------|--------|--------------|
| T11.1 | Create attribute type system (color, button, select) in theme | High | DONE ✅ | - |
| T11.2 | Build admin UI for attribute type selection | High | DONE ✅ | T11.1 |
| T11.3 | Implement color swatch display with color picker | Medium | DONE ✅ | T11.2 |
| T11.4 | Implement button-style attribute display | Medium | DONE ✅ | T11.2 |
| T11.5 | Implement select dropdown attribute display | Low | DONE ✅ | T11.2 |
| T11.6 | Support multiple attributes per variable product | High | DONE ✅ | T11.3, T11.4, T11.5 |
| T11.7 | Remove Reviews and Additional Info from tabs (keep only Description) | Low | DONE ✅ | - |
| T11.8 | Remove inner scrolls from product page (unified scroll) | Medium | DONE ✅ | - |
| T11.9 | Create thumbnail carousel for product gallery (4 visible) | Medium | DONE ✅ | - |
| T11.10 | Style and test variable product attribute selectors | Medium | TODO | T11.6 |

---

### Phase 12: Cart Page Improvements (კალათის გვერდი)
> **Goal:** Polish cart styles and improve mini-cart design

| ID | Task | Complexity | Status | Dependencies |
|----|------|------------|--------|--------------|
| T12.1 | Remove yellow border on hover from Order Summary container | Low | TODO | - |
| T12.2 | Remove hover effects from cart item elements | Low | TODO | - |
| T12.3 | Redesign mini-cart to match cart page styles | Medium | TODO | T12.1, T12.2 |
| T12.4 | Unify cart and mini-cart typography and spacing | Low | TODO | T12.3 |
| T12.5 | Test cart page responsiveness | Low | TODO | T12.4 |

---

### Phase 13: Homepage Improvements (მთავარი გვერდი)
> **Goal:** Categories carousel, slider customizer, remove unused components

| ID | Task | Complexity | Status | Dependencies |
|----|------|------------|--------|--------------|
| T13.1 | Convert categories component to carousel (6 visible, show all) | Medium | TODO | - |
| T13.2 | Create Customizer section for slider banners | High | TODO | - |
| T13.3 | Add 5 slide slots with image, title, subtitle, link fields | Medium | TODO | T13.2 |
| T13.4 | Connect slider component to Customizer settings | Medium | TODO | T13.3 |
| T13.5 | Remove trust-badges-grid component | Low | TODO | - |
| T13.6 | Remove side-by-side banners after new products section | Low | TODO | - |
| T13.7 | Test homepage carousel and slider functionality | Low | TODO | T13.4 |

---

### Phase 14: Header Improvements (ჰედერი)
> **Goal:** Remove account icon, add mega-menu button to all pages

| ID | Task | Complexity | Status | Dependencies |
|----|------|------------|--------|--------------|
| T14.1 | Remove account icon from header | Low | TODO | - |
| T14.2 | Create categories dropdown button below logo | Medium | TODO | - |
| T14.3 | Connect dropdown to mega-menu component | Medium | TODO | T14.2 |
| T14.4 | Implement subcategory hover reveal (like homepage) | Medium | TODO | T14.3 |
| T14.5 | Ensure mega-menu works on all pages (not just homepage) | Medium | TODO | T14.4 |
| T14.6 | Test header navigation and mega-menu | Low | TODO | T14.5 |

---

### Phase 15: Shop Page Improvements (შოპის გვერდი)
> **Goal:** Products per page, price range filter, category checkboxes, category template

| ID | Task | Complexity | Status | Dependencies |
|----|------|------------|--------|--------------|
| T15.1 | Add products per page selector (12/24/48/96) | Medium | TODO | - |
| T15.2 | Move price filter to top position | Low | TODO | - |
| T15.3 | Implement dual-handle price range slider | High | TODO | T15.2 |
| T15.4 | Convert category filter to checkbox style | Medium | TODO | - |
| T15.5 | Enable multi-category selection | Medium | TODO | T15.4 |
| T15.6 | Implement smart subcategory logic (show only selected subcategories) | High | TODO | T15.5 |
| T15.7 | Create taxonomy-product_cat.blade.php template | Medium | TODO | - |
| T15.8 | Show only subcategories in category page filters | Medium | TODO | T15.7 |
| T15.9 | Remove Availability filter component | Low | TODO | - |
| T15.10 | Test shop filters and category template | Medium | TODO | T15.8 |

---

### Phase 16: Search Functionality (სერჩის ფუნქციები)
> **Goal:** Fix search results pagination

| ID | Task | Complexity | Status | Dependencies |
|----|------|------------|--------|--------------|
| T16.1 | Create search-product.blade.php template | Medium | TODO | - |
| T16.2 | Create SearchResults View Composer | Medium | TODO | T16.1 |
| T16.3 | Implement proper WP_Query for product search | High | TODO | T16.2 |
| T16.4 | Add pagination support to search results | Medium | TODO | T16.3 |
| T16.5 | Style search results page (match shop page) | Low | TODO | T16.4 |
| T16.6 | Test search with pagination | Low | TODO | T16.5 |

---

### Phase 17: Performance Optimization (პერფორმანსი)
> **Goal:** Maximize site speed and optimize for Core Web Vitals

| ID | Task | Complexity | Status | Dependencies |
|----|------|------------|--------|--------------|
| T17.1 | Audit current performance (Lighthouse, GTmetrix) | Medium | TODO | - |
| T17.2 | Implement critical CSS inlining | High | TODO | T17.1 |
| T17.3 | Optimize image loading (WebP, lazy load, srcset) | Medium | TODO | T17.1 |
| T17.4 | Minimize JavaScript bundle size | Medium | TODO | T17.1 |
| T17.5 | Add preconnect/prefetch for external resources | Low | TODO | T17.1 |
| T17.6 | Implement object caching recommendations | Medium | TODO | T17.1 |
| T17.7 | Optimize database queries in View Composers | High | TODO | T17.1 |
| T17.8 | Configure CDN recommendations | Medium | TODO | T17.1 |
| T17.9 | Add service worker for caching (optional) | High | TODO | T17.8 |
| T17.10 | Final performance audit and report | Medium | TODO | T17.9 |

---

## File Structure (Target)

```
sega-woo-theme/
├── app/
│   ├── Providers/
│   │   └── ThemeServiceProvider.php
│   ├── View/
│   │   ├── Composers/
│   │   │   ├── App.php
│   │   │   ├── Shop.php
│   │   │   └── Product.php
│   │   └── Components/
│   │       ├── ProductCard.php
│   │       ├── Button.php
│   │       └── Price.php
│   ├── filters.php
│   ├── setup.php
│   └── helpers.php
│
├── config/
│   ├── app.php
│   └── view.php
│
├── resources/
│   ├── views/
│   │   ├── layouts/
│   │   │   └── app.blade.php
│   │   ├── partials/
│   │   │   ├── header.blade.php
│   │   │   └── footer.blade.php
│   │   ├── components/
│   │   │   ├── product-card.blade.php
│   │   │   ├── button.blade.php
│   │   │   └── price.blade.php
│   │   └── woocommerce/
│   │       ├── archive-product.blade.php
│   │       ├── single-product.blade.php
│   │       ├── cart/
│   │       ├── checkout/
│   │       ├── myaccount/
│   │       ├── single-product/
│   │       ├── loop/
│   │       └── global/
│   ├── scripts/
│   │   ├── app.js
│   │   └── components/
│   │       └── mini-cart.js
│   └── styles/
│       ├── app.css
│       └── woocommerce/
│
├── bud.config.js
├── tailwind.config.js
├── composer.json
├── package.json
└── functions.php
```

---

## Original Specification Analysis

**Source Document:** sage-woocommerce-guide.md

### Extracted Requirements
- Modern PHP development practices with Laravel components
- Blade templating for clean, maintainable templates
- View Composers for separating data logic from views
- Blade Components for reusable UI elements
- Full WooCommerce template override support
- Bud-based asset pipeline with hot reloading
- Tailwind CSS for utility-first styling
- Performance optimization (view caching, asset minification)

### Clarifications Made
- **Theme Purpose:** General Marketplace (user selected)
- **MVP Features:** All core features selected (Shop, Product, Cart, Account)
- **Styling Approach:** Tailwind CSS Only (as recommended)
- **Deployment:** Local Sites for development, Hostinger for production

---

## Progress Summary

| Phase | Tasks | Completed | Progress |
|-------|-------|-----------|----------|
| Phase 1: Foundation | 11 | 11 | 100% |
| Phase 2: Shop/Archive | 12 | 12 | 100% |
| Phase 3: Single Product | 12 | 12 | 100% |
| Phase 4: Cart & Checkout | 13 | 13 | 100% |
| Phase 5: User Account | 7 | 7 | 100% |
| Phase 6: Performance | 10 | 10 | 100% |
| Phase 7: Deployment | 6 | 6 | 100% |
| Phase 8: Search Popup | 7 | 7 | 100% |
| Phase 9: Homepage | 10 | 10 | 100% |
| Phase 10: Cart/Checkout Redesign | 8 | 8 | 100% |
| Phase 11: Product Page Improvements | 10 | 9 | 90% |
| Phase 12: Cart Page Improvements | 5 | 0 | 0% |
| Phase 13: Homepage Improvements | 7 | 0 | 0% |
| Phase 14: Header Improvements | 6 | 0 | 0% |
| Phase 15: Shop Page Improvements | 10 | 0 | 0% |
| Phase 16: Search Functionality | 6 | 0 | 0% |
| Phase 17: Performance Optimization | 10 | 0 | 0% |
| **TOTAL** | **150** | **105** | **70%** |

---

## Quick Reference

### Essential Commands
```bash
# Development
npm run dev          # Start dev server with hot reload
npm run build        # Production build

# Acorn
wp acorn optimize    # Generate cached files
wp acorn view:cache  # Cache compiled views
wp acorn view:clear  # Clear view cache

# Composer
composer install     # Install PHP dependencies
composer update      # Update PHP dependencies
```

### Key Blade Syntax
```blade
{{ $var }}              # Escaped output
{!! $html !!}           # Unescaped output (HTML)
@if @elseif @else @endif
@foreach @endforeach
@extends('layout')
@section('name') @endsection
@yield('name')
@include('partial', ['key' => $value])
<x-component :prop="$value" />
@class(['base', 'conditional' => $bool])
```

---

*Generated from sage-woocommerce-guide.md specification*
