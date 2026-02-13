import { findLinkFromClick } from "./link.ts"
import { performVisit, type VisitAction } from "./visit.ts"

let started = false
let currentPathname = window.location.pathname

export function start(): void {
  if (started) {
    return
  }
  started = true
  history.scrollRestoration = "manual"
  history.replaceState({ turbo: true }, "", window.location.href)
  window.addEventListener("click", onClick)
  window.addEventListener("popstate", onPopState)
}

export function stop(): void {
  if (!started) {
    return
  }
  started = false
  history.scrollRestoration = "auto"
  window.removeEventListener("click", onClick)
  window.removeEventListener("popstate", onPopState)
}

export function visit(
  url: string,
  options: { action?: VisitAction } = {},
): void {
  performVisit(new URL(url, document.baseURI), options.action ?? "advance")
}

function onClick(event: MouseEvent): void {
  const link = findLinkFromClick(event)
  if (!link) {
    return
  }
  event.preventDefault()
  const url = new URL(link.href, document.baseURI)
  const action: VisitAction =
    link.getAttribute("data-turbo-action") === "replace" ? "replace" : "advance"
  currentPathname = url.pathname
  performVisit(url, action)
}

function onPopState(): void {
  if (window.location.pathname === currentPathname) {
    return
  }
  currentPathname = window.location.pathname
  performVisit(new URL(window.location.href), "restore")
}
