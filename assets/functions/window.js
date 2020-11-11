/**
 * Renvoie la hauteur de la fenêtre
 *
 * @return {number}
 */
export function windowHeight () {
  return window.innerHeight || document.documentElement.clientHeight || document.body.clientHeight
}

const uuid = new Date().getTime().toString()
localStorage.setItem('windowId', uuid)
window.addEventListener('focus', function () {
  localStorage.setItem('windowId', uuid)
})
/**
 * Renvoie true si la fenêtre est active ou si elle a été la dernière fenêtre active
 */
export function isActiveWindow () {
  return uuid === localStorage.getItem('windowId')
}
