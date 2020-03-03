/**
 * Trouve la position de l'élément par rapport au haut de la page de manière recursive
 *
 * @param {HTMLElement} element
 */
export function offsetTop (element) {
  let top = element.offsetTop
  while(element = element.offsetParent) {
    top += element.offsetTop
  }
  return top
}

/**
 * Crée un élément HTML
 *
 * @param {string} tagName
 * @param {object} attributes
 * @return HTMLElement
 */
export function createElement (tagName, attributes = {}) {
  const e = document.createElement(tagName)
  for (const k of Object.keys(attributes)) {
    e.setAttribute(k, attributes[k])
  }
  return e
}

/**
 * Transform une chaine en élément DOM
 * @param {string} str
 * @return {DocumentFragment}
 */
export function strToDom(str) {
  return document.createRange().createContextualFragment(str).firstChild
}
