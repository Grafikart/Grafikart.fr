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
    const previousElement = drawer.previousElementSibling as HTMLElement
    previousElement.style.viewTransitionName = "drawer"
    if (isVisible) {
      previousElement.style.setProperty("--drawer-width", "0px")
    } else {
      previousElement.style.removeProperty("--drawer-width")
    }

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
