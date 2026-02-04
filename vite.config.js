import { defineConfig, loadEnv } from 'vite'
import tailwindcss from '@tailwindcss/vite'
import laravel from 'laravel-vite-plugin'
import { wordpressPlugin, wordpressThemeJson } from '@roots/vite-plugin'

export default defineConfig(({ mode }) => {
  // Load environment variables
  const env = loadEnv(mode, process.cwd(), '')

  // LocalWP site URL (default to localhost:10028)
  const wpUrl = env.APP_URL || 'http://localhost:10028'
  const vitePort = parseInt(env.VITE_PORT || '5173')

  return {
    // Base path for production builds
    base: mode === 'production'
      ? '/wp-content/themes/sega-woo-theme/public/build/'
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
    build: {
      outDir: 'public/build',
      emptyOutDir: true,
      manifest: true,
      sourcemap: mode !== 'production',
      rollupOptions: {
        input: {
          app: 'resources/js/app.js',
          'app-css': 'resources/css/app.css',
          editor: 'resources/js/editor.js',
          'editor-css': 'resources/css/editor.css',
        },
        output: {
          entryFileNames: 'assets/[name]-[hash].js',
          chunkFileNames: 'assets/[name]-[hash].js',
          assetFileNames: 'assets/[name]-[hash].[ext]',
        },
      },
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
      include: [],
      exclude: [],
    },
  }
})
