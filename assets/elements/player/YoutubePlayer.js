import { playerStyle } from './PlayerStyle.js'

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

    // Structure HTML
    let poster = this.getAttribute('poster')
    poster =
      poster === null
        ? ''
        : `<button class="poster">
      <img src="${poster}" alt="">
      <svg class="play" fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 46 46"><path d="M23 0C10.32 0 0 10.32 0 23s10.32 23 23 23 23-10.32 23-23S35.68 0 23 0zm8.55 23.83l-12 8A1 1 0 0118 31V15a1 1 0 011.55-.83l12 8a1 1 0 010 1.66z"/></svg>
      <div class="title">Voir la vidéo <em>(${this.getAttribute('duration')})</em></div>
    </button>`
    this.root.innerHTML = `
      <style>${playerStyle}</style>
      <div class="ratio">
        <div class="player"></div>
        ${poster}
        <svg viewBox="0 0 16 9" xmlns="http://www.w3.org/2000/svg" class="ratio-svg">
          <rect width="16" height="9" fill="transparent"/>
        </svg>
      </div>`

    // Evènements
    if (poster !== '') {
      const onClick = () => {
        this.startPlay()
        this.removeEventListener('click', onClick)
      }
      this.addEventListener('click', onClick)
      if (window.location.hash === '#autoplay' && !this.getAttribute('autoplay')) {
        onClick()
      }
    }
  }

  /**
   * Démarre la lecture de la vidéo pour la première fois
   */
  startPlay () {
    this.root.querySelector('.poster').setAttribute('aria-hidden', 'true')
    this.setAttribute('autoplay', 'autoplay')
    this.removeAttribute('poster')
    this.loadPlayer(this.getAttribute('video'))
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
        rel: 0,
        start: this.getAttribute('start')
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
    switch (event.data) {
      case YT.PlayerState.PLAYING:
        this.startTimer()
        this.dispatchEvent(new Event('play', { bubbles: true }))
        break
      case YT.PlayerState.ENDED:
        this.stopTimer()
        this.dispatchEvent(new Event('ended'))
        break
      case YT.PlayerState.PAUSED:
        this.stopTimer()
        this.dispatchEvent(new Event('pause'))
        break
    }
  }

  /**
   * @param {YT.PlayerEvent} event
   */
  onYoutubePlayerReady () {
    this.startTimer()
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
    this.dispatchEvent(new Event('timeupdate'))
    this.timer = window.setInterval(() => this.dispatchEvent(new Event('timeupdate')), 1000)
  }

  pause () {
    this.player.pauseVideo()
  }

  play () {
    this.player.playVideo()
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

  /**
   * Définit la position de lecture
   *
   * @param {number} t
   */
  set currentTime (t) {
    if (this.player) {
      this.player.seekTo(t)
    } else {
      this.setAttribute('start', t.toString())
      this.startPlay()
    }
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
