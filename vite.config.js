// vite.config.js
import { defineConfig, loadEnv } from 'vite'
import laravel from 'laravel-vite-plugin'

export default defineConfig(({ mode }) => {
  const env = loadEnv(mode, process.cwd(), '')
  // Ensure trailing slash if ASSET_URL is set
  const base = env.ASSET_URL ? `${env.ASSET_URL.replace(/\/$/, '')}/` : '/'

  return {
    base,
    plugins: [
      laravel({
        input: ['resources/css/app.css', 'resources/js/app.js'],
        refresh: true,
      }),
    ],
  }
})
