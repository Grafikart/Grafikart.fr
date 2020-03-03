/**
 * @property {Editor} editor
 * @property {HTMLButtonElement} element
 */
export default class Button {

  constructor (editor) {
    this.action = this.action.bind(this)
    this.editor = editor
    this.element = null
    const icon = this.icon()
    if (icon) {
      if (this.shortcut() !== false) {
        this.editor.addShortcut(this.shortcut(), this.action)
      }
      this.element = document.createElement('button')
      this.element.addEventListener('click', this.action)
      this.element.appendChild(icon)
    }
  }

  icon () {
    return ''
  }

  shortcut () {
    return false
  }

  action () {
    console.error('Vous devez définir une action pour ce bouton')
  }

}
