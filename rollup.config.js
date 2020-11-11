import resolve from "@rollup/plugin-node-resolve";
import postcss from "rollup-plugin-postcss";
import rootImport from "rollup-plugin-root-import";
import babel from "@rollup/plugin-babel";
import commonjs from "@rollup/plugin-commonjs";
import visualizer from "rollup-plugin-visualizer";
import preact from "rollup-plugin-preact";
import fs from "fs";
import path from "path";

const sources = ["app", "admin"];

let files = {}; // Persiste la liste des fichiers compilés

export default sources.map((source) => ({
  input: `assets/${source}.js`,
  output: {
    dir: "public/assets",
    format: "es",
    entryFileNames: "[name].[hash].js",
    chunkFileNames: "[name].[hash].js",
  },
  preserveEntrySignatures: false,
  plugins: [
    rootImport({
      root: `assets`,
    }),
    preact({
      usePreactX: true,
      noPropTypes: false,
      noReactIs: true,
      noEnv: true,
      resolvePreactCompat: true,
    }),
    resolve(),
    commonjs(),
    postcss({
      extract: true,
    }),
    babel({
      babelHelpers: "bundled",
      exclude: ["node_modules/**"],
    }),
    visualizer({
      filename: `public/assets/${source}.html`,
    }),
    // Génère un fichier manifest.json contenant les noms des assets compilés
    {
      name: "manifest",
      writeBundle(options, bundle) {
        // On extrait les noms des fichiers
        Object.values(bundle).forEach((file) => {
          const name = file.fileName.replace(/(?=\.).*/, "") + path.extname(file.fileName);
          files = { ...files, [name]: file.fileName };
        });
        // On écrit le fichier manifest.json
        fs.writeFileSync(path.resolve(__dirname, options.dir, "manifest.json"), JSON.stringify(files), "utf8");
      },
    },
  ],
}));
