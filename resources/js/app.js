import.meta.glob([
  '../images/**',
  '../fonts/**',
]);

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

Alpine.plugin(collapse);

// Register components
Alpine.data('miniCart', miniCart);
Alpine.data('toast', toast);

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
