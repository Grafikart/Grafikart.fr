import { throttle } from "@/lib/timer.ts"

export class ScrollTop extends HTMLElement {
  private isVisible = false

  constructor() {
    super()
    this.onScroll = throttle(this.onScroll.bind(this), 100)
  }

  connectedCallback() {
    this.addEventListener("click", () => {
      window.scrollTo({
        top: 0,
        behavior: "smooth",
      })
    })
    // oxlint-disable-next-line typescript/unbound-method
    window.addEventListener("scroll", this.onScroll)
  }

  disconnectedCallback() {
    // oxlint-disable-next-line typescript/unbound-method
    window.removeEventListener("scroll", this.onScroll)
  }

  onScroll() {
    const threshold = window.innerHeight / 3
    if (window.scrollY > threshold && !this.isVisible) {
      this.removeAttribute("hidden")
      this.isVisible = true
    } else if (window.scrollY < threshold && this.isVisible) {
      this.setAttribute("hidden", "hidden")
      this.isVisible = false
    }
  }
}
