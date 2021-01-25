/**
 * @property {number|null} timer
 * @property {choices} Choices
 * @property {string} endpoint
 * @property {bool} overwrite L'envoie d'une nouvelle image écrase la précédente
 */
export default class InputAttachment extends HTMLInputElement {
  connectedCallback () {
    const preview = this.getAttribute('preview')
    this.insertAdjacentHTML(
      'afterend',
      `
<div class="input-attachment">
<div class="input-attachment__preview" style="background-image:url(${preview || ''})"></div>
</div>
`
    )
    this.style.display = 'none'
    this.container = this.parentElement.querySelector('.input-attachment')
    this.container.addEventListener('dragenter', this.onDragEnter.bind(this))
    this.container.addEventListener('dragleave', this.ondragleave.bind(this))
    this.container.addEventListener('dragover', this.onDragOver)
    this.container.addEventListener('drop', this.onDrop.bind(this))
    this.container.addEventListener('click', this.onClick.bind(this))
    this.preview = this.container.querySelector('.input-attachment__preview')
    this.overwrite = this.getAttribute('overwrite') !== null
  }

  onDragEnter (e) {
    e.preventDefault()
    this.container.classList.add('is-hovered')
  }

  ondragleave (e) {
    e.preventDefault()
    this.container.classList.remove('is-hovered')
  }

  onDragOver (e) {
    e.preventDefault()
  }

  async onDrop (e) {
    e.preventDefault()
    this.container.classList.add('is-hovered')
    const loader = document.createElement('spinning-dots')
    loader.classList.add('input-attachment__loader')
    this.container.appendChild(loader)
    const files = e.dataTransfer.files
    if (files.length === 0) return false
    const data = new FormData()
    data.append('file', files[0])
    let url = this.getAttribute('data-endpoint')
    if (this.attachmentId !== '' && this.overwrite) {
      url = `${url}/${this.attachmentId}`
    }
    const response = await fetch(url, {
      method: 'POST',
      body: data
    })
    const responseData = await response.json()
    if (response.ok) {
      this.setAttachment(responseData)
    } else {
      const alert = document.createElement('alert-message')
      alert.innerHTML = "Impossible d'envoyer l'image"
      document.querySelector('.dashboard').appendChild(alert)
    }
    this.container.removeChild(loader)
    this.container.classList.remove('is-hovered')
  }

  onClick (e) {
    e.preventDefault()
    const modal = document.createElement('modal-dialog')
    modal.setAttribute('overlay-close', 'overlay-close')
    const fm = document.createElement('file-manager')
    fm.setAttribute('data-endpoint', this.getAttribute('data-endpoint'))
    modal.appendChild(fm)
    fm.addEventListener('file', e => {
      this.setAttachment(e.detail)
      modal.close()
    })
    document.body.appendChild(modal)
  }

  setAttachment (attachment) {
    this.preview.style.backgroundImage = `url(${attachment.url})`
    this.value = attachment.id
    const changeEvent = document.createEvent('HTMLEvents')
    changeEvent.initEvent('change', false, true)
    this.dispatchEvent(changeEvent)
    this.dispatchEvent(new CustomEvent('attachment', { detail: attachment }))
  }

  /**
   * @return {string}
   */
  get attachmentId () {
    return this.value
  }
}
