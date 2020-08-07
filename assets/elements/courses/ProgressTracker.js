import { jsonFetch } from '/functions/api.js'
import { isAuthenticated } from '/functions/auth.js'
import { ModalDialog } from '@sb-elements/all'
import confetti from 'canvas-confetti'
import { wait } from '/functions/timers.js'

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
    this.onEnd = this.onEnd.bind(this)
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
    this.video.addEventListener('ended', this.onEnd)
  }

  async onEnd () {
    await wait(1000)
    const dialog = new ModalDialog()
    dialog.setAttribute('overlay-close', 'overlay-close')
    dialog.setAttribute('hidden', 'hidden')
    dialog.innerHTML = `<div class="modal-box">
      <header>Félicitations ! </header>
      <div class="text-center py2">
        <img src="/images/success.svg" alt="" style="max-width: 80%;">
      </div>
      <p>Bravo pour votre avancement plus que 10 chapitres à tenir</p>
      <footer class="text-center mt3">
        <button data-dismiss class="btn btn-block btn-primary">
          Voir le chapitre suivant
        </button>
      </footer>
      <button
        data-dismiss
        aria-label="Close"
        class="modal-close"
      >
      <svg class="icon icon-cross">
  <use xlink:href="/sprite.svg#cross"></use>
</svg>
</button>
    </div>`
    confetti({
      particleCount: 100,
      zIndex: 3000,
      spread: 70,
      origin: { y: 0.6 }
    });
    jsonFetch(`/api/progress/${this.contentId}/1000`, { method: 'POST' }).catch(console.error)
    document.body.appendChild(dialog)
    window.requestAnimationFrame(() => {
      dialog.removeAttribute('hidden')
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
      const progression = Math.round(1000 * this.video.currentTime / this.video.duration)
      try {
        await jsonFetch(`/api/progress/${this.contentId}/${progression}`, { method: 'POST' })
      } catch (e) {
        console.error(`Impossible d'enregistrer la progression`)
      }
    }
  }

  disconnectedCallback () {
    this.video.removeEventListener('timeupdate', this.onProgress)
  }

}
