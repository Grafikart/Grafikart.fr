import { offsetTop } from '/functions/dom.js'
import { debounce } from '/functions/timers.js'

// On mémorise si la page précédente avait la vague
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
export class Waves extends HTMLElement {
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
    previousPageHadWaves = true
    document.querySelector('.header').classList.add('is-inversed')
    this.target = target ? document.querySelector(this.getAttribute('target')) : null
    this.position = this.getAttribute('position') || 'center'
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
        transition: opacity .3s;
        animation: backgroundIn .4s;
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
      /* Cette animation ne sert à rien mais permet d'empécher un bug de clipping (MERCI CHROME !) */
      @keyframes backgroundIn {
        from {
            transform: scaleY(1.1);
        }
        to {
            transform: scaleY(1);
        }
      }
      </style>
      <div class="waves-container ${className}">
        <div class="waves-background"></div>
        ${image}
        <svg xmlns="http://www.w3.org/2000/svg" class="waves" viewBox="0 0 1440 250"  style="isolation:isolate" preserveAspectRatio="none">
          <path fill="#FFF" style="animation-delay: .2s"  fill-opacity=".1" d="M0 24c166 0 358 11 755 133 382 116 563 101 685 72V80c-138 38-284 102-718 27C314 36 150 16 1 16l-1 8z"/>
          <path fill="#FFF" style="animation-delay: .4s" fill-opacity=".1" d="M0 24c166 0 358 11 755 133 382 116 563 101 685 72v-51l-2 1c-122 29-294 69-680-34C357 38 204 21 0 21v3z"/>
          <path style="fill: var(--background);" d="M1440 229v21H0V24c166 0 358 11 755 133 232 71 390 93 506 93 74 0 131-9 179-21 0-45 0-45 0 0z"/>
        </svg>
    </div>
    `
    this.container = this.root.querySelector('.waves-container')
    this.waves = this.root.querySelector('.waves')
    const background = this.root.querySelector('.waves-background')
    if (image !== '') {
      this.root.querySelector('img').addEventListener('load', () => {
        background.style.opacity = 0.96
      })
    }
    window.requestAnimationFrame(this.matchTarget)
    window.addEventListener('resize', this.onResize)
  }

  disconnectedCallback () {
    window.removeEventListener('resize', this.onResize)
  }

  /**
   * @return {string}
   */
  backgroundImage () {
    if (this.getAttribute('background')) {
      return `<img src="${this.getAttribute('background')}" alt=""/>`
    }
    return ''
  }

  /**
   * Positionne la vague pour qu'elle arrive au milieu de l'élément qui est la cible
   */
  matchTarget () {
    if (this.target === null) {
      return
    }
    let top = offsetTop(this.target)
    const height = this.target.offsetHeight
    if (this.position === 'center') {
      top = top + height / 2 - 117
    } else if (this.position === 'bottom') {
      top = top + height
    } else if (this.position === 'bottomWave') {
      top = top + height
      this.container.style.boxSizing = 'border-box'
    }
    this.container.style.height = `${top}px`
  }

  onResize () {
    this.matchTarget()
  }
}
