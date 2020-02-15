// On mémorise si la page précédente avait la vague
let previousPageHadWaves = false
let timer = null

/**
 * Custom element pour générer les vagues sous le header
 *
 * @property {ShadowRoot} root
 */
export default class Waves extends global.HTMLElement {
  constructor () {
    super()
    this.root = this.attachShadow({ mode: 'open' })
  }

  connectedCallback () {
    if (timer) {
      window.clearTimeout(timer)
    }
    const className = previousPageHadWaves === true ? 'no-animation' : ''
    previousPageHadWaves = true
    this.root.innerHTML = `
      <style>
      .waves-container {
        opacity: 1!important;
        overflow: hidden;
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        width: 100%;
        height: 100%;
        z-index: -1;
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
        background:linear-gradient(to bottom, var(--contrast), var(--contrast)) top center no-repeat;
        background-size: 100% calc(100% - 522px);
        animation: opacityIn .7s both;
      }
      .waves {
        position: absolute;
        left: 50%;
        right: 0;
        bottom: 0;
        width: 100vw;
        min-width: 1440px;
        transform: translateX(-50%);
      }
      .waves path {
        animation: waveIn .7s both;
      }
      @keyframes waveIn {
        from {
            opacity: 0;
            transform: translateY(-60px);
        }
        to {
            opacity: 1;
            transform: translateY(0px);
        }
      }
      @keyframes opacityIn {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
      }
      </style>
      <div class="waves-container ${className}" style="opacity: 0">
            <div class="waves-background"></div>
          <svg class="waves" width="1440" height="522" viewBox="0 0 1440 522" fill="none" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
          <path style="animation-delay: 0s; fill:var(--contrast)"  d="M0 287.5V-135.5H1440C1440 -135.5 1440.5 430 1440.5 500.5C1318 530.5 1137 546 755.5 425C358.577 299.108 166.5 287.5 0 287.5Z"/>
          <path style="animation-delay: .2s" d="M0 287.5C166.5 287.5 358.577 299.108 755.5 425C1137 546 1318 530.5 1440.5 500.5C1440.5 487.578 1440.48 469.337 1440.46 447.511L1438.92 447.888C1316.52 477.867 1144.33 520.04 758.5 413.5C357.113 302.665 204 285 0 285V287.5Z" fill="white" fill-opacity="0.2"/>
          <path style="animation-delay: .4s" d="M0.5 280V64.5H1440.5V346C1303 385.5 1156.6 451.701 722.5 373.5C314.5 300 150.5 280 0.5 280Z" fill="url(#paint0_linear)" fill-opacity="0.1"/>
          <defs>
          <linearGradient id="paint0_linear" x1="720.5" y1="29" x2="720.5" y2="409.912" gradientUnits="userSpaceOnUse">
            <stop stop-opacity="0"/>
            <stop offset="1"/>
          </linearGradient>
          </defs>
          </svg>
    </div>
    `
  }

  disconnectedCallback () {
    timer = window.setTimeout(function () {
      timer = null
      previousPageHadWaves = false
    }, 700)
  }
}

global.customElements.define('waves-shape', Waves)
