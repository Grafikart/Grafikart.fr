import {scrollTo} from '@fn/scroll'

/**
 * Custom element pour créer un système d'onglet accessible
 *
 * Référence : https://www.w3.org/TR/wai-aria-practices/examples/tabs/tabs-1/tabs.html
 */
export default class Tabs extends HTMLElement {

  connectedCallback () {
    // On ajoute les attributs aria
    this.setAttribute('role', 'tablist')
    let hashTab = false
    let hashTarget = false

    Array.from(this.children).forEach((_, i) => {
      const tab = this.children[i]
      const control = tab.getAttribute('aria-controls')
      const target = document.querySelector('#' + control)
      if (control === window.location.hash.replace('#', '')) {
        hashTarget = target
        hashTab = tab
      }

      // On ajoute les attributs aria sur l'onglet
      tab.setAttribute('role', 'tab')
      tab.setAttribute('tabindex', '-1')
      tab.setAttribute('aria-selected', 'false')
      tab.setAttribute('aria-controls', control)
      tab.setAttribute('id', 'tab-' + control)
      tab.dataset.control = target

      // On ajoute les attributs aria sur la cible
      target.setAttribute('role', 'tabpanel')
      target.setAttribute('aria-labelledby', 'tab-' + control)
      target.setAttribute('hidden', 'hidden')

      // Navigation à la souris
      tab.addEventListener('click', (e) => {
        e.preventDefault()
        this.activate(tab)
      })
      // Navigation au clavier
      tab.addEventListener('keyup', e => {
        if (e.key === 'ArrowRight') {
          const tab = this.children[i === this.children.length - 1 ? 0 : i + 1]
          this.activate(tab)
          tab.focus()
        }
        if (e.key === 'ArrowLeft') {
          const tab = this.children[i === 0 ? this.children.length - 1 : i - 1]
          this.activate(tab)
          tab.focus()
        }
        if (e.key === 'Home') {
          const tab = this.children[0]
          this.activate(tab)
          tab.focus()
        }
        if (e.key === 'End') {
          const tab = this.children[this.children.length - 1]
          this.activate(tab)
          tab.focus()
        }
      })
    })

    // On active le premier onglet
    if (hashTab) {
      this.activate(hashTab)
      scrollTo(hashTarget)
    } else {
      this.activate(this.children[0], false)
    }
  }

  /**
   * @param {HTMLElement} tab
   */
  activate (tab, changeHash = true) {
    // Si un onglet est actif on le désactive
    const currentTab = this.querySelector('[aria-selected="true"]')
    if (currentTab !== null) {
      const target = document.querySelector('#' + currentTab.getAttribute('aria-controls'))
      target.setAttribute('hidden', 'hidden')
      currentTab.setAttribute('aria-selected', 'false')
      currentTab.setAttribute('tabindex', '-1')
    }

    // On active l'onglet ciblé
    const target = document.querySelector('#' + tab.getAttribute('aria-controls'))
    target.removeAttribute('hidden')
    tab.setAttribute('aria-selected', 'true')
    tab.setAttribute('tabindex', '0')

    // On met à jour l'url
    if (changeHash) {
      window.history.replaceState({}, '', '#' + tab.getAttribute('aria-controls'))
    }
  }

}

customElements.define('nav-tabs', Tabs)
