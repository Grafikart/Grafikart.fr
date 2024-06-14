import preact from '@preact/preset-vite'
import { resolve } from 'node:path'
import {defineConfig} from "vite";

const root = "./assets";

/**
 * Rafraichi la page quand on modifie un fichier twig
 */
const twigRefreshPlugin = () => ({
  name: 'twig-refresh',
  configureServer({ watcher, ws }) {
    watcher.add(resolve(__dirname, "templates/**/*.twig"));
    watcher.on("change", function (path) {
      if (path.endsWith(".twig")) {
        ws.send({
          type: 'full-reload',
        })
      }
    });
  }
})

export default defineConfig({
  resolve: {
    alias: {
      react: "preact/compat",
      "react-dom": "preact/compat",
    }
  },
  server: {
    port: 3000,
    host: "0.0.0.0",
  },
  emitManifest: true,
  cors: true,
  base: '/assets/',
  build: {
    outDir: '../public/assets/',
    rollupOptions: {
      output: {
        manualChunks: undefined // Désactive la séparation du vendor
      },
      input: {
        app: resolve(__dirname, 'assets/app.js'),
        admin: resolve(__dirname, 'assets/admin.js')
      }
    },
    polyfillDynamicImport: false,
    assetsDir: '',
    manifest: true,
  },
  plugins: [preact(), twigRefreshPlugin()],
  root
})
