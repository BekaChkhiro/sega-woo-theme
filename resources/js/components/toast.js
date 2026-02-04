/**
 * Toast Notification Alpine.js Component
 *
 * Displays temporary toast notifications for cart actions and other feedback.
 */
export default function toast() {
  return {
    toasts: [],
    counter: 0,

    init() {
      // Listen for toast events
      document.body.addEventListener('show-toast', (e) => {
        this.show(e.detail.message, e.detail.type);
      });
    },

    /**
     * Show a toast notification
     * @param {string} message - The message to display
     * @param {string} type - The type: 'success', 'error', 'info', 'warning'
     * @param {number} duration - How long to show the toast (ms)
     */
    show(message, type = 'info', duration = 4000) {
      const id = ++this.counter;

      const toast = {
        id,
        message,
        type,
        visible: false,
      };

      this.toasts.push(toast);

      // Trigger enter animation
      setTimeout(() => {
        const index = this.toasts.findIndex(t => t.id === id);
        if (index !== -1) {
          this.toasts[index].visible = true;
        }
      }, 10);

      // Auto-dismiss after duration
      setTimeout(() => {
        this.dismiss(id);
      }, duration);
    },

    /**
     * Dismiss a toast by ID
     */
    dismiss(id) {
      const index = this.toasts.findIndex(t => t.id === id);
      if (index !== -1) {
        this.toasts[index].visible = false;

        // Remove from DOM after animation
        setTimeout(() => {
          this.toasts = this.toasts.filter(t => t.id !== id);
        }, 300);
      }
    },

    /**
     * Get icon SVG based on type
     */
    getIcon(type) {
      switch (type) {
        case 'success':
          return `<svg class="h-5 w-5 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>`;
        case 'error':
          return `<svg class="h-5 w-5 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>`;
        case 'warning':
          return `<svg class="h-5 w-5 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
          </svg>`;
        case 'info':
        default:
          return `<svg class="h-5 w-5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>`;
      }
    },

    /**
     * Get background classes based on type
     */
    getBgClass(type) {
      switch (type) {
        case 'success':
          return 'bg-green-50 border-green-200';
        case 'error':
          return 'bg-red-50 border-red-200';
        case 'warning':
          return 'bg-amber-50 border-amber-200';
        case 'info':
        default:
          return 'bg-blue-50 border-blue-200';
      }
    },

    /**
     * Get text color classes based on type
     */
    getTextClass(type) {
      switch (type) {
        case 'success':
          return 'text-green-800';
        case 'error':
          return 'text-red-800';
        case 'warning':
          return 'text-amber-800';
        case 'info':
        default:
          return 'text-blue-800';
      }
    },
  };
}
