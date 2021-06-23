import { jsonFetch } from '/functions/api.js'
import TomSelect from 'tom-select'
import { redirect } from '/functions/url'

export class InputChoices extends HTMLInputElement {}

export class SelectChoices extends HTMLSelectElement {}

/**
 * Ajoute le comportement sur les select / champs
 * @param {InputChoices|SelectChoices} cls
 */
function bindBehaviour (cls) {
  cls.prototype.connectedCallback = function () {
    if (this.getAttribute('choicesBinded')) {
      return
    }
    this.setAttribute('choicesBinded', 'true')

    // Ajout de plugins suivant le type de champs mappé
    const options = {
      hideSelected: true,
      persist: false,
      plugins: {},
      closeAfterSelect: true
    }
    if (this.tagName === 'SELECT') {
      options.allowEmptyOption = true
      options.plugins.no_backspace_delete = {}
      options.plugins.dropdown_input = {}
      if (this.getAttribute('multiple')) {
        options.plugins.remove_button = {
          title: 'Supprimer cet élément'
        }
      }
    } else {
      options.plugins.remove_button = {
        title: 'Supprimer cet élément'
      }
    }

    // On configure les options en fonction de la situation
    if (this.dataset.remote) {
      options.valueField = this.dataset.value
      options.labelField = this.dataset.label
      options.searchField = this.dataset.label
      options.load = async (query, callback) => {
        const url = `${this.dataset.remote}?q=${encodeURIComponent(query)}`
        const data = await jsonFetch(url)
        callback(data)
      }
    }
    if (this.dataset.create) {
      options.create = true
    }
    this.widget = new TomSelect(this, options)

    // Si l'option "redirect" est présente, on redirige au changement de valeur
    if (this.dataset.redirect !== undefined) {
      this.widget.on('change', () => redirectOnChange(this))
    }
  }

  cls.prototype.disconnectedCallback = function () {
    if (this.widget) {
      this.widget.destroy()
    }
  }
}

function redirectOnChange (select) {
  const params = new URLSearchParams(window.location.search)
  if (select.value === '') {
    params.delete(select.name)
  } else {
    params.set(select.name, select.value)
  }
  if (params.has('page')) {
    params.delete('page')
  }
  redirect(`${location.pathname}?${params}`)
}

Array.from([InputChoices, SelectChoices]).forEach(bindBehaviour)
