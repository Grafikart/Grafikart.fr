/**
 * @property {HTMLSpanElement} switch
 */
export default class Switch extends HTMLInputElement {

  connectedCallback () {
    if (this.nextElementSibling === null  || this.nextElementSibling.tagName !== 'LABEL') {
      console.error('Impossible de greffer le switch')
      return
    }
    this.parentElement.classList.add('form-switch')
    this.parentElement.classList.remove('form-check')
    this.switch = document.createElement('span')
    this.switch.classList.add('switch')
    this.nextElementSibling.prepend(this.switch)

  }

  disconnectedCallback () {
    if (this.parentElement) {
      this.parentElement.classList.remove('form-switch')
    }
    this.switch.parentElement.remove(this.switch)
  }

}

global.customElements.define('input-switch', Switch, {extends: 'input'})
