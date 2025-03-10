export default class EditButton extends HTMLElement {
  connectedCallback() {
    if (!window.grafikart || !window.grafikart.EDIT_LINK) {
      return;
    }
    this.removeAttribute("hidden");
    const parentClasses = this.getAttribute("class") ?? "";
    this.removeAttribute("class");
    this.innerHTML = `<a href="${window.grafikart.EDIT_LINK}" class="${parentClasses}">
        <svg class="icon icon-edit">
          <use href="/sprite.svg#edit"></use>
        </svg>
        Editer
      </a>`;
  }
}
