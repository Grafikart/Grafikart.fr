import './css/app.scss'
import './elements/index.js'
import './pages/index.js'

import Turbolinks from 'turbolinks'

import './modules/scrollreveal.js'
import './modules/highlight.js'
import { showHistory } from './modules/history.js'
import { $$, $ } from '/functions/dom.js'
import { registerKonami, registerBadgeAlert } from '/modules/badges.js'
import { registerWindowHeightCSS } from '/modules/window.js'
import { registerHeader } from '/modules/header.js'
import { registerMermaid } from '/modules/mermaid.js'
import { disableBodyScroll, enableBodyScroll, clearAllBodyScrollLocks } from 'body-scroll-lock'
import { slideDown } from '/functions/animation.js'
import TomSelect from 'tom-select'

registerKonami()
registerMermaid()
registerBadgeAlert()
registerHeader()
registerWindowHeightCSS()
let isFirstRender = true

document.addEventListener('turbolinks:load', () => {
  if (!isFirstRender && window.plausible) {
    window.plausible('pageview')
  }
  isFirstRender = false;
})

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

  // ScrollIntoView elements
  const scrollIntoViewElement = document.querySelector('.js-scrollIntoView')
  if (scrollIntoViewElement) {
    const parent = scrollIntoViewElement.offsetParent
    parent.scrollTop = scrollIntoViewElement.offsetTop - scrollIntoViewElement.offsetHeight
  }

  // Header toggle
  const burgerButton = $('#js-burger')
  const headerNav = $('.header-nav')
  if (burgerButton) {
    let open = false
    burgerButton.addEventListener('click', () => {
      $('#header').classList.toggle('is-open')

      open ? enableBodyScroll(headerNav) : disableBodyScroll(headerNav)
      open = !open
    })
  }

  // Choices
  $$('select[multiple]:not([is])').forEach(
    s =>
      new TomSelect(s, {
        plugins: {
          remove_button: {
            title: 'Supprimer cet élément'
          }
        },
        maxItems: s.dataset.limit || null
      })
  )

  $$('form.js-preventMultiSubmit').forEach(form => {
    form.addEventListener('submit', () => {
      const button = form.querySelector('button[type="submit"]')
      if (button) {
        button.disabled = true
        button.innerText = 'Chargement...'
      }
    })
  })

  const podcastButton = $('#podcast-new')
  if (podcastButton) {
    podcastButton.addEventListener('click', async e => {
      e.preventDefault()
      podcastButton.remove()
      const form = $('#podcast-form')
      await slideDown(form, 200)
      form.querySelector('input').focus()
    })
  }
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

Turbolinks.start()
