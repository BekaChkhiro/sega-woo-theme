import Swiper from 'swiper';
import { Navigation, FreeMode } from 'swiper/modules';

/**
 * Subcategory Carousel Component
 * Horizontal scrollable carousel for subcategory links
 */
export default function subcategoryCarousel() {
  return {
    swiper: null,

    init() {
      this.$nextTick(() => {
        this.initSwiper();
      });
    },

    initSwiper() {
      const container = this.$refs.swiperContainer;
      if (!container) return;

      this.swiper = new Swiper(container, {
        modules: [Navigation, FreeMode],
        slidesPerView: 'auto',
        spaceBetween: 8,
        freeMode: {
          enabled: true,
          sticky: false,
        },
        navigation: {
          prevEl: this.$refs.prevBtn,
          nextEl: this.$refs.nextBtn,
        },
        breakpoints: {
          640: {
            spaceBetween: 10,
          },
          1024: {
            spaceBetween: 12,
          },
        },
      });
    },

    destroy() {
      if (this.swiper) {
        this.swiper.destroy(true, true);
        this.swiper = null;
      }
    },
  };
}
