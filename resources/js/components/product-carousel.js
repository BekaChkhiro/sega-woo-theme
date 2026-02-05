import Swiper from 'swiper';
import { Navigation, Pagination, Autoplay } from 'swiper/modules';

/**
 * Product Carousel Component
 * Uses Swiper.js for smooth, touch-friendly product carousel
 *
 * @param {Object} config - Swiper configuration options
 * @returns {Object} Alpine.js component
 */
export default function productCarousel(config = {}) {
  return {
    swiper: null,
    currentSlide: 0,
    totalSlides: 0,
    isAutoplayPaused: false,

    /**
     * Initialize the component
     */
    init() {
      this.$nextTick(() => {
        this.initSwiper();
      });
    },

    /**
     * Initialize Swiper instance
     */
    initSwiper() {
      const swiperEl = this.$refs.swiper;
      if (!swiperEl) return;

      // Merge default config with passed config
      const defaultConfig = {
        modules: [Navigation, Pagination, Autoplay],

        // Slides
        slidesPerView: 1,
        spaceBetween: 12,

        // Speed
        speed: 500,

        // Loop
        loop: false,

        // Touch
        grabCursor: true,
        touchRatio: 1,
        touchAngle: 45,
        threshold: 10,

        // Responsive breakpoints
        breakpoints: {
          // Mobile (>= 480px)
          480: {
            slidesPerView: 2,
            spaceBetween: 12,
          },
          // Tablet (>= 768px)
          768: {
            slidesPerView: 3,
            spaceBetween: 16,
          },
          // Desktop (>= 1024px)
          1024: {
            slidesPerView: 4,
            spaceBetween: 20,
          },
          // Large Desktop (>= 1280px)
          1280: {
            slidesPerView: 4,
            spaceBetween: 24,
          },
        },

        // Accessibility
        a11y: {
          enabled: true,
          prevSlideMessage: 'Previous products',
          nextSlideMessage: 'Next products',
          paginationBulletMessage: 'Go to product {{index}}',
        },

        // Keyboard
        keyboard: {
          enabled: true,
          onlyInViewport: true,
        },

        // Mouse wheel (optional, can be disabled)
        mousewheel: {
          enabled: false,
        },
      };

      // Add navigation if refs exist
      if (this.$refs.next && this.$refs.prev) {
        defaultConfig.navigation = {
          nextEl: this.$refs.next,
          prevEl: this.$refs.prev,
          disabledClass: 'opacity-40 cursor-not-allowed',
        };
      }

      // Add pagination if ref exists
      if (this.$refs.pagination) {
        defaultConfig.pagination = {
          el: this.$refs.pagination,
          clickable: true,
          bulletClass: 'product-carousel-bullet',
          bulletActiveClass: 'product-carousel-bullet-active',
          renderBullet: (index, className) => {
            return `<button class="${className}" aria-label="Go to products ${index + 1}"></button>`;
          },
        };
      }

      // Merge configs (passed config overrides defaults)
      const finalConfig = this.deepMerge(defaultConfig, config);

      // Add event handlers
      finalConfig.on = {
        init: (swiper) => {
          this.totalSlides = swiper.slides.length;
          this.currentSlide = swiper.realIndex;
          this.updateNavigationState(swiper);
        },
        slideChange: (swiper) => {
          this.currentSlide = swiper.realIndex;
          this.updateNavigationState(swiper);
        },
        autoplayPause: () => {
          this.isAutoplayPaused = true;
        },
        autoplayResume: () => {
          this.isAutoplayPaused = false;
        },
        reachBeginning: (swiper) => {
          this.updateNavigationState(swiper);
        },
        reachEnd: (swiper) => {
          this.updateNavigationState(swiper);
        },
      };

      // Initialize Swiper
      this.swiper = new Swiper(swiperEl, finalConfig);
    },

    /**
     * Update navigation button states
     * @param {Swiper} swiper - Swiper instance
     */
    updateNavigationState(swiper) {
      if (!this.$refs.prev || !this.$refs.next) return;

      // Update disabled state for non-loop carousels
      if (!swiper.params.loop) {
        if (swiper.isBeginning) {
          this.$refs.prev.setAttribute('disabled', 'true');
        } else {
          this.$refs.prev.removeAttribute('disabled');
        }

        if (swiper.isEnd) {
          this.$refs.next.setAttribute('disabled', 'true');
        } else {
          this.$refs.next.removeAttribute('disabled');
        }
      }
    },

    /**
     * Go to specific slide
     * @param {number} index - Slide index
     */
    goToSlide(index) {
      if (this.swiper) {
        if (this.swiper.params.loop) {
          this.swiper.slideToLoop(index);
        } else {
          this.swiper.slideTo(index);
        }
      }
    },

    /**
     * Go to next slide
     */
    nextSlide() {
      if (this.swiper) {
        this.swiper.slideNext();
      }
    },

    /**
     * Go to previous slide
     */
    prevSlide() {
      if (this.swiper) {
        this.swiper.slidePrev();
      }
    },

    /**
     * Toggle autoplay
     */
    toggleAutoplay() {
      if (!this.swiper || !this.swiper.params.autoplay) return;

      if (this.swiper.autoplay.running) {
        this.swiper.autoplay.stop();
        this.isAutoplayPaused = true;
      } else {
        this.swiper.autoplay.start();
        this.isAutoplayPaused = false;
      }
    },

    /**
     * Deep merge two objects
     * @param {Object} target - Target object
     * @param {Object} source - Source object
     * @returns {Object} Merged object
     */
    deepMerge(target, source) {
      const output = { ...target };

      if (this.isObject(target) && this.isObject(source)) {
        Object.keys(source).forEach((key) => {
          if (this.isObject(source[key])) {
            if (!(key in target)) {
              Object.assign(output, { [key]: source[key] });
            } else {
              output[key] = this.deepMerge(target[key], source[key]);
            }
          } else {
            Object.assign(output, { [key]: source[key] });
          }
        });
      }

      return output;
    },

    /**
     * Check if value is an object
     * @param {*} item - Value to check
     * @returns {boolean}
     */
    isObject(item) {
      return item && typeof item === 'object' && !Array.isArray(item);
    },

    /**
     * Destroy Swiper instance
     */
    destroy() {
      if (this.swiper) {
        this.swiper.destroy(true, true);
        this.swiper = null;
      }
    },
  };
}
