import Choices from 'choices.js'
import 'choices.js/public/assets/styles/choices.css'
import { debounce } from '../../functions/timers'

/**
 * @param {string} endpoint
 * @param {string} search
 */
function getItems (endpoint, search) {
  return async function () {
    const response = await fetch(endpoint + '/' + search)
    if (response.status >= 200 && response.status < 300) {
      return await response.json()
    }
    return [];
  }
}


/**
 * @property {number|null} timer
 * @property {choices} Choices
 * @property {string} endpoint
 */
class UserSelect extends HTMLSelectElement {

  connectedCallback () {
    if (this.timer) {
      global.clearTimeout(this.timer)
      this.timer = null
      return
    }
    this.endpoint = this.getAttribute('endpoint')
    if (this.endpoint === null) {
      console.error("Impossible de monter l'élément user-select, endpoint n'est pas définit")
      return
    }
    const onSearch = debounce(this.onSearch.bind(this), 1000)
    this.choices = new Choices(this)
    this.addEventListener('search', onSearch)
  }

  disconnectedCallback () {
    this.timer = window.setTimeout(function () {
      if (this.choices) {
        this.choices.destroy()
      }
    }, 500)
  }

  onSearch (e) {
    const search = e.detail.value
    if (search.length > 2) {
      this.choices.setChoices(getItems(this.endpoint, search), 'id', 'username', true)
    }
  }

}


global.customElements.define('user-select', UserSelect, {extends: 'select'})
