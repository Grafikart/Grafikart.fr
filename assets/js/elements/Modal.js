export class Modal extends HTMLElement {

  constructor () {
    super()
    this.onEscapeKey = this.onEscapeKey.bind(this)
    this.close = this.close.bind(this)
  }

  connectedCallback () {
    this.addEventListener('click', this.close.bind(this))
    if (this.children.length > 0) {
      this.children[0].addEventListener('click', e => {
        e.stopPropagation()
      })
    }
    window.addEventListener('keyup', this.onEscapeKey)
  }

  disconnectedCallback () {
    window.removeEventListener('keyup', this.onEscapeKey)
  }

  onEscapeKey (e) {
    if (e.key === 'Escape') {
      e.preventDefault()
      this.close()
    }
  }

  close () {
    this.classList.add('is-closing')
    window.setTimeout(() => {
      this.parentElement.removeChild(this)
    }, 500)
  }

}

customElements.define('modal-box', Modal)
