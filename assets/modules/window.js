/**
 * Crée une variable CSS --vh qui contient la hauteur réel de la fenêtre
 */
export function registerWindowHeightCSS () {
  document.addEventListener('DOMContentLoaded', () => {
    let windowHeight = window.innerHeight
    document.documentElement.style.setProperty('--vh', `${window.innerHeight  * 0.01}px`)
    document.documentElement.style.setProperty('--windowHeight', `${window.innerHeight}px`)

    window.addEventListener('resize', () => {
      if (windowHeight === window.innerHeight) {
        return
      }
      windowHeight = window.innerHeight
      document.documentElement.style.setProperty('--vh', `${window.innerHeight * 0.01}px`)
      document.documentElement.style.setProperty('--windowHeight', `${window.innerHeight}px`)
    })
  })
}
