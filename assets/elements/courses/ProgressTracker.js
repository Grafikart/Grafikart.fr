import { jsonFetch } from '/functions/api.js'
import { isAuthenticated } from '/functions/auth.js'
import confetti from 'canvas-confetti'
import { wait } from '/functions/timers.js'
import { strToDom } from '/functions/dom.js'

const TIME_FOR_TRACKING = 10 // Nombre de secondes consécutives avant de considérer un visionnage

/**
 * @property {HTMLVideoElement} video
 * @property {number} timeBeforeTracking
 * @property {number} lastTickTime
 * @property {contentId} string
 */
export class ProgressTracker extends HTMLElement {
  static get observedAttributes () {
    return ['progress']
  }

  constructor () {
    super()
    this.onProgress = this.onProgress.bind(this)
    this.onEnd = this.onEnd.bind(this)
    this.onHashChange = this.onHashChange.bind(this)
    this.timeBeforeTracking = TIME_FOR_TRACKING
    this.lastTickTime = 0
    this.contentId = this.getAttribute('contentId')
  }

  connectedCallback () {
    this.video = this.firstElementChild
    if (!this.video) {
      return null
    }
    window.addEventListener('hashchange', this.onHashChange)
    if (!isAuthenticated()) {
      return null
    }
    this.video.addEventListener('timeupdate', this.onProgress)
    this.video.addEventListener('ended', this.onEnd)
  }

  attributeChangedCallback (name, oldValue, newValue) {
    if (name === 'progress' && newValue) {
      this.firstElementChild.setAttribute(
        'start',
        Math.floor(parseInt(this.getAttribute('duration'), 10) * parseFloat(newValue))
      )
    }
  }

  async onEnd () {
    await wait(300)
    const { message } = await jsonFetch(`/api/progress/${this.contentId}/1000`, { method: 'POST' }).catch(console.error)
    document.body.appendChild(strToDom(message))
    confetti({
      particleCount: 100,
      zIndex: 3000,
      spread: 70,
      origin: { y: 0.6 }
    })
  }

  async onProgress () {
    if (this.lastTickTime === null || !this.video.duration) {
      this.lastTickTime = this.video.currentTime
      return
    }

    const timeSinceLastTick = this.video.currentTime - this.lastTickTime
    if (timeSinceLastTick < 0 || timeSinceLastTick > 5) {
      this.lastTickTime = this.video.currentTime
      this.timeBeforeTracking = TIME_FOR_TRACKING
      return
    }

    this.timeBeforeTracking -= timeSinceLastTick
    this.lastTickTime = this.video.currentTime
    if (this.timeBeforeTracking < 0) {
      this.timeBeforeTracking = TIME_FOR_TRACKING
      const progression = Math.round((1000 * this.video.currentTime) / this.video.duration)
      try {
        await jsonFetch(`/api/progress/${this.contentId}/${progression}`, { method: 'POST' })
      } catch (e) {
        console.error(`Impossible d'enregistrer la progression`)
      }
    }
  }

  onHashChange () {
    if (window.location.hash.startsWith('#t')) {
      const t = parseInt(window.location.hash.replace('#t', ''), 10)
      if (Number.isNaN(t)) {
        return null
      }
      this.video.currentTime = t
      this.video.scrollIntoView({
        behavior: 'smooth',
        block: 'center',
        inline: 'center'
      })
    }
  }

  disconnectedCallback () {
    this.video.removeEventListener('timeupdate', this.onProgress)
    window.removeEventListener('hashchange', this.onHashChange)
  }
}
