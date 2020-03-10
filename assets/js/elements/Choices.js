import Choices from 'choices.js'

/**
 * @property {Choices} choices
 */
class ChoicesElement extends HTMLInputElement {

  connectedCallback () {
    if (!this.getAttribute('choicesBinded')) {
      this.setAttribute('choicesBinded', 'true')
      this.choices = new Choices(this, {
        removeItems: true,
        removeItemButton: true,
        addItemText: (value) => {
          return `Appuyer sur entrer pour ajouter <b>"${value}"</b>`;
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

customElements.define('input-choices', ChoicesElement, {extends: 'input'})
