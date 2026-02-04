# Sage WooCommerce Theme - Project Plan

> **Project Type:** Full-Stack WordPress/WooCommerce Theme
> **Framework:** Sage 10 (Roots)
> **Created:** 2026-02-02
> **Last Updated:** 2026-02-04
> **Current Focus:** T4.11 - Style cart with Tailwind
> **Status:** In Progress
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
| T4.11 | Style cart with Tailwind | Medium | TODO | T4.3 |
| T4.12 | Style checkout with Tailwind | Medium | TODO | T4.6 |
| T4.13 | Create Button Blade Component | Low | TODO | T1.7 |

---

### Phase 5: User Account Pages (MVP Core)
> **Goal:** My Account dashboard and order management

| ID | Task | Complexity | Status | Dependencies |
|----|------|------------|--------|--------------|
| T5.1 | Create my-account.blade.php template | Medium | TODO | T1.7 |
| T5.2 | Create form-login.blade.php template | Medium | TODO | T5.1 |
| T5.3 | Create orders.blade.php template | Medium | TODO | T5.1 |
| T5.4 | Create view-order.blade.php template | Medium | TODO | T5.3 |
| T5.5 | Create account navigation component | Medium | TODO | T5.1 |
| T5.6 | Style account pages with Tailwind | Medium | TODO | T5.1 |
| T5.7 | Implement account dashboard widgets | Medium | TODO | T5.1 |

---

### Phase 6: Performance & Polish
> **Goal:** Optimization, hooks, and final polish

| ID | Task | Complexity | Status | Dependencies |
|----|------|------------|--------|--------------|
| T6.1 | Remove default WooCommerce styles | Low | TODO | T2.2 |
| T6.2 | Implement conditional script loading | Medium | TODO | T4.2 |
| T6.3 | Add lazy loading for images | Low | TODO | T2.3 |
| T6.4 | Implement transient caching for queries | Medium | TODO | T2.1 |
| T6.5 | Optimize cart fragments | Medium | TODO | T4.1 |
| T6.6 | Configure Vite production build | Medium | TODO | T1.4 |
| T6.7 | Run wp acorn view:cache | Low | TODO | T6.6 |
| T6.8 | Customize WooCommerce hooks in app/filters.php | Medium | TODO | T1.6 |
| T6.9 | Add custom sale badge with percentage | Low | TODO | T6.8 |
| T6.10 | Customize checkout fields | Medium | TODO | T4.6 |

---

### Phase 7: Deployment
> **Goal:** Deploy theme to production (Hostinger)

| ID | Task | Complexity | Status | Dependencies |
|----|------|------------|--------|--------------|
| T7.1 | Update vite.config.js for production | Low | TODO | T6.6 |
| T7.2 | Build production assets (npm run build) | Low | TODO | T7.1 |
| T7.3 | Upload theme to Hostinger | Medium | TODO | T7.2 |
| T7.4 | Run wp acorn optimize on production | Low | TODO | T7.3 |
| T7.5 | Test all WooCommerce functionality | High | TODO | T7.4 |
| T7.6 | Configure SSL and permalinks | Low | TODO | T7.3 |

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
| Phase 4: Cart & Checkout | 13 | 10 | 77% |
| Phase 5: User Account | 7 | 0 | 0% |
| Phase 6: Performance | 10 | 0 | 0% |
| Phase 7: Deployment | 6 | 0 | 0% |
| **TOTAL** | **71** | **45** | **63%** |

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
