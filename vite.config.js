// @ts-check
const prefresh = require('@prefresh/vite')
const cors = require('@koa/cors')

/**
 * @type { import('vite').UserConfig }
 */
const config = {
  jsx: 'preact',
  plugins: [prefresh()],
  root: './assets',
  configureServer: function ({ app }) {
    app.use(cors({ origin: '*' }))
  }
}

module.exports = config
