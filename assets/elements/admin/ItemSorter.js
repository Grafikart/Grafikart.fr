import Sortable from 'sortablejs'
import { jsonFetch } from '/functions/api.js'
import SpinningDots from '@grafikart/spinning-dots-element'

/**
 * Construit un élément représentant un élément
 *
 * @param {ICourse} course
 * @param {function} onRemove
 * @return {HTMLLIElement}
 */
function createLi (item) {
  const li = document.createElement('li')
  li.classList.add('sortable-item')
  li.setAttribute('class', 'sortable-item')
  li.setAttribute('data-id', item.id.toString())
  li.innerHTML = `
    <a href="${item.url}">${item.name}</a>
    <button type="button" class="sortable-item__delete">
      <svg class="icon icon-add">
        <use xlink:href="/sprite.svg#delete"></use>
      </svg>
    </button>
  `
  li.querySelector('button').addEventListener('click', async e => {
    if (confirm('Sûr ?')) {
      e.preventDefault()
      const loader = showLoader(li)
      await jsonFetch(item.url, { method: 'DELETE' })
      hideLoader(loader)
      li.parentElement.removeChild(li)
    }
  })
  const ul = document.createElement('ul')
  ul.setAttribute('data-parent', item.id.toString())
  if (item.children.length > 0) {
    item.children.forEach(i => ul.appendChild(createLi(i)))
  }
  li.appendChild(ul)
  return li
}

/**
 * @param {HTMLElement} item
 * @return {Element}
 */
function showLoader (item) {
  const loader = new SpinningDots()
  loader.classList.add('sortable-item__loader')
  item.insertAdjacentElement('afterbegin', loader)
  return loader
}

/**
 * @param {HTMLElement} item
 */
function hideLoader (loader) {
  loader.parentElement.removeChild(loader)
}

/**
 * CustomElement pour la gestion d'une liste réorganisable sur 2 niveaux
 *
 * @property {HTMLUListElement} list <ul> contenant la liste des chapitres
 * @property {string} editPath URL d'édition d'un cours
 * @typedef {{title: string, courses: ICourse[]}} IChapter
 * @typedef {{id: number, title: string}} ICourse
 */
export class ItemSorter extends HTMLElement {
  constructor () {
    super()
    this.sortables = []
    this.sortableOptions = {
      group: 'nested',
      animation: 150,
      fallbackOnBody: true,
      swapThreshold: 0.65,
      preventOnFilter: false,
      onEnd: this.persist.bind(this)
    }
  }

  connectedCallback () {
    this.list = this.renderList()
    this.appendChild(this.list)
    this.bindSortable()
  }

  /**
   * Construit la liste de chapitre
   *
   * @param {IChapter[]} chapters
   * @return HTMLUListElement
   */
  renderList () {
    const items = JSON.parse(this.getAttribute('items'))
    const ul = document.createElement('ul')
    ul.setAttribute('class', 'sortable-items stack')
    items.forEach(i => ul.appendChild(createLi(i)))
    return ul
  }

  /**
   * Greffer le comportement sortablejs
   */
  bindSortable () {
    this.sortables = Array.from(this.list.querySelectorAll('ul')).map(u => {
      return new Sortable(u, this.sortableOptions)
    })
    this.sortables.push(
      new Sortable(this.list, {
        ...this.sortableOptions
      })
    )
  }

  disconnectedCallback () {
    this.sortables.forEach(sortable => sortable.destroy())
    if (this.list.parentElement) {
      this.list.parentElement.removeChild(this.list)
    }
  }

  async persist (e) {
    const parentId = e.to.dataset.parent || null
    const positions = Array.from(e.to.children).map((li, k) => {
      return {
        id: li.dataset.id,
        position: k,
        parent: parentId
      }
    })
    const loader = showLoader(e.item)
    await jsonFetch(this.getAttribute('endpoint'), {
      method: 'POST',
      body: JSON.stringify({
        positions
      })
    })
    hideLoader(loader)
  }
}
