import {createElement} from '@fn/dom'
import Sortable from 'sortablejs'

/**
 * Représentation d'un commentaire de l'API
 * @typedef {{title: string, courses: Course[]}} Chapter
 * @typedef {{id: number, title: string}} Course
 */

/**
 * Construit la liste de chapitre
 *
 * ```html
 * <ul class="chapters-editor stack-large">
 *    <li data-title={chapter.title}>
 *      <input class="chapters-editor__chapter" bind:value={chapter.title} />
 *      <ul>
 *        <li class="chapters-editor__course" data-title={course.title} data-id={course.id}>{course.title}</li>
 *      </ul>
 *    </li>
 * </ul>
 * ```
 *
 * @param {Chapter[]} chapters
 * @return HTMLUListElement
 */
function buildList (chapters, input) {
  // On construit notre <ul> racine
  const ul = createElement('ul', {
    class: 'chapters-editor stack-large'
  })

  // On ajoute les chapitres au <ul>
  chapters.forEach(function (chapter) {
    const li = buildChapterElement(chapter, function () {
      updateInput(ul, input)
    })
    ul.appendChild(li)
  })

  return ul
}

/**
 * Construit le <li> d'un chapitre
 *
 * @param {Chapter} chapter
 */
function buildChapterElement (chapter, onUpdate) {
  // Le champs permettant de mettre à jour le titre
  const input = createElement('input', {
    class: 'chapters-editor__chapter',
    value: chapter.title,
    onblur: onUpdate
  })

  // On construit le ul enfant (qui contiendra les cours
  const lis = chapter.courses.map(course => buildCourseElement(course, onUpdate))
  const nestedUl = createElement('ul', {}, ...lis)

  // On construit le <li> du chapitre avec les éléments précédents
  return createElement('li', {
    'data-title': chapter.title
  }, input, nestedUl)
}

/**
 * Construit le <li> d'un cours
 * @param {Course} course
 */
function buildCourseElement (course, onDelete) {
  const span = createElement('span', {}, course.title)
  const deleteButton = createElement('button', {}, 'supprimer')
  return createElement('li', {
    class: 'chapters-editor__course',
    'data-id': course.id,
    'data-title': course.title,
    onClick: function (e) {
      e.currentTarget.parentElement.removeChild(e.currentTarget)
      onDelete()
    }
  }, span, deleteButton)
}

/**
 * Met à jour le champs avec les nouvelles données
 *
 * @param {HTMLUListElement} ul
 * @param {HTMLTextAreaElement} input
 */
function updateInput (ul, input) {
  const newChapters = []
  Array.from(ul.children).forEach(li => {
    newChapters.push({
      title: li.querySelector('input').value,
      courses: Array.from(li.querySelectorAll('li')).map(l => {
        return {
          id: l.dataset.id,
          title: l.dataset.title
        }
      })
    })
  })
  input.value = JSON.stringify(newChapters)
}

export default class ChaptersEditor extends HTMLTextAreaElement {

  connectedCallback () {
    const chapters = JSON.parse(this.value)
    const ul = buildList(chapters, this)
    const uls = [...Array.from(ul.querySelectorAll('ul')), ul]
    this.insertAdjacentElement('afterend', ul)
    uls.forEach(u => {
      new Sortable(u, {
        group: 'nested',
        animation: 150,
        fallbackOnBody: true,
        swapThreshold: 0.65,
        onEnd: () => updateInput(ul, this)
      })
    })

  }

  disconnectedCallback () {

  }

}

customElements.define('chapters-editor', ChaptersEditor, {extends: 'textarea'})
