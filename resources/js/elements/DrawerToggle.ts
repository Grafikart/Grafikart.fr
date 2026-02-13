export class DrawerToggle extends HTMLElement {
  connectedCallback() {
    this.addEventListener("click", this.toggleDrawer)
  }

  toggleDrawer = () => {
    const drawer = document.querySelector<HTMLDivElement>("#drawer")
    if (!drawer) {
      return
    }
    const isVisible = this.isDrawerVisible(drawer)
    drawer.classList.remove("hidden")
    document.body.dataset.drawer = isVisible ? "hidden" : "visible"
  }

  isDrawerVisible(drawer: HTMLDivElement) {
    if (document.body.dataset.drawer === undefined) {
      document.body.dataset.drawer =
        drawer.getBoundingClientRect().width === 0 ? "hidden" : "visible"
    }
    return document.body.dataset.drawer === "visible"
  }
}
