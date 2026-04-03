import { siblings } from "@/lib/dom.ts"

export class NavTabs extends HTMLElement {
  connectedCallback() {
    const tabs = this.tabs

    tabs.forEach((tab) => {
      tab.addEventListener("click", (e) => {
        e.preventDefault()
        if (tab.getAttribute("aria-selected") === "true") {
          return
        }
        this.selectTab(tab)
      })
    })

    if (!window.location.hash) {
      return
    }

    const currentTab = tabs.find(
      (tab) => tab.getAttribute("href") === window.location.hash,
    )

    if (!currentTab) {
      return
    }

    this.selectTab(currentTab)
  }

  private get tabs(): HTMLAnchorElement[] {
    return Array.from(this.querySelectorAll<HTMLAnchorElement>("a"))
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
  }
}
