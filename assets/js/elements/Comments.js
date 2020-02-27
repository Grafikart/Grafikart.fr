import CommentsSvelte from './comments/Comments.svelte'

/**
 * @property {IntersectionObserver} observer
 */
export default class Comments extends HTMLElement {

  constructor() {
    super()
  }

  connectedCallback () {
    this.observer = new IntersectionObserver((observables) => {
      observables.forEach((observable) => {
        // L'élément devient visible
        if (observable.intersectionRatio > 0) {
          this.attachComments()
        }
      })
    })
    // this.observer.observe(this)
    this.attachComments()
  }

  disconnectedCallback () {
    this.observer.disconnect()
  }

  attachComments () {
    const target = this.getAttribute('target')
    new CommentsSvelte({
      target: this,
      props: {
        target
      }
    })
    this.observer.disconnect()
  }

}

customElements.define('comments-area', Comments)
