// @ts-check
const prefresh = require('@prefresh/vite')
const cors = require('@koa/cors')
const path = require('path')
const sass = require('sass')
const fs = require('fs')

const root = './assets'
const scss = path.resolve(root, 'css/app.scss')

function renderCSS () {
  sass.render({
    file: scss,
  }, function(err, result) {
    if (err) {
      console.error(err)
    } else {
      fs.writeFile(scss.replace('.scss', '.css'), result.css, function(err){
        if (err) {
          console.error(err)
        }
      })
      scss.replace('.scss', '.css')
    }
  });
}

/**
 * @type { import('vite').UserConfig }
 */
const config = {
  jsx: 'preact',
  plugins: [prefresh()],
  root,
  configureServer: function ({ root, app, watcher }) {
    renderCSS()
    watcher.add(path.resolve(root, '../templates/**/*.twig'))
    watcher.on('change', function (path) {
      if (path.endsWith('.twig')) {
        watcher.send({
          type: 'full-reload',
          path
        })
      } else if (path.endsWith('.scss')) {
        renderCSS()
      }
    })
    app.use(cors({ origin: '*' }))
  }
}

module.exports = config
