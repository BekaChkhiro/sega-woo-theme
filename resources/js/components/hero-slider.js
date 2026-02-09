import Swiper from 'swiper';
import { Navigation, Pagination, Autoplay, EffectFade } from 'swiper/modules';

/**
 * Hero Slider Component
 * Uses Swiper.js for smooth, touch-friendly slider functionality
 *
 * @param {Object} config - Configuration options
 * @param {boolean} config.autoplay - Enable autoplay (default: true)
 * @param {number} config.delay - Autoplay delay in ms (default: 5000)
 * @param {boolean} config.showNavigation - Show navigation arrows (default: true)
 * @param {boolean} config.showPagination - Show pagination dots (default: true)
 */
export default function heroSlider(config = {}) {
  return {
    swiper: null,
    currentSlide: 0,
    totalSlides: 0,
    isAutoplayPaused: false,
    config: {
      autoplay: config.autoplay ?? true,
      delay: config.delay ?? 5000,
      showNavigation: config.showNavigation ?? true,
      showPagination: config.showPagination ?? true,
    },

    init() {
      this.$nextTick(() => {
        this.initSwiper();
      });
    },

    initSwiper() {
      const swiperEl = this.$refs.swiper;
      if (!swiperEl) return;

      // Destroy existing instance if any
      if (this.swiper) {
        this.swiper.destroy(true, true);
      }

      const autoplayConfig = this.config.autoplay ? {
        delay: this.config.delay,
        disableOnInteraction: false,
        pauseOnMouseEnter: true,
      } : false;

      this.swiper = new Swiper(swiperEl, {
        modules: [Navigation, Pagination, Autoplay, EffectFade],

        // Effect
        effect: 'fade',
        fadeEffect: {
          crossFade: true
        },

        // Autoplay
        autoplay: autoplayConfig,

        // Speed
        speed: 600,

        // Loop
        loop: true,

        // Pagination (dots) - always initialize for live preview toggle
        pagination: {
          el: this.$refs.pagination,
          clickable: true,
          bulletClass: 'hero-slider-bullet',
          bulletActiveClass: 'hero-slider-bullet-active',
          renderBullet: (index, className) => {
            return `<button class="${className}" aria-label="Go to slide ${index + 1}"></button>`;
          },
        },

        // Navigation (arrows) - always initialize for live preview toggle
        navigation: {
          nextEl: this.$refs.next,
          prevEl: this.$refs.prev,
        },

        // Accessibility (use translated strings if available)
        a11y: {
          prevSlideMessage: window.segaThemeI18n?.prevSlide || 'Previous slide',
          nextSlideMessage: window.segaThemeI18n?.nextSlide || 'Next slide',
          paginationBulletMessage: window.segaThemeI18n?.goToSlide || 'Go to slide {{index}}',
        },

        // Events
        on: {
          init: (swiper) => {
            this.totalSlides = swiper.slides.length;
            this.currentSlide = swiper.realIndex;
          },
          slideChange: (swiper) => {
            this.currentSlide = swiper.realIndex;
          },
          autoplayPause: () => {
            this.isAutoplayPaused = true;
          },
          autoplayResume: () => {
            this.isAutoplayPaused = false;
          },
        },
      });

      // Expose Swiper instance globally for Customizer live preview
      window.heroSliderSwiper = this.swiper;
    },

    goToSlide(index) {
      if (this.swiper) {
        this.swiper.slideToLoop(index);
      }
    },

    nextSlide() {
      if (this.swiper) {
        this.swiper.slideNext();
      }
    },

    prevSlide() {
      if (this.swiper) {
        this.swiper.slidePrev();
      }
    },

    toggleAutoplay() {
      if (!this.swiper) return;

      if (this.swiper.autoplay.running) {
        this.swiper.autoplay.stop();
        this.isAutoplayPaused = true;
      } else {
        this.swiper.autoplay.start();
        this.isAutoplayPaused = false;
      }
    },

    destroy() {
      if (this.swiper) {
        this.swiper.destroy(true, true);
        this.swiper = null;
        window.heroSliderSwiper = null;
      }
    },
  };
}
