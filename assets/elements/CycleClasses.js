const DURATION = 5000

/**
 * @property {number} timer
 * @property {HTMLElement[]} elements
 */
export class CycleClasses extends HTMLElement {
  connectedCallback () {
    this.elements = Array.from(this.children)
    this.applyClasses()
    this.timer = window.setTimeout(this.cycle.bind(this), DURATION)
  }

  applyClasses () {
    this.elements.forEach((child, index) => {
      child.classList.remove(child.dataset.currentClass)
      child.dataset.currentClass = `item-${index}`
      child.classList.add(`item-${index}`)
    })
  }

  cycle () {
    this.elements = [this.elements[this.elements.length - 1], ...this.elements.slice(0, this.elements.length - 1)]
    this.applyClasses()
    this.timer = window.setTimeout(this.cycle.bind(this), DURATION)
  }

  disconnectedCallback () {
    window.clearTimeout(this.timer)
  }
}
