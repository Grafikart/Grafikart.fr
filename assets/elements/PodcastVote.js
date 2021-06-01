import { jsonFetch } from '/functions/api.js'
import { isAuthenticated } from '/functions/auth.js'
import confetti from 'canvas-confetti'
import { windowHeight, windowWidth } from '/functions/window.js'

export class PodcastVote extends HTMLElement {
  connectedCallback () {
    this.voted = this.getAttribute('aria-selected') !== null
    this.$button = this.querySelector('span')
    this.$count = this.querySelector('strong')
    this.endpoint = this.dataset.endpoint
    if (!isAuthenticated()) {
      return
    }
    this.addEventListener('click', this.onClick.bind(this))
  }

  async onClick () {
    this.$button.innerHTML = '<spinning-dots style="width: 14px;"></spinning-dots>'
    const { votesCount } = await jsonFetch(this.endpoint, { methods: 'POST' })
    this.$count.innerText = votesCount
    this.voted = !this.voted
    this.updateUI()
  }

  updateUI () {
    if (this.voted) {
      this.$button.innerText = 'votes--'
      this.setAttribute('aria-selected', 'true')
      const rect = this.getBoundingClientRect()
      const y = (rect.top + rect.height) / windowHeight()
      const x = (rect.left + rect.width / 2) / windowWidth()
      confetti({
        particleCount: 25,
        zIndex: 3000,
        spread: 30,
        startVelocity: 20,
        gravity: 0.5,
        ticks: 100,
        disableForReducedMotion: true,
        origin: { y, x }
      })
    } else {
      this.$button.innerText = 'votes++'
      this.removeAttribute('aria-selected')
    }
  }
}
