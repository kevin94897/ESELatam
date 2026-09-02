import { defineConfig, type Plugin } from 'vite';
import tailwindcss from '@tailwindcss/vite';
import { resolve } from 'node:path';

/**
 * Los templates PHP no forman parte del grafo de módulos de Vite, así que
 * editarlos no dispara ninguna recarga (y el markup nuevo — p. ej. una clase
 * Tailwind recién agregada — no se ve hasta recargar a mano). Este plugin
 * los observa y fuerza un full-reload del navegador; el pequeño debounce da
 * margen a que Tailwind re-escanee las clases antes de que el navegador
 * vuelva a pedir el CSS.
 */
function phpFullReload(): Plugin {
  let timer: ReturnType<typeof setTimeout> | undefined;
  return {
    name: 'php-full-reload',
    configureServer(server) {
      server.watcher.add(resolve(__dirname, '**/*.php'));
      server.watcher.on('change', (file) => {
        if (!file.endsWith('.php')) return;
        clearTimeout(timer);
        timer = setTimeout(() => {
          server.ws.send({ type: 'full-reload', path: '*' });
        }, 150);
      });
    },
  };
}

/**
 * Vite genera un manifest.json en dist/.vite/manifest.json que PHP lee
 * para encolar los assets con hash. En dev, WP apunta al server de Vite.
 */
export default defineConfig({
  // Base relativa: el theme vive en /wp-content/themes/ese-latam/dist/,
  // así los preloads de chunks dinámicos se resuelven respecto al módulo.
  base: '',
  plugins: [tailwindcss(), phpFullReload()],
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
