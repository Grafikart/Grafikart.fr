// On mémorise si la page précédente avait la vague
import {offsetTop} from '../functions/dom'
import {debounce} from '../functions/timers'

let previousPageHadWaves = false

/**
 * Custom element pour générer les vagues sous le header
 *
 * @property {ShadowRoot} root
 * @property {HTMLElement|null} target
 * @property {HTMLElement} container
 * @property {HTMLElement} waves
 * @property {string} position
 */
export default class Waves extends global.HTMLElement {
  constructor () {
    super()
    this.root = this.attachShadow({ mode: 'open' })
    this.matchTarget = this.matchTarget.bind(this)
    this.onResize = debounce(this.onResize.bind(this), 500)
  }

  connectedCallback () {
    const className = previousPageHadWaves === true ? 'no-animation' : ''
    const target = document.querySelector(this.getAttribute('target'))
    const image = this.backgroundImage()
    const opacity = image ? '.9' : '1';
    previousPageHadWaves = true
    document.querySelector('.header').classList.add('is-inversed')
    this.target = target ? document.querySelector(this.getAttribute('target')) : null
    this.position = this.getAttribute('position') || 'center';
    this.root.innerHTML = `
      <style>
      img {
        position: absolute;
        width: 100%;
        height: 100%;
        z-index: 1;
        top: 0;
        left: 0;
        bottom: 0;
        right: 0;
        object-fit: cover;
        opacity: 0;
        transition: .3s;
      }
      .waves-container {
        opacity: 1!important;
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        width: 100%;
        overflow: hidden;
        z-index: -1;
        height: 0;
        box-sizing: content-box;
        padding-bottom: var(--wave-height, 235px);
        animation: containerIn .4s;
      }
      .waves-container.no-animation * {
        animation: none!important;
      }
      .waves-background {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        width: 100%;
        height: 100%;
        z-index: 2;
        background: linear-gradient(to bottom, var(--contrast), var(--contrast));
        opacity: ${opacity};
      }
      .waves {
        position: absolute;
        left: 50%;
        right: 0;
        z-index: 3;
        bottom: 0;
        width: 100vw;
        height: auto;
        min-width: 1440px;
        transform: translateX(-50%);
        max-height: var(--wave-height, 235px);
      }
      .waves path {
        animation: waveIn .7s both;
      }
      .waves path:last-child {
        animation: none;
      }
      @keyframes waveIn {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0px);
        }
      }
      @keyframes containerIn {
        from {
            opacity: 0;
            transform: translateY(-30px);
        }
        to {
            opacity: 1;
            transform: translateY(0px);
        }
      }
      </style>
      <div class="waves-container ${className}">
        <div class="waves-background"></div>
        ${image}
        <svg class="waves" xmlns="http://www.w3.org/2000/svg" style="isolation:isolate" preserveAspectRatio="none" viewBox="5978 129.24 1440 259.76">
          <defs>
            <clipPath id="a">
              <path d="M5978 129.24h1440V389H5978z"/>
            </clipPath>
          </defs>
          <g fill="#FFF" clip-path="url(#a)">
            <path style="animation-delay: .2s" d="M5978 153.77c166.44 0 358.45 11.66 755.24 138.08 381.36 121.5 562.3 105.94 684.76 75.81 0-15.48-.02-54.72-.1-155.11-137.43 39.67-283.82 106.09-717.65 27.58-407.86-73.8-571.8-93.89-721.75-93.89l-.5 7.53z" fill-opacity=".1"/>
            <path style="animation-delay: .4s" d="M5978 153.77c166.44 0 358.45 11.66 755.24 138.08 381.36 121.5 562.3 105.94 684.76 75.81l-.04-53.2-1.54.37c-122.36 30.1-294.49 72.46-680.18-34.53C6334.99 169 6181.93 151.26 5978 151.26v2.51z" fill-opacity=".1"/>
            <path d="M7418 367.66V389H5978V153.77c166.44 0 358.45 11.66 755.24 138.08C6965.46 365.84 7123.37 389 7239 389c74.27 0 131.1-9.56 178.99-21.34q0-70.8 0 0z" style="fill: var(--background);"/>
          </g>
        </svg>
    </div>
    `
    this.container = this.root.querySelector('.waves-container')
    this.waves = this.root.querySelector('.waves')
    if (image) {
      this.root.querySelector('img').addEventListener('load', function (e) {
        e.currentTarget.style.opacity = 1
      })
    }
    window.requestAnimationFrame(this.matchTarget)
    window.addEventListener('resize', this.onResize)
  }

  disconnectedCallback () {
    window.removeEventListener('resize', this.onResize)
  }

  /**
   * @return {string|null}
   */
  backgroundImage () {
    if (this.getAttribute('background')) {
      return `<img src="${this.getAttribute('background')}" alt=""/>`
    }
    return null
  }

  /**
   * Positionne la vague pour qu'elle arrive au milieu de l'élément qui est la cible
   */
  matchTarget () {
    if (this.target === null) {
      return
    }
    let top = offsetTop(this.target)
    let height = this.target.offsetHeight
    if (this.position === 'center') {
      top = top + height / 2 - 117
    } else if (this.position === 'bottom') {
      top = top + height
      this.container.style.boxSizing = 'border-box'
    }
    this.container.style.height = `${top}px`
  }

  onResize () {
    this.matchTarget()
  }

}

global.customElements.define('waves-shape', Waves)
