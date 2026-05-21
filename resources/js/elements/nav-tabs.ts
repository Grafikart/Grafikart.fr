import { siblings } from "@/lib/dom.ts"

export class NavTabs extends HTMLElement {
  connectedCallback() {
    const tabs = this.tabs

    tabs.forEach((tab) => {
      tab.addEventListener("click", (e) => {
        if (tab.getAttribute("aria-selected") === "true") {
          e.preventDefault()
          return
        }
      })
    })

    window.addEventListener("hashchange", this.selectCurrentHashTab)
    this.selectCurrentHashTab()
  }

  disconnectedCallback() {
    window.removeEventListener("hashchange", this.selectCurrentHashTab)
  }

  private get tabs(): HTMLAnchorElement[] {
    return Array.from(this.querySelectorAll<HTMLAnchorElement>("a"))
  }

  private selectCurrentHashTab = (): void => {
    if (!window.location.hash) {
      return
    }

    const currentTab = this.tabs.find(
      (tab) => tab.getAttribute("href") === window.location.hash,
    )

    if (!currentTab) {
      return
    }

    this.selectTab(currentTab)
  }

  private selectTab(tab: HTMLAnchorElement): void {
    tab.setAttribute("aria-selected", "true")
    siblings(tab).forEach((el) => el.removeAttribute("aria-selected"))

    const id = tab.getAttribute("href")
    if (!id) {
      return
    }

    const target = document.querySelector<HTMLDivElement>(id)
    if (!target) {
      return
    }

    target.removeAttribute("hidden")
    siblings(target).forEach((el) => el.setAttribute("hidden", ""))

    // Scroll if the content is too far away from the visible area
    if (target.getBoundingClientRect().y > window.innerHeight / 2) {
      this.scrollIntoView({ behavior: "smooth", block: "start" })
    }
  }
}
