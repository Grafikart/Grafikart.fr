/**
 * Objet permettant de construire un éditeur CodeMirror
 *
 * @property {HTMLDivElement} element
 * @property {CodeMirror} editor
 * @property {string} value
 * @property {object} options
 */
export default class Editor {
  /**
   * @param {string} value
   * @param {object} options
   */
  constructor (value = '', options = {}) {
    this.value = value
    this.options = options
    this.element = document.createElement('div')
    this.element.classList.add('mdeditor__editor')
  }

  /**
   * Démarre l'éditeur
   */
  async boot () {
    const { default: CodeMirror } = await import('/libs/CodeMirror.js')
    this.editor = new CodeMirror(this.element, {
      value: this.value,
      mode: 'markdown',
      theme: 'neo',
      lineWrapping: true,
      cursorBlinkRate: 0,
      viewportMargin: Infinity,
      ...this.options
    })
    window.requestAnimationFrame(() => {
      this.editor.refresh()
      if (this.options.autofocus) {
        this.focus()
      }
    })
    this.editor.on('change', cm => {
      this.onChange(cm.getValue())
    })
  }

  /**
   * Entoure la selection.
   *
   * @param {string} start
   * @param {null|string} end
   */
  wrapWith (start, end = null) {
    if (end === null) {
      end = start
    }
    const selection = this.editor.getSelection()
    this.editor.getDoc().replaceSelection(start + this.editor.getDoc().getSelection() + end)
    if (selection === '') {
      const cursor = this.editor.getCursor()
      this.editor.setCursor({ ...cursor, ch: cursor.ch - end.length })
    }
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
   * Remplace la selection par la valeur donnée.
   *
   * @param {string} value
   */
  setValue (value) {
    if (this.editor && value !== this.editor.getValue()) {
      this.editor.setValue(value)
    }
  }

  /**
   * Ajoute un racourci à l'éditeur
   *
   * @param {string} shortcut
   * @param {function} action
   */
  addShortcut (shortcut, action) {
    this.editor.setOption('extraKeys', {
      ...this.editor.getOption('extraKeys'),
      [shortcut]: action
    })
  }

  /**
   * Fonction appelée lors du changement de valeur de l'éditeur
   *
   * @param {string} value
   */
  onChange () {}

  /**
   * Focus the field and go to the last character
   */
  focus () {
    this.editor.focus()
    this.editor.setCursor(this.editor.lineCount(), 0)
  }
}
