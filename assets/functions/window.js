/**
 * Renvoie la hauteur de la fenêtre
 *
 * @return {number}
 */
export function windowHeight () {
  return window.innerHeight || document.documentElement.clientHeight || document.body.clientHeight
}
