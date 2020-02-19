/**
 * @property {number|null} timer
 * @property {choices} Choices
 * @property {string} endpoint
 */
import Alert from '../Alert'
import SpinningDots from '@grafikart/spinning-dots-element'

export default class InputAttachment extends HTMLInputElement {

  connectedCallback () {
    const preview = this.dataset.image
    this.insertAdjacentHTML('afterend', `
<div class="input-attachment">
<div class="input-attachment__preview" style="background-image:url(${preview})"></div>
</div>
`)
    this.style.display = 'none'
    this.container = this.parentElement.querySelector('.input-attachment')
    this.container.addEventListener('dragenter', this.onDragEnter.bind(this))
    this.container.addEventListener('dragleave', this.ondragleave.bind(this))
    this.container.addEventListener('dragover', this.onDragOver)
    this.container.addEventListener('drop', this.onDrop.bind(this))
    this.preview = this.container.querySelector('.input-attachment__preview')
  }

  disconnectedCallback () {

  }

  onDragEnter (e) {
    e.stopPropagation()
    e.preventDefault()
    this.container.classList.add('is-hovered')
  }

  ondragleave (e) {
    e.stopPropagation()
    e.preventDefault()
    this.container.classList.remove('is-hovered')
  }

  onDragOver (e) {
    e.stopPropagation()
    e.preventDefault()
  }

  async onDrop (e) {
    e.stopPropagation()
    e.preventDefault()
    this.container.classList.add('is-hovered')
    const loader = new SpinningDots()
    loader.classList.add('input-attachment__loader')
    this.container.appendChild(loader)
    const files = e.dataTransfer.files
    if (files.length === 0) return false
    const data = new FormData()
    data.append('file', files[0])
    const response = await fetch(`/admin/attachment/${this.attachmentId}`, {
      method: 'POST',
      body: data
    })
    const responseData = await response.json()
    if (response.status >= 200 && response.status < 300) {
      this.preview.style.backgroundImage = `url(${responseData.url})`
      this.value = responseData.id
    } else {
      const alert = new Alert({message: responseData.error})
      document.querySelector('.dashboard').appendChild(alert)
    }
    this.container.removeChild(loader)
    this.container.classList.remove('is-hovered')
  }

  /**
   * @return {string}
   */
  get attachmentId () {
    return this.value
  }

}


global.customElements.define('input-attachment', InputAttachment, {extends: 'input'})
