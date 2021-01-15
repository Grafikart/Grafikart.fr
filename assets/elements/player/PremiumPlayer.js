import { playerStyle } from './PlayerStyle.js'
import { flash } from '/elements/Alert.js'

/**
 * Element représentant une video premium `<premium-player video="1333">`.
 *
 * ## Attributes
 *
 * - video, ID de la vidéo
 * - poster, URL de la miniature
 * - autoplay
 * - title, Titre à afficher sur le player
 *
 * @property {ShadowRoot} root
 * @property {HTMLVideoElement} video
 */
export class PremiumPlayer extends HTMLElement {
  constructor () {
    super()

    // Initialisation
    this.root = this.attachShadow({ mode: 'open' })

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

  /**
   * @param {string} url
   * @return {Promise<void>}
   */
  loadPlayer (url) {
    this.video = document.createElement('video')
    this.video.src = url
    this.video.controls = true
    this.video.autoplay = true
    this.video.currentTime = this.getAttribute('start') ? parseInt(this.getAttribute('start'), 10) : 0
    this.video.addEventListener('play', () => {
      this.dispatchEvent(new Event('play', { bubbles: true }))
    })
    ;['ended', 'pause', 'timeupdate'].forEach(eventName => {
      this.video.addEventListener(eventName, () => {
        this.dispatchEvent(new Event(eventName))
      })
    })
    this.video.addEventListener('error', () => {
      flash('Une erreur est survenue lors du chargement de la vidéo', 'error')
    })
    this.root.querySelector('.player').appendChild(this.video)
  }

  pause () {
    this.video.pause()
  }

  play () {
    this.video.play()
  }

  /**
   * Durée de la vidéo
   * @return {number}
   */
  get duration () {
    return this.video ? this.video.duration : null
  }

  /**
   * Position de la lecture
   * @return {number}
   */
  get currentTime () {
    return this.video ? this.video.currentTime : null
  }

  /**
   * Définit la position de lecture
   *
   * @param {number} t
   */
  set currentTime (t) {
    if (this.video) {
      this.video.currentTime = t
    } else {
      this.setAttribute('start', t.toString())
      this.startPlay()
    }
  }
}
