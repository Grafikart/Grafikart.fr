import { jsonFetchOrFlash } from '/functions/api.js'
import SpinningDots from '@grafikart/spinning-dots-element'

export class AutosaveBlur extends HTMLFormElement {
  connectedCallback () {
    this.querySelectorAll('input, textarea').forEach(input => {
      input.addEventListener('blur', this.save.bind(this))
    })
  }

  save () {
    const loader = new SpinningDots()
    this.style.position = 'relative'
    loader.style.position = 'absolute'
    loader.style.top = '8px'
    loader.style.color = 'var(--contrast)'
    loader.style.right = '8px'
    loader.style.height = '16px'
    loader.style.width = '16px'
    this.appendChild(loader)
    jsonFetchOrFlash(this.getAttribute('action') || '', {
      method: this.getAttribute('method'),
      body: new FormData(this)
    })
      .catch(console.error)
      .finally(() => {
        this.removeChild(loader)
      })
  }

  disconnectedCallback () {}
}
