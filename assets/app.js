import './css/app.scss'

import './elements/index.js'
import './pages/index.js'

import Turbolinks from 'turbolinks'

import './modules/scrollreveal.js'
import './modules/highlight.js'
import { showHistory } from './modules/history.js'
import ChoicesJS from 'choices.js'
import { $$ } from '/functions/dom.js'
import DarkMode from './elements/DarkMode'

document.addEventListener('turbolinks:load', () => {
  new DarkMode()

  // Choices
  $$('select[multiple]').forEach(s => new ChoicesJS(s))
})

window.Grafikart = {
  showHistory
}

Turbolinks.start()
