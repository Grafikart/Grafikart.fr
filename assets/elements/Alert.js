import { slideUp } from '/functions/animation.js'

export class Alert extends HTMLElement {
  constructor ({ type, message } = {}) {
    super()
    if (type !== undefined) {
      this.type = type
    }
    if (this.type === 'error' || this.type === null) {
      this.type = 'danger'
    }
    this.message = message
    this.close = this.close.bind(this)
  }

  connectedCallback () {
    this.type = this.type || this.getAttribute('type') || 'error'
    const text = this.innerHTML
    const duration = this.getAttribute('duration')
    let progressBar = ''
    if (duration !== null) {
      progressBar = `<div class="alert__progress" style="animation-duration: ${duration}s">`
      window.setTimeout(this.close, duration * 1000)
    }
    this.innerHTML = `<div class="alert alert-${this.type}">
        <svg class="icon icon-${this.icon}">
          <use xlink:href="/sprite.svg#${this.icon}"></use>
        </svg>
        <div>
          ${this.message || text}
        </div>
        <button class="alert-close">
          <svg class="icon">
            <use xlink:href="/sprite.svg#cross"></use>
          </svg>
        </button>
        ${progressBar}
      </div>`
    this.querySelector('.alert-close').addEventListener('click', e => {
      e.preventDefault()
      this.close()
    })
  }

  close () {
    const element = this.querySelector('.alert')
    element.classList.add('out')
    window.setTimeout(async () => {
      await slideUp(element)
      this.parentElement.removeChild(this)
      this.dispatchEvent(new CustomEvent('close'))
    }, 500)
  }

  get icon () {
    if (this.type === 'danger') {
      return 'warning'
    } else if (this.type === 'success') {
      return 'check'
    }
    return this.type
  }
}

export class FloatingAlert extends Alert {
  constructor (options = {}) {
    super(options)
  }

  connectedCallback () {
    super.connectedCallback()
    this.classList.add('is-floating')
  }
}

export function flash (message, type = 'success', duration = 2) {
  const alert = new FloatingAlert()
  if (duration) {
    alert.setAttribute('duration', duration)
  }
  alert.setAttribute('type', type)
  alert.innerText = message
  document.body.appendChild(alert)
}
