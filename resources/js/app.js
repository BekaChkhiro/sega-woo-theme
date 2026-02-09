import.meta.glob([
  '../images/**',
  '../fonts/**',
]);

/**
 * Swiper Slider - Core styles
 */
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';
import 'swiper/css/effect-fade';

/**
 * Alpine.js - Lightweight JavaScript framework for interactive components
 */
import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';

/**
 * Alpine.js Components
 */
import miniCart from './components/mini-cart';
import toast from './components/toast';
import searchPopup from './components/search-popup';
import heroSlider from './components/hero-slider';
import productCarousel from './components/product-carousel';
import productGallery from './components/product-gallery';
import priceRangeSlider from './components/price-range-slider';
import categoryFilter from './components/category-filter';
import shopFilters from './components/shop-filters';
import availabilityFilter from './components/availability-filter';
import perPageFilter from './components/per-page-filter';
import orderbyFilter from './components/orderby-filter';
import paginationFilter from './components/pagination-filter';
import subcategoryCarousel from './components/subcategory-carousel';

Alpine.plugin(collapse);

// Register components
Alpine.data('miniCart', miniCart);
Alpine.data('toast', toast);
Alpine.data('searchPopup', searchPopup);
Alpine.data('heroSlider', heroSlider);
Alpine.data('productCarousel', productCarousel);
Alpine.data('productGallery', productGallery);
Alpine.data('priceRangeSlider', priceRangeSlider);
Alpine.data('categoryFilter', categoryFilter);
Alpine.data('shopFilters', shopFilters);
Alpine.data('availabilityFilter', availabilityFilter);
Alpine.data('perPageFilter', perPageFilter);
Alpine.data('orderbyFilter', orderbyFilter);
Alpine.data('paginationFilter', paginationFilter);
Alpine.data('subcategoryCarousel', subcategoryCarousel);

window.Alpine = Alpine;
Alpine.start();

/**
 * Mobile Menu Toggle
 */
document.addEventListener('DOMContentLoaded', () => {
  const menuToggle = document.querySelector('[data-mobile-menu-toggle]');
  const mobileMenu = document.querySelector('[data-mobile-menu]');

  if (menuToggle && mobileMenu) {
    menuToggle.addEventListener('click', () => {
      const isExpanded = menuToggle.getAttribute('aria-expanded') === 'true';
      menuToggle.setAttribute('aria-expanded', !isExpanded);
      mobileMenu.classList.toggle('hidden');
    });
  }
});
