import { jsonFetch } from '/functions/api.js'
import { isAuthenticated } from '/functions/auth.js'

const TIME_FOR_TRACKING = 10 // Nombre de secondes consécutives avant de considérer un visionnage

/**
 * @property {HTMLVideoElement} video
 * @property {number} timeBeforeTracking
 * @property {number} lastTickTime
 * @property {contentId} string
 */
export class ProgressTracker extends HTMLElement {

  constructor () {
    super()
    this.onProgress = this.onProgress.bind(this)
    this.timeBeforeTracking = TIME_FOR_TRACKING
    this.lastTickTime = 0
    this.contentId = this.getAttribute('contentId')
  }

  connectedCallback () {
    this.video = this.firstElementChild
    if (!this.video || !isAuthenticated()) {
      return null
    }
    this.video.addEventListener('timeupdate', this.onProgress)
  }

  async onProgress () {
    if (this.lastTickTime === null || !this.video.duration) {
      this.lastTickTime = this.video.currentTime
      return
    }

    const timeSinceLastTick = this.video.currentTime - this.lastTickTime
    if (timeSinceLastTick < 0 || timeSinceLastTick > 5) {
      this.lastTickTime = 0
      this.timeBeforeTracking = TIME_FOR_TRACKING
      return
    }

    this.timeBeforeTracking -= timeSinceLastTick
    this.lastTickTime = this.video.currentTime
    if (this.timeBeforeTracking < 0) {
      this.timeBeforeTracking = TIME_FOR_TRACKING
      const progression = Math.round(1000 * this.video.currentTime / this.video.duration)
      try {
        await jsonFetch(`/api/progress/${this.contentId}/${progression}`, {method: 'POST'})
      } catch (e) {
        console.error(`Impossible d'enregistrer la progression`)
      }
    }
  }

  disconnectedCallback () {
    this.video.removeEventListener('timeupdate', this.onProgress)
  }

}
