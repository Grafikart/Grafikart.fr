import { throttle } from "@/lib/timer.ts"

const SCROLL_DELTA = 20

enum HeaderState {
  DEFAULT = "default",
  FIXED = "fixed",
  HIDDEN = "hidden",
}

/**
 * A custom element that handles header visibility based on scroll position
 */
export class SiteHeader extends HTMLElement {
  private currentTop = 0
  private previousTop = 0
  private scrolling = false
  private scrollOffset = 0
  private state = HeaderState.DEFAULT
  private scrollListener: (() => void) | null = null

  connectedCallback() {
    this.scrollOffset = this.offsetHeight

    this.classList.add(
      this.nextElementSibling?.classList.contains("bg-background-light")
        ? "bg-background-light"
        : "bg-background",
    )

    this.scrollListener = throttle(() => {
      if (!this.scrolling) {
        this.scrolling = true
        window.requestAnimationFrame(() => this.autoHideHeader())
      }
    }, 100)

    window.addEventListener("scroll", this.scrollListener, {
      passive: true,
    })
  }

  disconnectedCallback() {
    if (this.scrollListener) {
      window.removeEventListener("scroll", this.scrollListener)
      this.scrollListener = null
    }
  }

  private autoHideHeader() {
    this.currentTop = document.documentElement.scrollTop

    // Check if scrolled past header height
    if (this.currentTop > this.offsetHeight) {
      // Scrolling down - hide header
      if (
        this.currentTop - this.previousTop > SCROLL_DELTA &&
        this.currentTop > this.scrollOffset
      ) {
        this.setState(HeaderState.HIDDEN)
      }
      // Scrolling up - show fixed header
      else if (this.previousTop - this.currentTop > SCROLL_DELTA) {
        this.setState(HeaderState.FIXED)
      }
    } else {
      // At the top - default state
      this.setState(HeaderState.DEFAULT)
    }

    this.previousTop = this.currentTop
    this.scrolling = false
  }

  private setState(newState: HeaderState) {
    if (newState === this.state) {
      return
    }

    this.setAttribute("data-state", newState)
    document.body.style.setProperty(
      "--header-height",
      newState === HeaderState.HIDDEN ? "0px" : `${this.offsetHeight}px`,
    )
    if (newState === HeaderState.HIDDEN) {
      this.classList.add("-translate-y-full")
    } else if (newState === HeaderState.FIXED) {
      this.classList.remove("-translate-y-full")
    } else if (newState === HeaderState.DEFAULT) {
      this.classList.remove("-translate-y-full")
    }

    this.state = newState
  }
}
