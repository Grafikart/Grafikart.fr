import './editor.scss'
import Editor from './Editor'
import Toolbar from './Toolbar'
import {createElement} from '@fn/dom'

/**
 * @property {HTMLDivElement} container
 */
class MarkdownEditor extends HTMLTextAreaElement {

  constructor () {
    super()
    this.toggleFullscreen = this.toggleFullscreen.bind(this)
  }

  connectedCallback () {
    const editor = new Editor(this.value)
    const toolbar = new Toolbar(editor)

    // Construction du DOM
    this.container = createElement('div', {class: 'mdeditor'})
    this.container.appendChild(toolbar.element)
    this.container.appendChild(editor.element)

    // Evènement
    toolbar.onFullScreen = this.toggleFullscreen
    editor.onChange = (value) => this.value = value

    // On ajoute au dom
    this.insertAdjacentElement('beforebegin', this.container)
    this.style.display = 'none'
  }

  toggleFullscreen () {
    this.container.classList.toggle('mdeditor--fullscreen')
  }

}

customElements.define('markdown-editor', MarkdownEditor, {extends: 'textarea'})
