import '../css/app.scss'

import './elements'
import './pages'

import Turbolinks from 'turbolinks'

import './modules/scrollreveal'
import './modules/highlight'
import {showHistory} from './modules/history'
import Choices from 'choices.js'
import {$$} from '@fn/dom'

document.addEventListener('turbolinks:load', function () {
  const darkToggle = document.querySelector('#dark-toggle')
  if (darkToggle) {
    darkToggle.addEventListener('click', e => {
      e.stopPropagation()
      e.preventDefault()
      document.body.classList.toggle('dark')
    })
  }

  // Choices
  $$('select[multiple]').forEach((s) => new Choices(s))
})

global.Grafikart = {
  showHistory
}

Turbolinks.start()
