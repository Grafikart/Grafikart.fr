/**
 * @property {string} type
 */
import { slideUp } from '../modules/animation'

export default class Alert extends HTMLElement {

  constructor () {
    super()
    this.type = this.getAttribute('type')
    this.innerHTML = `<div class="alert alert-${this.type}">
        <svg class="icon icon-{$name}">
          <use xlink:href="/sprite.svg#${this.icon}"></use>
        </svg>
        ${this.innerHTML}
        <button class="alert-close">
          <svg class="icon icon-{$name}">
            <use xlink:href="/sprite.svg#cross"></use>
          </svg>
        </button>
      </div>`
    this.querySelector('.alert-close').addEventListener('click', this.close.bind(this))
  }

  close () {
    const element = this.querySelector('.alert')
    element.classList.add('out')
    window.setTimeout(async() => {
      await slideUp(element)
      this.parentElement.removeChild(this)
    }, 500)
  }

  get icon () {
    if (this.type === 'danger') {
      return 'warning'
    }
  }

}
