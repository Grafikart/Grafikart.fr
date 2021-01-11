import ChoicesJS from 'choices.js'
import { redirect } from '/functions/url.js'
import { jsonFetch } from '/functions/api.js'
import { debounce } from '/functions/timers.js'

/**
 * @property {Choices} choices
 */
export class InputChoices extends HTMLInputElement {
  connectedCallback () {
    if (!this.getAttribute('choicesBinded')) {
      this.setAttribute('choicesBinded', 'true')
      this.choices = new ChoicesJS(this, {
        removeItems: true,
        removeItemButton: true,
        addItemText: value => {
          return `Appuyer sur entrer pour ajouter <b>"${value}"</b>`
        }
      })
    }
  }

  disconnectedCallback () {
    if (this.choices) {
      this.choices.destroy()
    }
  }
}

/**
 * @property {Choices} choices
 */
export class SelectChoices extends HTMLSelectElement {
  connectedCallback () {
    if (!this.getAttribute('choicesBinded')) {
      this.setAttribute('choicesBinded', 'true')
      this.choices = new ChoicesJS(this, {
        placeholder: true,
        shouldSort: false,
        itemSelectText: '',
        searchEnabled: this.dataset.search !== undefined
      })

      // On redirige l'utilisateur au changement de valeur
      if (this.dataset.redirect !== undefined) {
        this.addEventListener('change', e => {
          const params = new URLSearchParams(window.location.search)
          if (e.target.value === '') {
            params.delete(e.target.name)
          } else {
            params.set(e.target.name, e.target.value)
          }
          if (params.has('page')) {
            params.delete('page')
          }
          redirect(`${location.pathname}?${params}`)
        })
      }

      // La recherche utilise une API
      if (this.dataset.search) {
        this.addEventListener(
          'search',
          debounce(async e => {
            const data = await jsonFetch(`${this.dataset.search}?q=${encodeURIComponent(e.detail.value)}`)
            this.choices.setChoices(data, this.dataset.value || 'value', this.dataset.label || 'label', true)
          }, 400)
        )
      }
    }
  }

  disconnectedCallback () {
    if (this.choices) {
      this.choices.destroy()
    }
  }
}
