import './css/app.scss'

import './elements/index.js'
import './pages/index.js'

import Turbolinks from 'turbolinks'

import './modules/scrollreveal.js'
import './modules/highlight.js'
import { showHistory } from './modules/history.js'
import ChoicesJS from 'choices.js'
import { $$ } from '/functions/dom.js'

document.addEventListener('turbolinks:load', () => {
  const darkToggle = document.querySelector('#dark-toggle')
  if (darkToggle) {
    darkToggle.addEventListener('click', e => {
      e.stopPropagation()
      e.preventDefault()
      document.body.classList.toggle('dark')
    })
  }

  // Choices
  $$('select[multiple]').forEach(s => new ChoicesJS(s))
})

window.Grafikart = {
  showHistory
}

Turbolinks.start()
