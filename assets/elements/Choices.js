import ChoicesJS from 'choices.js'

/**
 * @property {Choices} choices
 */
export class Choices extends HTMLInputElement {
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
