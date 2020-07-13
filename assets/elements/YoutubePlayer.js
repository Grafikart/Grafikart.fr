/**
 * Instance de l'API youtube iframe
 * @type {null|YT}
 */
let YT = null

/**
 * Element représentant une video youtube `<youtube-player video="UEINCHBN">`.
 *
 * ## Attributes
 *
 * - video, ID de la vidéo Youtube
 * - poster, URL de la miniature
 * - autoplay
 * - playButton, ID du bouton play à connecter au player
 * - title, Titre à afficher sur le player
 *
 * @property {ShadowRoot} root
 * @property {?number} timer Timer permettant de suivre la progression de la lecture
 * @property {YT.Player} player
 */
export class YoutubePlayer extends HTMLElement {
  static get observedAttributes () {
    return ['video', 'button']
  }

  constructor (attributes = {}) {
    super()

    // Initialisation
    Object.keys(attributes).forEach(k => this.setAttribute(k, attributes[k]))
    this.root = this.attachShadow({ mode: 'open' })
    this.onYoutubePlayerStateChange = this.onYoutubePlayerStateChange.bind(this)
    this.onYoutubePlayerReady = this.onYoutubePlayerReady.bind(this)
    this.getAttribute('poster')

    // Structure HTML
    let poster = this.getAttribute('poster')
    poster =
      poster === null
        ? ''
        : `<div class="poster">
      <img src="${poster}">
      <svg class="play" fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 46 46"><path d="M23 0C10.32 0 0 10.32 0 23s10.32 23 23 23 23-10.32 23-23S35.68 0 23 0zm8.55 23.83l-12 8A1 1 0 0118 31V15a1 1 0 011.55-.83l12 8a1 1 0 010 1.66z"/></svg>
      <div class="title">${this.getAttribute('title')}</div>
    </div>`
    this.root.innerHTML = `
      ${this.buildStyles()}
      <div class="ratio">
        <div class="player"></div>
        ${poster}
      </div>`

    // Evènements
    if (poster !== '') {
      const onClick = () => {
        this.root.querySelector('.poster').setAttribute('aria-hidden', 'true')
        this.setAttribute('autoplay', 'autoplay')
        this.removeAttribute('poster')
        this.loadPlayer(this.getAttribute('video'))
        this.removeEventListener('click', onClick)
      }
      this.addEventListener('click', onClick)
    }
  }

  disconnectedCallback () {
    this.stopTimer()
  }

  async attributeChangedCallback (name, oldValue, newValue) {
    if (name === 'video' && newValue !== null && this.getAttribute('poster') === null) {
      this.loadPlayer(newValue)
    }
    if (name === 'button' && newValue !== null) {
      /** @var {PlayButton} button **/
      const button = document.querySelector(newValue)
      if (button !== null) {
        button.setAttribute('video', `#${this.id}`)
      }
    }
  }

  /**
   * @param {string} youtubeID
   * @return {Promise<void>}
   */
  async loadPlayer (youtubeID) {
    await loadYoutubeApi()
    if (this.player) {
      this.player.cueVideoById(this.getAttribute('video'))
      this.player.playVideo()
      return
    }
    this.player = new YT.Player(this.root.querySelector('.player'), {
      videoId: youtubeID,
      host: 'https://www.youtube-nocookie.com',
      playerVars: {
        autoplay: this.getAttribute('autoplay') ? 1 : 0,
        loop: 0,
        modestbranding: 1,
        controls: 1,
        showinfo: 0,
        rel: 0
      },
      events: {
        onStateChange: this.onYoutubePlayerStateChange,
        onReady: this.onYoutubePlayerReady
      }
    })
  }

  /**
   * @param {YT.OnStateChangeEvent} event
   */
  onYoutubePlayerStateChange (event) {
    if (event.data === YT.PlayerState.PLAYING) {
      this.startTimer()
      this.dispatchEvent(new global.Event('play'))
    } else if (event.data === YT.PlayerState.ENDED) {
      this.stopTimer()
      this.dispatchEvent(new global.Event('ended'))
    }
  }

  /**
   * @param {YT.PlayerEvent} event
   */
  onYoutubePlayerReady () {
    this.startTimer()
    this.dispatchEvent(new Event('play'))
  }

  /**
   * Génère le style associé au player
   * @returns {string}
   */
  buildStyles () {
    return `<style>
      :host {
        display: block;
      }
      .ratio {
        background-color:black;
        position: relative;
        padding-bottom: 56.25%;
      }
      .poster {
        cursor: pointer;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
      }
      .poster:hover .play {
        transform: scale(1.1)
      }
      .poster:hover::before {
        opacity: .8;
      }
      .title {
        color: #FFF;
        font-size: 22px;
        position: relative;
        text-align: center;
        z-index: 3;
        transition: .3s;
      }
      .play {
        position: relative;
        width: 48px;
        height: 48px;
        z-index: 3;
        fill: #FFF;
        margin-bottom: 8px;
        filter:  drop-shadow(0 1px 20px #121C4280);
        transition: .3s;
      }
      .poster::before {
        content:'';
        background: linear-gradient(to top, var(--color) 0%, var(--color-transparent) 100%);
        z-index: 2;
      }
      .poster,
      iframe,
      .poster::before,
      img {
        position: absolute;
        top:0;
        left: 0;
        bottom: 0;
        right: 0;
        width: 100%;
        height: 100%;
        transition: opacity .5s;
      }
      .poster[aria-hidden] {
        pointer-events: none;
        opacity: 0;
      }
    </style>`
  }

  stopTimer () {
    if (this.timer) {
      window.clearInterval(this.timer)
      this.timer = null
    }
  }

  startTimer () {
    if (this.timer) {
      return null
    }
    this.dispatchEvent(new global.Event('timeupdate'))
    this.timer = window.setInterval(() => this.dispatchEvent(new global.Event('timeupdate')), 1000)
  }

  /**
   * Durée de la vidéo
   * @return {number}
   */
  get duration () {
    return this.player ? this.player.getDuration() : null
  }

  /**
   * Position de la lecture
   * @return {number}
   */
  get currentTime () {
    return this.player ? this.player.getCurrentTime() : null
  }
}

/**
 * Charge l'API Youtube Player
 * @returns {Promise<YT>}
 */
async function loadYoutubeApi () {
  return new Promise(resolve => {
    if (YT) {
      resolve(YT)
    }
    const tag = document.createElement('script')
    tag.src = 'https://www.youtube.com/iframe_api'
    const firstScriptTag = document.getElementsByTagName('script')[0]
    firstScriptTag.parentNode.insertBefore(tag, firstScriptTag)
    window.onYouTubeIframeAPIReady = function () {
      YT = window.YT
      window.onYouTubeIframeAPIReady = undefined
      resolve(YT)
    }
  })
}
