import { defineConfig } from 'vite';
import tailwindcss from '@tailwindcss/vite';
import { resolve } from 'node:path';

/**
 * Vite genera un manifest.json en dist/.vite/manifest.json que PHP lee
 * para encolar los assets con hash. En dev, WP apunta al server de Vite.
 */
export default defineConfig({
  // Base relativa: el theme vive en /wp-content/themes/ese-latam/dist/,
  // así los preloads de chunks dinámicos se resuelven respecto al módulo.
  base: '',
  plugins: [tailwindcss()],
  resolve: {
    alias: {
      '@': resolve(__dirname, 'src/ts'),
    },
  },
  build: {
    manifest: true,
    outDir: 'dist',
    emptyOutDir: true,
    rollupOptions: {
      input: {
        main: resolve(__dirname, 'src/ts/main.ts'),
        style: resolve(__dirname, 'src/css/main.css'),
      },
    },
  },
  server: {
    host: '0.0.0.0',
    port: 5173,
    strictPort: true,
    cors: true,
    origin: 'http://localhost:5173',
  },
});
