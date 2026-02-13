/**
 * Render a new document into the current page by merging <head> and replacing <body>.
 * Returns a promise that resolves when new stylesheets have loaded.
 */
export async function renderPage(newDoc: Document): Promise<void> {
  mergeHead(newDoc.head)
  replaceBody(newDoc.body as HTMLBodyElement)
  updateHtmlAttributes(newDoc.documentElement)
}

function updateHtmlAttributes(newDocumentElement: HTMLElement): void {
  for (const attr of ["lang", "dir"] as const) {
    const value = newDocumentElement.getAttribute(attr)
    if (value) {
      document.documentElement.setAttribute(attr, value)
    } else {
      document.documentElement.removeAttribute(attr)
    }
  }
}

function mergeHead(newHead: HTMLHeadElement): void {
  const currentElements = Array.from(document.head.children)
  const newElements = Array.from(newHead.children)

  // Remove current provisional elements not in new head
  for (const el of currentElements) {
    if (isScript(el) || isStylesheet(el)) {
      continue
    }
    if (!hasMatchInList(el, newElements)) {
      el.remove()
    }
  }

  // Add new elements not already present
  for (const el of newElements) {
    if (isScript(el)) {
      if (!hasMatchInList(el, currentElements)) {
        document.head.appendChild(activateScript(el as HTMLScriptElement))
      }
      continue
    }
    if (isStylesheet(el)) {
      if (!hasMatchInList(el, currentElements)) {
        document.head.appendChild(el)
      }
      continue
    }
    if (!hasMatchInList(el, currentElements)) {
      document.head.appendChild(el)
    }
  }
}

function replaceBody(newBody: HTMLBodyElement): void {
  document.adoptNode(newBody)
  activateBodyScripts(newBody)
  document.body.replaceWith(newBody)
}

function activateBodyScripts(body: HTMLBodyElement): void {
  for (const script of body.querySelectorAll("script")) {
    script.replaceWith(activateScript(script))
  }
}

function activateScript(original: HTMLScriptElement): HTMLScriptElement {
  const script = document.createElement("script")
  for (const attr of original.attributes) {
    script.setAttribute(attr.name, attr.value)
  }
  script.textContent = original.textContent
  script.async = false
  return script
}

function hasMatchInList(element: Element, list: Element[]): boolean {
  if (element.tagName === "TITLE") {
    return list.some(
      (el) => el.tagName === "TITLE" && el.innerHTML === element.innerHTML,
    )
  }
  return list.some((el) => el.isEqualNode(element))
}

function isScript(element: Element): boolean {
  return element.localName === "script"
}

function isStylesheet(element: Element): boolean {
  return (
    element.localName === "style" ||
    (element.localName === "link" &&
      element.getAttribute("rel") === "stylesheet")
  )
}
