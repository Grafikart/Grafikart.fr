/**
 * Crée une popup centré sur l'écran utilisateur
 * @param {string} url
 * @param {string} title
 * @param {number} w
 * @param {number} h
 */
function popupCenter (url, title, w, h) {
  /* global screen */
  const dualScreenLeft = window.screenLeft !== undefined ? window.screenLeft : screen.left
  const dualScreenTop = window.screenTop !== undefined ? window.screenTop : screen.top

  const width = window.innerWidth
    ? window.innerWidth
    : document.documentElement.clientWidth
    ? document.documentElement.clientWidth
    : screen.width
  const height = window.innerHeight
    ? window.innerHeight
    : document.documentElement.clientHeight
    ? document.documentElement.clientHeight
    : screen.height

  const left = width / 2 - w / 2 + dualScreenLeft
  const top = height / 2 - h / 2 + dualScreenTop
  const newWindow = window.open(url, title, `scrollbars=yes, width=${w}, height=${h}, top=${top}, left=${left}`)

  if (window.focus) {
    newWindow.focus()
  }
}

/**
 * Element permettant un partage sur les réseaux sociaux en créant une popup
 */
export default class Sharer extends HTMLAnchorElement {
  constructor () {
    super()
    this.addEventListener('click', e => {
      e.preventDefault()
      e.stopPropagation()
      popupCenter(`${this.getAttribute('href')}&text=${encodeURIComponent(document.title)}`, 'Partager', 670, 340)
    })
  }
}
