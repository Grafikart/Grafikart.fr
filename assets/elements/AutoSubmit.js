export default class AutoSubmit extends HTMLFormElement {

  connectedCallback () {
    Array.from(this.querySelectorAll('input')).forEach(input => {
      input.addEventListener('change', () => {
        this.submit()
      })
    })
  }

}

customElements.define('auto-submit', AutoSubmit, {extends: 'form'})
