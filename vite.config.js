/**
 * Vite Configuration for Sage WooCommerce Theme
 *
 * Development: npm run dev (uses .env)
 * Production:  npm run build (uses .env.production)
 *
 * Production builds output to public/build/ with manifest.json
 * for Acorn asset loading on Hostinger.
 */
import { defineConfig, loadEnv } from 'vite'
import tailwindcss from '@tailwindcss/vite'
import laravel from 'laravel-vite-plugin'
import { wordpressPlugin, wordpressThemeJson } from '@roots/vite-plugin'

export default defineConfig(({ mode }) => {
  // Load environment variables
  const env = loadEnv(mode, process.cwd(), '')

  // Configuration
  const isProduction = mode === 'production'
  const wpUrl = env.APP_URL || 'http://localhost:10028'
  const vitePort = parseInt(env.VITE_PORT || '5173')

  // Theme folder name (configurable for deployment flexibility)
  const themeFolderName = env.THEME_FOLDER || 'sega-woo-theme-master'

  return {
    // Base path for production builds (uses theme folder from env)
    base: isProduction
      ? `/wp-content/themes/${themeFolderName}/public/build/`
      : '/',

    plugins: [
      tailwindcss(),

      laravel({
        input: [
          'resources/css/app.css',
          'resources/js/app.js',
          'resources/css/editor.css',
          'resources/js/editor.js',
        ],
        refresh: [
          'resources/views/**/*.blade.php',
          'app/**/*.php',
        ],
      }),

      wordpressPlugin(),

      // Generate theme.json from Tailwind config
      wordpressThemeJson({
        disableTailwindColors: false,
        disableTailwindFonts: false,
        disableTailwindFontSizes: false,
      }),
    ],

    // Dev server configuration for LocalWP
    server: {
      host: '0.0.0.0',
      port: vitePort,
      strictPort: true,
      cors: true,

      // HMR configuration for LocalWP
      hmr: {
        host: 'localhost',
        port: vitePort,
        protocol: 'ws',
      },

      // Watch PHP/Blade files for full page reload
      watch: {
        usePolling: true,
        interval: 100,
        ignored: ['**/node_modules/**', '**/vendor/**'],
      },

      // Origin for asset URLs during development
      origin: `http://localhost:${vitePort}`,
    },

    // Build configuration for production
    // Assets are output to public/build/ with manifest.json for Acorn integration
    build: {
      outDir: 'public/build',
      emptyOutDir: true,
      manifest: true,

      // Sourcemaps: only in development (disabled for Hostinger)
      sourcemap: !isProduction,

      // Target modern browsers for smaller bundles
      target: 'es2020',

      // Minification settings
      minify: isProduction ? 'esbuild' : false,

      // CSS code splitting for better caching
      cssCodeSplit: true,

      // Inline small assets (< 4kb) as base64 to reduce HTTP requests
      assetsInlineLimit: 4096,

      // Asset size warnings (in KB)
      chunkSizeWarningLimit: 500,

      // Rollup options for advanced bundling
      rollupOptions: {
        input: {
          app: 'resources/js/app.js',
          'app-css': 'resources/css/app.css',
          editor: 'resources/js/editor.js',
          'editor-css': 'resources/css/editor.css',
        },
        output: {
          // Entry file naming with hash for cache busting
          entryFileNames: 'assets/[name]-[hash].js',

          // Chunk file naming
          chunkFileNames: 'assets/[name]-[hash].js',

          // Asset file naming (CSS, images, fonts)
          assetFileNames: (assetInfo) => {
            // Organize assets by type
            const extType = assetInfo.name?.split('.').pop() || 'misc'

            if (/png|jpe?g|svg|gif|tiff|bmp|ico|webp/i.test(extType)) {
              return 'assets/images/[name]-[hash][extname]'
            }
            if (/woff2?|eot|ttf|otf/i.test(extType)) {
              return 'assets/fonts/[name]-[hash][extname]'
            }
            return 'assets/[name]-[hash][extname]'
          },

          // Manual chunk splitting for better caching
          manualChunks: (id) => {
            // Vendor chunk for node_modules
            if (id.includes('node_modules')) {
              // Alpine.js in its own chunk (frequently used)
              if (id.includes('alpinejs') || id.includes('@alpinejs')) {
                return 'vendor-alpine'
              }
              // Swiper in its own chunk (only loaded on homepage)
              if (id.includes('swiper')) {
                return 'vendor-swiper'
              }
              // Other vendors
              return 'vendor'
            }
          },
        },

        // Tree-shaking configuration
        treeshake: {
          moduleSideEffects: 'no-external',
          propertyReadSideEffects: false,
        },
      },

      // Report compressed size in build output
      reportCompressedSize: true,
    },

    // esbuild options for minification
    esbuild: {
      // Drop console and debugger in production
      drop: isProduction ? ['console', 'debugger'] : [],

      // Legal comments handling
      legalComments: 'none',

      // Target for transformation
      target: 'es2020',
    },

    // CSS processing options
    css: {
      devSourcemap: !isProduction,
    },

    // Path aliases
    resolve: {
      alias: {
        '@scripts': '/resources/js',
        '@styles': '/resources/css',
        '@fonts': '/resources/fonts',
        '@images': '/resources/images',
      },
    },

    // Optimize dependencies
    optimizeDeps: {
      include: ['alpinejs', '@alpinejs/collapse', 'swiper'],
      exclude: [],
    },

    // Preview server (for testing production builds locally)
    preview: {
      port: 4173,
      strictPort: true,
    },
  }
})
