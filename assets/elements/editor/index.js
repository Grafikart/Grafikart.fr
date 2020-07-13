import './editor.scss'
import Editor from './Editor'
import Toolbar from './Toolbar'
import { createElement } from '/functions/dom.js'

/**
 * @property {HTMLDivElement} container
 * @property {HTMLFormElement|null} form
 * @property {Editor|null} editor
 */
export class MarkdownEditor extends HTMLTextAreaElement {
  constructor () {
    super()
    this.toggleFullscreen = this.toggleFullscreen.bind(this)
    this.onFormReset = this.onFormReset.bind(this)
  }

  async connectedCallback () {
    const editor = new Editor(this.value, this.getAttribute('original'))
    await editor.boot()
    const toolbar = new Toolbar(editor)

    // Construction du DOM
    this.container = createElement('div', { class: 'mdeditor' })
    this.container.appendChild(toolbar.element)
    this.container.appendChild(editor.element)

    // Evènement
    toolbar.onFullScreen = this.toggleFullscreen
    editor.onChange = value => {
      this.value = value
      this.dispatchEvent(
        new Event('input', {
          bubbles: true,
          cancelable: true
        })
      )
    }
    this.syncEditor = () => editor.setValue(this.value)
    if (this.form) {
      this.form.addEventListener('reset', this.onFormReset)
    }

    // On ajoute au dom
    this.insertAdjacentElement('beforebegin', this.container)
    this.style.display = 'none'
    this.editor = editor
  }

  disconnectedCallback () {
    if (this.form) {
      this.form.removeEventListener('reset', this.onFormReset)
    }
    if (this.container) {
      this.container.remove()
    }
  }

  onFormReset () {
    if (this.editor) {
      this.editor.setValue('')
    }
  }

  toggleFullscreen () {
    this.container.classList.toggle('mdeditor--fullscreen')
  }

  /**
   * Permet de forcer la synchronisation de l'éditeur depuis le textarea (utile quand le composant est monté dans react)
   */
  syncEditor () {}
}
