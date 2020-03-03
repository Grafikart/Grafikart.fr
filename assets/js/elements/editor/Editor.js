import CodeMirror from 'codemirror'
import 'codemirror/mode/markdown/markdown'
import 'codemirror/lib/codemirror.css'
import 'codemirror/theme/neo.css'

/**
 * Objet permettant de construire un éditeur CodeMirror
 *
 * @property {HTMLDivElement} element
 * @property {CodeMirror} editor
 */
export default class Editor {

  /**
   * @param {string} value
   */
  constructor (value = '') {
    this.element = document.createElement('div')
    this.element.classList.add('mdeditor__editor')
    this.editor = new CodeMirror(this.element, {
      value: value,
      mode: 'markdown',
      theme: 'neo',
      lineWrapping: true,
      cursorBlinkRate: 0,
      viewportMargin: Infinity,
    })
    window.requestAnimationFrame(() => {
      this.editor.refresh()
    })
    this.editor.on('change', (cm) => {
      this.onChange(cm.getValue())
    })
  }

  /**
   * Entoure la selection.
   *
   * @param {string} start
   * @param {string|null} end
   */
  wrapWith (start, end = null) {
    if (end === null) {
      end = start
    }
    this.editor.getDoc().replaceSelection(start + this.editor.getDoc().getSelection() + end)
    this.editor.focus()
  }

  /**
   * Remplace la selection par la valeur donnée.
   *
   * @param {string} value
   */
  replace (value) {
    this.editor.getDoc().replaceSelection(value)
    this.editor.focus()
  }

  /**
   * Ajoute un racourci à l'éditeur
   *
   * @param {string} shortcut
   * @param {function} action
   */
  addShortcut(shortcut, action) {
    this.editor.setOption('extraKeys', {
      ...this.editor.getOption('extraKeys'),
      [shortcut]: action
    })
  }

  /**
   * Fonction appelée lors du changement de valeur de l'éditeur
   * @param {string} value
   */
  onChange (value) {}

}
