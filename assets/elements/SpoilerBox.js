import {strToDom} from '/functions/dom'

export class SpoilerBox extends HTMLElement {
  connected = false

  connectedCallback () {
    if (this.connected) {
      return
    }
    this.connected = true
    const div = document.createElement('div')
    div.innerHTML = this.innerHTML
    this.innerHTML = ''
    div.style.display = 'none'

    const p = strToDom('<p><button class="btn-secondary">Voir la réponse</button></p>')
    const button = p.firstElementChild
    button.addEventListener('click', async () => {
      div.style.display = 'block'
      p.remove()
    })
    this.appendChild(p)
    this.appendChild(div)
    this.style.display = 'block'
  }
}
