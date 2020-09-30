import resolve from '@rollup/plugin-node-resolve'
import postcss from 'rollup-plugin-postcss'
import rootImport from 'rollup-plugin-root-import'
import babel from '@rollup/plugin-babel'
import commonjs from '@rollup/plugin-commonjs'
import visualizer from 'rollup-plugin-visualizer';
import preact from "rollup-plugin-preact";

const sources = ['app', 'admin']

export default sources.map(source => ({
  input: `assets/${source}.js`,
  output: {
    dir: 'public/assets',
    format: 'es'
  },
  preserveEntrySignatures: false,
  plugins: [
    rootImport({
      root: `assets`
    }),
    preact({
      usePreactX: true,
      noPropTypes: false,
      noReactIs: true,
      noEnv: true,
      resolvePreactCompat: true
    }),
    resolve(),
    commonjs(),
    postcss({
      extract: true
    }),
    babel({
      babelHelpers: 'bundled',
      exclude: [
        'node_modules/**'
      ]
    }),
    visualizer({
      filename: `public/assets/${source}.html`
    })
  ]
}))
