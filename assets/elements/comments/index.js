import { h, render } from 'preact'
import { Comments as CommentsComponent } from './Comments.jsx'

/**
 * @property {IntersectionObserver} observer
 */
export class Comments extends HTMLElement {
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
    render(h(CommentsComponent, { target }), this)
    this.observer.disconnect()
  }
}
