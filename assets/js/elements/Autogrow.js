/**
 * @property {HTMLDivElement} _dummy
 */
import { debounce } from '../functions/timers'

export default class Autogrow extends HTMLTextAreaElement {

  /**
   * @return {HTMLDivElement}
   */
  get dummy () {
    if (this._dummy === undefined) {
      this._dummy = document.createElement('div')
      const style = window.getComputedStyle(this)
      this._dummy.style.fontSize = style.fontSize
      this._dummy.style.fontFamily = style.fontFamily
      this._dummy.style.lineHeight = style.lineHeight
      this._dummy.style.overflowX = style.hidden
      this._dummy.style.width = style.width
      this._dummy.style.padding = style.padding
      this._dummy.style.whiteSpace = 'pre-wrap'
      this._dummy.style.position = 'absolute'
      this._dummy.style.top = 0
      this.insertAdjacentElement('afterend', this._dummy)
      this._dummy.style.visibility = 'hidden'
    }
    return this._dummy
  }

  /**
   * Redimensionne le textarea en fonction du texte
   */
  autogrow () {
    this.dummy.textContent = this.value
    const dummyHeight = window.getComputedStyle(this.dummy).height
    if (this.style.height !== dummyHeight) {
      this.style.height = dummyHeight
    }
  }

  onFocus () {
    this.autogrow()
    window.addEventListener('resize', this.onResize)
    this.removeEventListener('focus', this.onFocus)
  }

  onResize () {
    if (this._dummy) {
      const style = window.getComputedStyle(this)
      this._dummy.style.width = style.width
      this.autogrow()
    }
  }

  connectedCallback () {
    this.addEventListener('keyup', this.autogrow)
    this.addEventListener('focus', this.onFocus)
    this.style.overflowY = 'hidden'
    this.style.resize = 'none'
  }

  disconnectedCallback () {
    if (this._dummy) {
      this._dummy.parentElement.removeChild(this._dummy)
    }
    window.removeEventListener('resize', this.onResize)
  }

  constructor () {
    super()
    this.autogrow = this.autogrow.bind(this)
    this.onResize = debounce(this.onResize.bind(this), 300)
    this.onFocus = this.onFocus.bind(this)
  }

}

customElements.define('textarea-autogrow', Autogrow, { extends: 'textarea' })

