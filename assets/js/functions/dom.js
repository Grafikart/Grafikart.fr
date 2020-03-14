import htm from 'htm'

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
 * @param {...HTMLElement|string} children
 * @return HTMLElement
 */
export function createElement (tagName, attributes = {}, ...children) {
  // On construit l'élément
  const e = document.createElement(tagName)

  // On lui associe les bons attributs
  for (const k of Object.keys(attributes || {})) {
    if (k.startsWith('on')) {
      e.addEventListener(k.substr(2).toLowerCase(), attributes[k])
    } else {
      e.setAttribute(k, attributes[k])
    }
  }

  // On aplatit les enfants
  children = children.reduce(function (acc, child) {
    return Array.isArray(child) ? [...acc, ...child] : [...acc, child]
  }, [])

  // On ajoute les enfants à l'élément
  for (const child of children) {
    if (typeof child === 'string' || typeof child === 'number') {
      e.appendChild(document.createTextNode(child))
    } else if (child instanceof HTMLElement) {
      e.appendChild(child)
    } else {
      console.error("Impossible d'ajouter l'élément", child, children)
    }
  }
  return e
}

/**
 * CreateElement version Tagged templates
 * @type {(strings: TemplateStringsArray, ...values: any[]) => (HTMLElement[] | HTMLElement)}
 */
export const html = htm.bind(createElement)

/**
 * Transform une chaine en élément DOM
 * @param {string} str
 * @return {DocumentFragment}
 */
export function strToDom(str) {
  return document.createRange().createContextualFragment(str).firstChild
}

/**
 *
 * @param {HTMLElement|Document|Node} element
 * @param {string} selector
 * @return {null|*}
 */
export function closest (element, selector) {
    for ( ; element && element !== document; element = element.parentNode ) {
      if ( element.matches( selector ) ) return element;
    }
    return null;
}
