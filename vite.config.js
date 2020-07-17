// @ts-check
const prefresh = require('@prefresh/vite')
const cors = require('@koa/cors')
const path = require('path')
const sass = require('sass')
const fs = require('fs')

const root = './assets'
const scss = path.resolve(root, 'css/app.scss')

/**
 * @type { import('vite').UserConfig }
 */
const config = {
  jsx: 'preact',
  plugins: [prefresh()],
  root,
  configureServer: function ({ root, app, watcher }) {
    watcher.add(path.resolve(root, '../templates/**/*.twig'))
    watcher.on('change', function (path) {
      if (path.endsWith('.twig')) {
        watcher.send({
          type: 'full-reload',
          path
        })
      }
    })
    app.use(cors({ origin: '*' }))
  }
}

module.exports = config
