import { debounce } from '../functions/timers'

export default class Autogrow extends HTMLTextAreaElement {

  autogrow () {
    this.style.height = 'auto'
    this.style.height = this.scrollHeight + 'px'
  }

  onFocus () {
    this.autogrow()
    window.addEventListener('resize', this.onResize)
    this.removeEventListener('focus', this.onFocus)
  }

  onResize () {
    this.autogrow()
  }

  connectedCallback () {
    this.style.overflow = 'hidden'
    this.style.resize = 'none'
    this.addEventListener('input', this.autogrow)
    this.addEventListener('focus', this.onFocus)
  }

  disconnectedCallback () {
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

