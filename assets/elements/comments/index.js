import { render, h } from 'preact'
import { Comments } from './Comments.jsx'

/**
 * @property {IntersectionObserver} observer
 */
export default class CommentsElement extends HTMLElement {
  constructor () {
    super()
  }

  connectedCallback () {
    this.observer = new IntersectionObserver(observables => {
      observables.forEach(observable => {
        // L'élément devient visible
        if (observable.intersectionRatio > 0) {
          this.attachComments()
        }
      })
    })
    this.observer.observe(this)
  }

  disconnectedCallback () {
    this.observer.disconnect()
  }

  attachComments () {
    const target = this.getAttribute('target')
    render(h(Comments, { target }), this)
    this.observer.disconnect()
  }
}

customElements.define('comments-area', CommentsElement)
