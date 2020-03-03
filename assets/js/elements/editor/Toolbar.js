/**
 * @property {HTMLDivElement} element
 */
import {BoldButton, FullScreenButton, LinkButton, SpeechButton} from './buttons'
import {createElement} from '@fn/dom'

export default class Toolbar {

  /**
   * @param {Editor} value
   */
  constructor (editor) {
    this.element = document.createElement('div')
    this.element.classList.add('mdeditor__toolbar')
    const left = createElement('div', {class: 'mdeditor__toolbarleft'})
    const right = createElement('div', {class: 'mdeditor__toolbarright'})
    const fullScreenButton = new FullScreenButton(editor)
    this.addButtons(left, [
      new BoldButton(editor),
      new LinkButton(editor),
      new SpeechButton(editor)
    ])
    this.addButtons(right, [
      fullScreenButton
    ])
    this.element.appendChild(left)
    this.element.appendChild(right)
    fullScreenButton.element.addEventListener('click', () => {
      this.onFullScreen()
    })
  }

  /**
   * @param {Button[]} button
   */
  addButtons (target, buttons) {
    for (const button of buttons) {
      if (button.element !== null) {
        target.appendChild(button.element)
      }
    }
  }

  onFullScreen () {
  }

}
