import {defineConfig} from "vite";
import symfonyPlugin from "vite-plugin-symfony";
import react from '@vitejs/plugin-react'

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
    symfonyPlugin(),
  ],
  build: {
    rolldownOptions: {
      input: {
        app: "./assets/app.js"
      },
    }
  },
});
