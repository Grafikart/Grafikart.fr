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
