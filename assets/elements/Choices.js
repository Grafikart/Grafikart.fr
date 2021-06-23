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
    const plugins = {}
    if (this.tagName === 'SELECT') {
      plugins.no_backspace_delete = {}
      plugins.dropdown_input = {}
      if (this.getAttribute('multiple')) {
        plugins.remove_button = {
          title: 'Supprimer cet élément'
        }
      }
    } else {
      plugins.remove_button = {
        title: 'Supprimer cet élément'
      }
    }

    // On configure les options en fonction de la situation
    let options = {
      allowEmptyOption: true,
      plugins,
      hideSelected: true,
      persist: false
    }
    if (this.dataset.remote) {
      options = {
        ...options,
        valueField: this.dataset.value,
        labelField: this.dataset.label,
        searchField: this.dataset.label,
        load: async (query, callback) => {
          const url = `${this.dataset.remote}?q=${encodeURIComponent(query)}`
          const data = await jsonFetch(url)
          callback(data)
        }
      }
    }
    if (this.dataset.create) {
      options = {
        ...options,
        create: true
      }
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
