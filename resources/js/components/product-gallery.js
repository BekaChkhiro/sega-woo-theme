import Swiper from 'swiper';
import { Navigation } from 'swiper/modules';

/**
 * Product Gallery Component
 * Handles main image display, thumbnail carousel, and lightbox
 */
export default function productGallery() {
  return {
    images: [],
    originalImages: [],
    currentIndex: 0,
    lightboxOpen: false,
    thumbnailSwiper: null,

    init() {
      // Get images from data attribute
      const imagesData = this.$el.dataset.images;
      if (imagesData) {
        try {
          this.images = JSON.parse(imagesData);
          this.originalImages = JSON.parse(imagesData);
        } catch (e) {
          console.error('Failed to parse gallery images:', e);
        }
      }

      // Initialize thumbnail swiper after Alpine renders x-for slides
      // Use double nextTick to ensure DOM is fully updated
      this.$nextTick(() => {
        this.$nextTick(() => {
          this.initThumbnailSwiper();
        });
      });
    },

    initThumbnailSwiper() {
      const swiperEl = this.$refs.thumbnailSwiper;
      const prevEl = this.$refs.thumbPrev;
      const nextEl = this.$refs.thumbNext;

      if (!swiperEl || this.images.length <= 1) return;

      this.thumbnailSwiper = new Swiper(swiperEl, {
        modules: [Navigation],
        slidesPerView: 4,
        spaceBetween: 8,
        watchSlidesProgress: true,
        slideToClickedSlide: false,

        navigation: {
          nextEl: nextEl,
          prevEl: prevEl,
          disabledClass: 'opacity-40 cursor-not-allowed',
        },

        breakpoints: {
          640: {
            spaceBetween: 12,
          },
        },
      });
    },

    slideThumbnailTo(index) {
      if (this.thumbnailSwiper) {
        // Slide to make the selected thumbnail visible
        const slidesPerView = this.thumbnailSwiper.params.slidesPerView;
        const targetSlide = Math.max(0, index - Math.floor(slidesPerView / 2));
        this.thumbnailSwiper.slideTo(targetSlide);
      }
    },

    setImage(index) {
      this.currentIndex = index;
    },

    nextImage() {
      if (this.images.length > 0) {
        this.currentIndex = (this.currentIndex + 1) % this.images.length;
        this.slideThumbnailTo(this.currentIndex);
      }
    },

    previousImage() {
      if (this.images.length > 0) {
        this.currentIndex = (this.currentIndex - 1 + this.images.length) % this.images.length;
        this.slideThumbnailTo(this.currentIndex);
      }
    },

    openLightbox() {
      if (this.images.length > 0) {
        this.lightboxOpen = true;
        document.body.style.overflow = 'hidden';
      }
    },

    closeLightbox() {
      this.lightboxOpen = false;
      document.body.style.overflow = '';
    },

    // Update gallery when variation changes (for variable products)
    updateGalleryImage(imageUrl, fullUrl, thumbUrl, alt, srcset, sizes) {
      const variationImage = {
        id: 0,
        url: imageUrl,
        full_url: fullUrl,
        thumb_url: thumbUrl,
        alt: alt,
        srcset: srcset || '',
        sizes: sizes || '',
        is_main: true,
        is_variation: true
      };

      // Remove any previously inserted variation image and reset
      this.images = this.originalImages.filter(img => !img.is_variation);

      // Add variation image at the start
      this.images.unshift(variationImage);
      this.currentIndex = 0;

      // Update thumbnail swiper if needed
      if (this.thumbnailSwiper) {
        this.$nextTick(() => {
          this.thumbnailSwiper.update();
          this.thumbnailSwiper.slideTo(0);
        });
      }
    },

    // Reset to original images
    resetGallery() {
      this.images = [...this.originalImages];
      this.currentIndex = 0;

      // Reset thumbnail swiper
      if (this.thumbnailSwiper) {
        this.$nextTick(() => {
          this.thumbnailSwiper.update();
          this.thumbnailSwiper.slideTo(0);
        });
      }
    },

    destroy() {
      if (this.thumbnailSwiper) {
        this.thumbnailSwiper.destroy(true, true);
        this.thumbnailSwiper = null;
      }
    }
  };
}
