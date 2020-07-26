export class AutoSubmit extends HTMLFormElement {
  connectedCallback () {
    Array.from(this.querySelectorAll('input')).forEach(input => {
      input.addEventListener('change', () => {
        this.submit()
      })
    })
  }
}
