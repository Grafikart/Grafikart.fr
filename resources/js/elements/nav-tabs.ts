import { siblings } from "@/lib/dom.ts"

export class NavTabs extends HTMLElement {
  connectedCallback() {
    this.querySelectorAll("a").forEach((a) => {
      a.addEventListener("click", (e) => {
        e.preventDefault()
        if (a.hasAttribute("aria-selected")) {
          return
        }
        // Toggle aria-selected attribute
        a.setAttribute("aria-selected", "true")
        siblings(a).forEach((el) => el.removeAttribute("aria-selected"))
        // Toggle hidden state
        const id = a.getAttribute("href")
        if (!id) {
          return
        }
        const target = document.querySelector<HTMLDivElement>(id)
        if (!target) {
          return
        }
        target.removeAttribute("hidden")
        siblings(target).forEach((el) => el.setAttribute("hidden", ""))
      })
    })
  }
}
