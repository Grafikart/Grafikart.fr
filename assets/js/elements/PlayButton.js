/**
 * Element permettant de représenter un bouton de lecture avec progression.
 *
 * @property {ShadowRoot} root
 * @property {HTMLButtonElement} button
 * @property {SVGCircleElement} circle
 */
export default class PlayButton extends HTMLElement {
  static get observedAttributes () {
    return ['playing', 'progress']
  }

  constructor () {
    super()
    this.root = this.attachShadow({mode: 'open'})
  }

  connectedCallback () {
    this.root.innerHTML = `
      ${this.buildStyles()}
      <button>
        <svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg">
          <circle cx="16" cy="16" r="15" stroke="currentColor" stroke-width="1" fill="none"/>
        </svg>
      </button>
    `
    this.button = this.root.querySelector('button')
    this.circle = this.root.querySelector('circle')
  }

  attributeChangedCallback (name, oldValue, newValue) {
    if (name === 'playing' && newValue === null) {
      this.root.host.classList.remove('is-playing')
    } else if (name === 'playing') {
      this.root.host.classList.add('is-playing')
    }
    if (name === 'progress') {
      const progress = newValue ? parseInt(newValue, 10) : 0
      if (this.circle) {
        this.circle.style.strokeDashoffset = (94 - 94 * progress / 100) + 'px'
      }
    }
  }

  /**
   * Build the style
   */
  buildStyles () {
    return `<style>
      button {
        outline: none;
        position: relative;
        border: none;
        width: 32px;
        height: 32px;
        border-radius: 32px;
        flex: none;
        background: var(--play);
        margin-right: 1.5em;
        transition: .3s;
        color: var(--contrast);
      }
      button svg {
        opacity: 0;
        position: absolute;
        top: 0;
        left: 0;
        width: 32px;
        height: 32px;
        transform: rotate(-90deg);
        transition: opacity .3s;
      }
      button circle {
        stroke-dasharray: 94px;
        stroke-dashoffset: 94px;
        transition: stroke-dashoffset .1s;
      }
      button::before {
        position: absolute;
        top: 10px;
        left: 13px;
        content: '';
        height: 0;
        border-left: 9px solid var(--color);
        border-top: 6px solid transparent;
        border-bottom: 6px solid transparent;
      }
      :host-context(.is-playing) button {
        background-color: #4869EE33 !important;
      }
      :host-context(.is-playing) button::before,
      :host-context(.is-playing) button::after {
        content: '';
        position: absolute;
        border: none;
        top: 10px;
        left: 18px;
        background: var(--contrast);
        width: 4px;
        height: 12px;
      }
      :host-context(.is-playing) button::before {
        left: 10px;
      }
      :host-context(.is-playing) button svg {
        opacity: 1;
      }
    </style>`
  }

  /**
   * Attache le bouton a un player
   *
   * @param {YoutubePlayer|HTMLVideoElement} video
   */
  attachVideo (video) {
    const onTimeUpdate = () => this.setAttribute('progress', (100 * video.currentTime / video.duration).toString())
    const onPlay = () => this.setAttribute('playing', 'playing')
    const onEnded = () => this.removeAttribute('playing')
    video.addEventListener('timeupdate', onTimeUpdate)
    video.addEventListener('play', onPlay)
    video.addEventListener('ended', onEnded)
    this.detachVideo = () => {
      video.removeEventListener('timeupdate', onTimeUpdate)
      video.removeEventListener('play', onPlay)
      video.removeEventListener('ended', onEnded)
      this.removeAttribute('playing')
      this.detachVideo = function () {}
    }
  }

  /**
   * Détache le lecteur (supprime les listeners) du bouton de lecture.
   */
  detachVideo () {}
}
