import $script from 'scriptjs'

let lazylangs = ['typescript', 'elixir', 'less', 'stylus', 'scss', 'sass', 'yaml']

/**
 * Ajoute highlightjs sur les éléments sélectionnés
 *
 * @param {NodeList<HTMLElement>} $codes
 */
function highlight ($codes) {
  $codes.forEach(function (code) {
    let lazy = false
    let cls = code.getAttribute('class')
    if (cls === null) {
      cls = 'bash'
    } else {
      cls = code.getAttribute('class').replace('markup', 'bash')
    }
    lazylangs.forEach(lang => {
      if (cls.endsWith(lang)) {
        lazy = true
        $script(`//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.13.1/languages/${lang}.min.js`, () => {
          global.hljs.highlightBlock(code)
        })
      }
    })
    if (lazy === false) {
      global.hljs.highlightBlock(code)
    }
  })
}

/**
 * Détecte et ajoute la coloration syntaxique sur le site
 */
function bindHighlight () {
  let $codes = document.querySelectorAll('pre code')
  if ($codes.length > 0) {
    if (global.hljs) {
      highlight($codes)
    } else {
      let link = document.createElement('link')
      link.setAttribute('rel', 'stylesheet')
      document.querySelector('head').appendChild(link)
      $script('//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.18.1/highlight.min.js', function () {
        global.hljs.configure({tabReplace: '    '})
        highlight($codes)
      })
    }
  }
}

document.addEventListener('turbolinks:load', function () {
  bindHighlight()
})
