import ReactDiffViewer from 'react-diff-viewer'
import { render } from 'preact'
import { useClickOutside, useToggle } from '/functions/hooks.js'
import { useRef } from 'preact/hooks'

/**
 * @property {HTMLDivElement} container
 * @property {monaco} editor
 */
export class DiffEditor extends HTMLTextAreaElement {
  async connectedCallback () {
    this.container = document.createElement('div')
    this.style.display = 'none'
    this.insertAdjacentElement('beforebegin', this.container)
    this.value = this.getAttribute('updated')
    const onChange = e => {
      this.value = e.target.value
      this.render(onChange, this.container)
    }
    this.render(onChange, this.container)
  }

  render (onChange, container) {
    // On normalise les retours à la ligne pour éviter d'avoir des diffs parasites
    const original = this.getAttribute('original').replace(/\r\n|\r|\n/g, "\r\n")
    const updated = this.value.replace(/\r\n|\r|\n/g, "\r\n")
    render(
      <DiffEditorComponent onChange={onChange} originalValue={original} newValue={updated} />,
      container
    )
  }

  disconnectedCallback () {
    if (this.container) {
      this.container.parentElement.removeChild(this.container)
    }
  }
}

function DiffEditorComponent ({ originalValue, newValue, onChange }) {
  const container = useRef()
  const [editMode, toggleMode] = useToggle(false)
  useClickOutside(container, editMode ? toggleMode : null)
  const DiffComponent = ReactDiffViewer.default ? ReactDiffViewer.default : ReactDiffViewer // Parceque vite ne sait pas gérer ce module
  return (
    <div ref={container}>
      {editMode ? (
        <textarea is='markdown-editor' defaultValue={newValue} onChange={onChange} />
      ) : (
        <div onDblClick={toggleMode}>
          <DiffComponent
            enableSyntaxHighlighting={true}
            compareMethod='diffChars'
            oldValue={originalValue}
            newValue={newValue}
            hideLineNumbers={true}
            splitView={true}
            leftTitle='Article original'
            rightTitle='Révision'
          />
        </div>
      )}
    </div>
  )
}
