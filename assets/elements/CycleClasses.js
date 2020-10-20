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
    const [first, ...other] = this.elements
    this.elements = [...other, first]
    this.applyClasses()
    this.timer = window.setTimeout(this.cycle.bind(this), DURATION)
  }

  disconnectedCallback () {
    window.clearTimeout(this.timer)
  }
}
