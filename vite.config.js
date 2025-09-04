import { defineConfig, loadEnv } from 'vite'
import laravel from 'laravel-vite-plugin'

export default defineConfig(({ mode }) => {
  const env = loadEnv(mode, process.cwd(), '')
  const rawBase = env.ASSET_URL ? new URL(env.ASSET_URL).pathname : '/'
  const base = rawBase.endsWith('/') ? rawBase : rawBase + '/'

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
