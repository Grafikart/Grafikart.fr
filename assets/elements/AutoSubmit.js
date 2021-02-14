export class AutoSubmit extends HTMLFormElement {
  connectedCallback () {
    Array.from(this.querySelectorAll('input, select')).forEach(input => {
      input.addEventListener('change', () => {
        this.submit()
      })
    })
  }
}
