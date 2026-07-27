import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'

export default defineConfig({
  plugins: [vue()],
  base: '/doc-static/',
  build: {
    outDir: '../../../public/doc-static',
    emptyOutDir: true,
  },
  server: {
    proxy: {
      '/doc': {
        target: 'http://localhost:2778',
        changeOrigin: true,
      },
    },
  },
})
