import './css/app.scss'

import './elements/index.js'
import './pages/index.js'

import Turbolinks from 'turbolinks'

import './modules/scrollreveal.js'
import './modules/highlight.js'
import { showHistory } from './modules/history.js'
import ChoicesJS from 'choices.js'
import { $$, $ } from '/functions/dom.js'
import { registerKonami, registerBadgeAlert } from '/modules/badges.js'

registerKonami()
registerBadgeAlert()

document.addEventListener('turbolinks:load', () => {
  const darkToggle = document.querySelector('#dark-toggle')
  if (darkToggle) {
    darkToggle.addEventListener('click', e => {
      e.stopPropagation()
      e.preventDefault()
      document.body.classList.toggle('dark')
    })
  }

  // Header toggle
  const burgerButton = $('#js-burger')
  if (burgerButton) {
    burgerButton.addEventListener('click', () => {
      $('#header').classList.toggle('is-open')
    })
  }

  // Choices
  $$('select[multiple]').forEach(s => new ChoicesJS(s))
})

window.Grafikart = {
  showHistory
}

Turbolinks.start()
