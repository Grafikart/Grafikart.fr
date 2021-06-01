/**
 * Renvoie la hauteur de la fenêtre
 *
 * @return {number}
 */
export function windowHeight () {
  return window.innerHeight || document.documentElement.clientHeight || document.body.clientHeight
}

/**
 * Renvoie la largeur de la fenêtre
 *
 * @return {number}
 */
export function windowWidth () {
  return window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth
}

const uuid = new Date().getTime().toString()
if (localStorage) {
  localStorage.setItem('windowId', uuid)
  window.addEventListener('focus', function () {
    localStorage.setItem('windowId', uuid)
  })
}

/**
 * Renvoie true si la fenêtre est active ou si elle a été la dernière fenêtre active
 */
export function isActiveWindow () {
  if (localStorage) {
    return uuid === localStorage.getItem('windowId')
  } else {
    return true
  }
}
