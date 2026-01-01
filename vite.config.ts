import {defineConfig, type PluginOption} from "vite";
import symfonyPlugin from "vite-plugin-symfony";
import react from '@vitejs/plugin-react'
import tailwindcss from "@tailwindcss/vite";
import {resolve} from "node:path";

/**
 * Rafraichi la page quand on modifie un fichier twig
 */
const twigRefreshPlugin = (): PluginOption => ({
  name: "twig-refresh",
  configureServer({ watcher, ws }) {
    watcher.add(resolve(__dirname, "templates/**/*.twig"));
    watcher.on("change", function (path) {
      if (path.endsWith(".twig")) {
        ws.send({
          type: "full-reload",
        });
      }
    });
  },
});

export default defineConfig({
  server: {
    port: 3000,
    host: '0.0.0.0'
  },
  plugins: [
    react({
      babel: {
        plugins: [['babel-plugin-react-compiler']],
      },
    }),
    twigRefreshPlugin(),
    tailwindcss(),
    symfonyPlugin(),
  ],
  resolve: {
    alias: {
      "@": resolve(__dirname, "./assets"),
    },
  },
  build: {
    rolldownOptions: {
      input: {
        app: "./assets/app.ts",
        admin: "./assets/admin.tsx"
      },
    }
  },
});
