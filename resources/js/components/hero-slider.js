import Swiper from 'swiper';
import { Navigation, Pagination, Autoplay, EffectFade } from 'swiper/modules';

/**
 * Hero Slider Component
 * Uses Swiper.js for smooth, touch-friendly slider functionality
 */
export default function heroSlider() {
  return {
    swiper: null,
    currentSlide: 0,
    totalSlides: 0,
    isAutoplayPaused: false,

    init() {
      this.$nextTick(() => {
        this.initSwiper();
      });
    },

    initSwiper() {
      const swiperEl = this.$refs.swiper;
      if (!swiperEl) return;

      this.swiper = new Swiper(swiperEl, {
        modules: [Navigation, Pagination, Autoplay, EffectFade],

        // Effect
        effect: 'fade',
        fadeEffect: {
          crossFade: true
        },

        // Autoplay
        autoplay: {
          delay: 5000,
          disableOnInteraction: false,
          pauseOnMouseEnter: true,
        },

        // Speed
        speed: 600,

        // Loop
        loop: true,

        // Pagination (dots)
        pagination: {
          el: this.$refs.pagination,
          clickable: true,
          bulletClass: 'hero-slider-bullet',
          bulletActiveClass: 'hero-slider-bullet-active',
          renderBullet: (index, className) => {
            return `<button class="${className}" aria-label="Go to slide ${index + 1}"></button>`;
          },
        },

        // Navigation (arrows)
        navigation: {
          nextEl: this.$refs.next,
          prevEl: this.$refs.prev,
        },

        // Accessibility
        a11y: {
          prevSlideMessage: 'Previous slide',
          nextSlideMessage: 'Next slide',
          paginationBulletMessage: 'Go to slide {{index}}',
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
      }
    },
  };
}
