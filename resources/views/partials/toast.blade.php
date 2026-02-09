{{-- Toast Notifications Container --}}
<div
    x-data="toast()"
    class="pointer-events-none fixed inset-0 z-[100] flex flex-col items-end justify-start gap-3 p-4 sm:p-6"
    aria-live="polite"
    aria-atomic="true"
>
    <template x-for="t in toasts" :key="t.id">
        <div
            x-show="t.visible"
            x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="translate-x-full opacity-0"
            x-transition:enter-end="translate-x-0 opacity-100"
            x-transition:leave="transition ease-in duration-200 transform"
            x-transition:leave-start="translate-x-0 opacity-100"
            x-transition:leave-end="translate-x-full opacity-0"
            class="pointer-events-auto w-full max-w-sm overflow-hidden rounded-lg border shadow-lg"
            :class="getBgClass(t.type)"
            role="alert"
        >
            <div class="p-4">
                <div class="flex items-start">
                    {{-- Icon --}}
                    <div class="flex-shrink-0" x-html="getIcon(t.type)"></div>

                    {{-- Message --}}
                    <div class="ml-3 w-0 flex-1 pt-0.5">
                        <p class="text-sm font-medium" :class="getTextClass(t.type)" x-text="t.message"></p>
                    </div>

                    {{-- Close Button --}}
                    <div class="ml-4 flex flex-shrink-0">
                        <button
                            type="button"
                            @click="dismiss(t.id)"
                            class="inline-flex rounded-md text-secondary-400 hover:text-secondary-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
                        >
                            <span class="sr-only">{{ __('Close', 'sega-woo-theme') }}</span>
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>
