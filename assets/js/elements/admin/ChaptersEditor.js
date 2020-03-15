import {closest, html} from '@fn/dom'
import {enterKeyListener} from '@fn/keyboard'
import Sortable from 'sortablejs'

/**
 * Construit un élément représentant un chapitre
 *
 * @param {IChapter} chapter
 * @param {function} onUpdate
 * @return {HTMLLIElement}
 */
function Chapter ({chapter, onUpdate, onRemove, onAdd}) {
  return html`
    <li data-title="${chapter.title}">
      <input type="text" value="${chapter.title}" class="chapters-editor__chapter" onblur=${onUpdate}/>
      <ul>
        ${chapter.courses.map(c => html`<${Course} course=${c} onRemove=${onRemove} />`)}
        <li class="chapters-editor__add">
          <input type="text" placeholder="Ajouter un cours" onkeydown=${enterKeyListener(onAdd)}/>
          <button type="button" onclick=${onAdd}>
            <svg class="icon icon-add">
              <use xlink:href="/sprite.svg#delete"></use>
            </svg>
          </button>
        </li>
      </ul>
    </li>`
}

/**
 * Construit un élément représentant un cours
 *
 * @param {ICourse} course
 * @param {function} onRemove
 * @return {HTMLLIElement}
 */
function Course ({course, onRemove}) {
  return html`
    <li
      class="chapters-editor__course"
      data-title=${course.title}
      data-id=${course.id}
    >
      <span>${course.title}</span>
      <button type="button" onclick=${onRemove}>
        <svg class="icon icon-delete">
          <use xlink:href="/sprite.svg#delete"></use>
        </svg>
      </button>
    </li>`
}

/**
 * CustomElement pour la gestion des chapitres associé aux formations
 *
 * @property {HTMLUListElement} list <ul> contenant la liste des chapitres
 * @typedef {{title: string, courses: ICourse[]}} IChapter
 * @typedef {{id: number, title: string}} ICourse
 */
export default class ChaptersEditor extends HTMLTextAreaElement {

  constructor () {
    super()
    this.sortables = []
    this.updateInput = this.updateInput.bind(this)
    this.removeCourse = this.removeCourse.bind(this)
  }

  connectedCallback () {
    // this.style.display = 'none'
    this.list = this.renderList()
    this.bindSortable()
    this.insertAdjacentElement('afterend', this.list)
  }

  /**
   * Construit la liste de chapitre
   *
   * @param {IChapter[]} chapters
   * @return HTMLUListElement
   */
  renderList () {
    const chapters = JSON.parse(this.value)
    return html`
      <ul class="chapters-editor stack">
        ${chapters.map(chapter =>
          html`<${Chapter}
            chapter=${chapter}
            onUpdate=${this.updateInput}
            onRemove=${this.removeCourse}
            onAdd="${this.addCourse}"
            />`
        )}
      </ul>`
  }

  /**
   * Ajoute un cours
   *
   * @param {KeyboardEvent} e
   */
  addCourse (e) {
    e.preventDefault()
    e.stopPropagation()
    const li = closest(e.currentTarget, 'li')
    const input = li.querySelector('input')
    const value = input.value
    console.log(value)
  }

  /**
   * Supprime un cours de la liste des chapitres
   *
   * @param {MouseEvent} e
   */
  async removeCourse (e) {
    e.preventDefault()
    if (e.currentTarget instanceof HTMLButtonElement) {
      const li = closest(e.currentTarget, 'li')
      li.parentElement.removeChild(li)
      this.updateInput()
    }
  }

  /**
   * Greffer le comportement sortablejs
   */
  bindSortable () {
    const options = {
      group: 'nested',
      animation: 150,
      fallbackOnBody: true,
      swapThreshold: 0.65,
      filter: '.chapters-editor__add',
      preventOnFilter: false,
      onEnd: () => this.updateInput()
    }
    this.sortables = Array.from(this.list.querySelectorAll('ul')).map(u => {
      return new Sortable(u, options)
    })
    this.sortables.push(
      new Sortable(this.list, {
        ...options,
        group: 'parent'
      })
    )
  }

  /**
   * Met à jour le champs avec les nouvelles données
   *
   * @param {HTMLUListElement} ul
   * @param {HTMLTextAreaElement} input
   */
  updateInput () {
    const newChapters = []
    Array.from(this.list.children).forEach(li => {
      const courses = li.querySelectorAll('li')
      // Il n'y a plus de cours dans ce chapitre
      if (courses.length === 0) {
        li.parentElement.removeChild(li)
        return
      }
      // On ajoute le chapitre au tableau
      newChapters.push({
        title: li.querySelector('input').value,
        courses: Array.from(courses).map(l => {
          if (l.dataset.id === undefined) { return null }
          return {
            id: l.dataset.id,
            title: l.dataset.title
          }
        }).filter(c => c !== null)
      })
    })
    this.value = JSON.stringify(newChapters)
  }

  disconnectedCallback () {
    this.sortables.forEach(sortable => sortable.destroy())
    if (this.list.parentElement) {
      this.list.parentElement.removeChild(this.list)
    }
  }

}

customElements.define('chapters-editor', ChaptersEditor, {extends: 'textarea'})
