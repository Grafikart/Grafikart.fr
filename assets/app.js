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
import { registerWindowHeightCSS } from '/modules/window.js'
import { disableBodyScroll, enableBodyScroll, clearAllBodyScrollLocks } from 'body-scroll-lock';


registerKonami()
registerBadgeAlert()
registerWindowHeightCSS()

document.addEventListener('turbolinks:load', () => {
  clearAllBodyScrollLocks()
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
  const headerNav = $('.header-nav')
  if (burgerButton) {
    let open = false;
    burgerButton.addEventListener('click', () => {
      $('#header').classList.toggle('is-open')

      open ? enableBodyScroll(headerNav) : disableBodyScroll(headerNav)
      open = !open
    })
  }

  // Choices
  $$('select[multiple]').forEach(
    s =>
      new ChoicesJS(s, {
        placeholder: true,
        shouldSort: false,
        itemSelectText: '',
        maxItemCount: s.dataset.limit || -1,
        maxItemText: s.dataset.limit && `Vous ne pouvez sélectionner que ${s.dataset.limit} éléments`
      })
  )
})

/**
 * Evite le chargement ajax lors de l'utilisation d'une ancre
 *
 * cf : https://github.com/turbolinks/turbolinks/issues/75
 */
document.addEventListener('turbolinks:click', e => {
  const anchorElement = e.target
  const isSamePageAnchor =
    anchorElement.hash &&
    anchorElement.origin === window.location.origin &&
    anchorElement.pathname === window.location.pathname

  if (isSamePageAnchor) {
    Turbolinks.controller.pushHistoryWithLocationAndRestorationIdentifier(e.data.url, Turbolinks.uuid())
    e.preventDefault()
    window.dispatchEvent(new Event('hashchange'))
  }
})

window.Grafikart = {
  showHistory
}
