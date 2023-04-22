import { debounce } from '/functions/timers'

/**
 * @property {HTMLAnchorElement[]} links
 * @property {Map<HTMLElement, HTMLAnchorElement>} sectionToAnchor
 */
export class GlossaryLetters extends HTMLElement {
  constructor () {
    super()
    this.onIntersection = this.onIntersection.bind(this)
  }

  connectedCallback () {
    const observer = new IntersectionObserver(entries => entries.forEach(this.onIntersection), {
      rootMargin: '-20% 0px -80% 0px'
    })

    // Construction du lien section <=> lien dans le menu
    const links = Array.from(this.querySelectorAll('a'))
    this.sectionToAnchor = new Map(
      links.map(link => {
        const section = document.querySelector(link.getAttribute('href'))
        if (!section) {
          link.setAttribute('disabled', '')
          return [null, link]
        }
        observer.observe(section)
        return [section, link]
      })
    )

    this.onWindowResize = debounce(this.onWindowResize, 500)
    this.onWindowResize()
    this.links = links
  }

  onWindowResize () {
    document.body.style.setProperty('--scrollOffset', `${this.offsetHeight}px`)
  }

  /**
   * @param {IntersectionObserverEntry} entry
   */
  onIntersection (entry) {
    const link = this.sectionToAnchor.get(entry.target)
    if (!link) {
      return
    }
    if (entry.isIntersecting) {
      link.classList.add('is-active')
    } else {
      link.classList.remove('is-active')
    }
  }
}
