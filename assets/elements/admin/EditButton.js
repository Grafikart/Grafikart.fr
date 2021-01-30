export default class EditButton extends HTMLElement {
  connectedCallback () {
    if (!window.grafikart || !window.grafikart.EDIT_LINK) {
      return
    }
    this.removeAttribute('hidden')
    this.innerHTML = `<a class="btn-primary" href="${window.grafikart.EDIT_LINK}">
        <svg class="icon icon-edit">
          <use xlink:href="/sprite.svg#edit"></use>
        </svg>
        Editer
      </a>`
  }
}
