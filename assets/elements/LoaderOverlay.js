import SpinningDots from '@grafikart/spinning-dots-element'

/**
 * Crée un loader qui se place au dessus de l'élément courant
 */
export default class LoaderOverlay extends HTMLElement {
  constructor () {
    super()
    this.style.position = 'absolute'
    this.style.left = '0'
    this.style.right = '0'
    this.style.bottom = '0'
    this.style.top = '0'
    this.style.margin = '0'
    this.style.padding = '0'
    this.style.zIndex = '10'
    this.style.display = 'flex'
    this.style.alignItems = 'center'
    this.style.justifyContent = 'center'
    this.style.transition = 'opacity .3s'
    this.style.background = 'rgba(255,255,255,.8)'
  }

  connectedCallback () {
    // On crée le loader
    const loader = new SpinningDots()
    loader.style.width = '20px'
    loader.style.height = '20px'

    // On ajoute le loader à notre élément
    this.appendChild(loader)
  }

  /**
   * Masque le loader avec un effet d'animation
   */
  hide () {
    this.style.opacity = 0
  }
}

customElements.define('loader-overlay', LoaderOverlay)
