/**
 * Given a click event, return the <a> element to navigate to, or null if the click should not trigger navigation.
 */
export function findLinkFromClick(event: MouseEvent): HTMLAnchorElement | null {
  if (event.defaultPrevented) {
    return null
  }
  if (
    event.button !== 0 ||
    event.altKey ||
    event.ctrlKey ||
    event.metaKey ||
    event.shiftKey
  ) {
    return null
  }

  const target =
    (event.composedPath()[0] as Element) ?? (event.target as Element)
  if (target instanceof HTMLElement && target.isContentEditable) {
    return null
  }

  const link = target.closest<HTMLAnchorElement>("a[href]")
  if (!link) {
    return null
  }

  const href = link.getAttribute("href")
  if (!href || href.startsWith("#")) {
    return null
  }
  if (link.hasAttribute("download")) {
    return null
  }

  const linkTarget = link.getAttribute("target")
  if (linkTarget && linkTarget !== "_self") {
    return null
  }

  const turboContainer = link.closest("[data-turbo]")
  if (turboContainer && turboContainer.getAttribute("data-turbo") === "false") {
    return null
  }

  const url = new URL(link.href, document.baseURI)

  if (url.origin !== window.location.origin) {
    return null
  }

  if (/\.\w+$/.test(url.pathname)) {
    return null
  }

  return link
}
