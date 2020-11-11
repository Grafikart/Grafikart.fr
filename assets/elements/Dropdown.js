/**
 * @property {HTMLUListElement} ul
 * @property {HTMLButtonElement} button
 * @property {number} index
 * @property {boolean} isOpen
 */
export class Dropdown extends HTMLElement {
  constructor () {
    super()
    this.open = this.open.bind(this)
    this.close = this.close.bind(this)
    this.toggleMenu = this.toggleMenu.bind(this)
    this.onKeyUp = this.onKeyUp.bind(this)
    this.onKeyDown = this.onKeyDown.bind(this)
    this.onBlur = this.onBlur.bind(this)
  }

  connectedCallback () {
    const button = this.querySelector('button')
    const ul = this.querySelector('ul')
    const id = this.getAttribute('id')
    button.setAttribute('aria-haspopup', 'listbox')
    button.setAttribute('id', `${id}button`)
    button.setAttribute('aria-controls', `${id}list`)
    button.addEventListener('click', this.toggleMenu)
    ul.setAttribute('id', `${id}list`)
    ul.setAttribute('aria-labelledby', `${id}label`)
    ul.setAttribute('tabindex', '-1')
    ul.setAttribute('role', 'listbox')
    ul.addEventListener('keydown', this.onKeyDown)
    ul.addEventListener('blur', this.onBlur)
    ul.querySelectorAll('a').forEach(a => a.setAttribute('tabindex', '-1'))
    document.addEventListener('keyup', this.onKeyUp)
    Array.from(ul.children).forEach((li, index) => {
      li.setAttribute('role', 'option')
      li.setAttribute('id', `${id}-index${index}`)
      if (li.getAttribute('aria-selected') === 'true') {
        this.index = index
      }
    })
    this.ul = ul
    this.button = button
    this.close()
  }

  disconnectedCallback () {
    document.removeEventListener('keyup', this.onKeyUp)
  }

  toggleMenu (e) {
    e.preventDefault()
    if (this.isOpen) {
      this.close()
    } else {
      this.open()
    }
  }

  onKeyUp (e) {
    if (e.key === 'Escape' && this.isOpen) {
      this.button.focus()
      this.close()
    }
  }

  onKeyDown (e) {
    if (e.key === 'ArrowDown' && this.isOpen) {
      e.preventDefault()
      this.select(this.index + 1)
    }
    if (e.key === 'ArrowUp' && this.isOpen) {
      e.preventDefault()
      this.select(this.index - 1)
    }
    if (e.key === 'Home' && this.isOpen) {
      e.preventDefault()
      this.select(0)
    }
    if (e.key === 'End' && this.isOpen) {
      e.preventDefault()
      this.select(this.ul.children.length - 1)
    }
    if (e.key === 'Enter') {
      const li = this.ul.children[this.index]
      if (!li) {
        return
      }
      const a = li.querySelector('a')
      if (a) {
        window.location.href = a.getAttribute('href')
      }
    }
  }

  onBlur (e) {
    if (!this.ul.contains(e.relatedTarget)) {
      this.close()
    } else {
      e.preventDefault()
      e.stopPropagation()
    }
  }

  open () {
    this.button.setAttribute('aria-expanded', 'true')
    this.ul.removeAttribute('aria-hidden', 'true')
    this.ul.removeAttribute('hidden')
    this.isOpen = true
    this.ul.focus()
    this.select(this.index || 0)
  }

  close () {
    this.button.removeAttribute('aria-expanded')
    this.ul.setAttribute('aria-hidden', 'true')
    this.ul.setAttribute('hidden', 'hidden')
    this.isOpen = false
  }

  select (index) {
    if (this.index !== undefined) {
      const current = this.ul.children[this.index]
      current && current.removeAttribute('aria-selected')
    }
    if (index < 0) {
      index = this.ul.children.length - 1
    } else if (index >= this.ul.children.length) {
      index = 0
    }
    const current = this.ul.children[index]
    current.setAttribute('aria-selected', 'true')
    this.ul.setAttribute('aria-activedescendant', current.id)
    this.index = index
  }
}
