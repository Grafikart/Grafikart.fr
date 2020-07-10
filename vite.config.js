// @ts-check
const preactRefresh = require('@prefresh/vite')
const cors = require('@koa/cors')

/**
 * @type { import('vite').UserConfig }
 */
const config = {
  jsx: 'preact',
  plugins: [preactRefresh()],
  root: './assets',
  optimizeDeps: {
    include: [
      'codemirror/mode/markdown/markdown',
    ]
  },
  alias: {
    '/@@/': './'
  },
  configureServer: function ({ app }) {
    app.use(cors({ origin: '*' }))
  }
}

module.exports = config
