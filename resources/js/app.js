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

Alpine.plugin(collapse);

// Register components
Alpine.data('miniCart', miniCart);
Alpine.data('toast', toast);
Alpine.data('searchPopup', searchPopup);
Alpine.data('heroSlider', heroSlider);
Alpine.data('productCarousel', productCarousel);
Alpine.data('productGallery', productGallery);

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
