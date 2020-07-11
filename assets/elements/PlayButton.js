/**
 * Element permettant de représenter un bouton de lecture avec progression.
 *
 * ## Attributes
 *
 * - progress, Nombre représentant la progression entre 0 et 100
 * - playing, La vidéo est en cours de lecture
 * - video, Selecteur de la vidéo à connecter à ce bouton
 *
 * @property {ShadowRoot} root
 * @property {HTMLButtonElement} button
 * @property {SVGCircleElement} circle
 */
export class PlayButton extends HTMLElement {
  static get observedAttributes () {
    return ['playing', 'progress', 'video']
  }

  constructor () {
    super()
    this.root = this.attachShadow({ mode: 'open' })
    this.root.innerHTML = `
      ${this.buildStyles()}
      <button>
        <svg fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32">
          <circle cx="16" cy="16" r="15" fill="none" stroke-width="1" stroke="currentColor" />
          <path d="M20.3 12.3L14 18.58l-2.3-2.3a1 1 0 00-1.4 1.42l3 3a1 1 0 001.4 0l7-7a1 1 0 00-1.4-1.42z" fill="#fff"/>
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
        this.circle.style.strokeDashoffset = `${94 - (94 * progress) / 100}px`
      }
      if (progress === 100) {
        this.root.host.classList.add('is-checked')
      } else {
        this.root.host.classList.remove('is-checked')
      }
    }
    if (name === 'video' && newValue !== null) {
      const video = document.querySelector(newValue)
      if (video !== null) {
        this.attachVideo(video)
      }
    }
  }

  /**
   * Build the style
   */
  buildStyles () {
    return `<style>
      button {
        cursor: inherit;
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
        opacity: 1;
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
      button path {
        opacity: 0;
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
      :host-context(.is-checked) button circle {
        opacity: 0;
      }
      :host-context(.is-checked) button svg {
        transform: rotate(0deg);
      }
      :host-context(.is-checked) button {
        background: var(--green);
      }
      :host-context(.is-checked) button path {
        opacity: 1;
      }
      :host-context(.is-checked) button::before {
        display: none;
      }
    </style>`
  }

  /**
   * Attache le bouton a un player
   *
   * @param {YoutubePlayer|HTMLVideoElement} video
   */
  attachVideo (video) {
    this.setAttribute('progress', 0)
    const onTimeUpdate = () => {
      this.setAttribute('progress', ((100 * video.currentTime) / video.duration).toString())
    }
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
