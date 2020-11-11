// @ts-check
const prefresh = require("@prefresh/vite");
const path = require("path");

const root = "./assets";

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
  jsx: "preact",
  plugins: [prefresh()],
  root,
  configureServer: function ({ root, watcher }) {
    watcher.add(path.resolve(root, "../templates/**/*.twig"));
    watcher.on("change", function (path) {
      if (path.endsWith(".twig")) {
        watcher.send({
          type: "full-reload",
          path,
        });
      }
    });
  },
};

module.exports = config;
