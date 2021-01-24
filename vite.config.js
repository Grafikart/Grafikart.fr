// @ts-check
import prefresh from "@prefresh/vite";
import { resolve } from 'path'

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

/**
 * @type { import('vite').UserConfig }
 */
const config = {
  alias: {
    react: "preact/compat",
    "react-dom": "preact/compat",
  },
  emitManifest: true,
  cors: true,
  optimizeDeps: {
    include: ['preact/hooks', 'preact/compat', 'htm/mini']
  },
  esbuild: {
    jsxFactory: 'h',
    jsxFragment: 'Fragment',
    jsxInject: `import { h, Fragment } from 'preact'`
  },
  base: '/assets/',
  build: {
    polyfillDynamicImport: false,
    assetsDir: '',
    manifest: true,
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
  },
  plugins: [prefresh(), twigRefreshPlugin()],
  root
};

module.exports = config;
