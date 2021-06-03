import { jsonFetch } from '/functions/api.js'
import { isAuthenticated } from '/functions/auth.js'
import confetti from 'canvas-confetti'
import { windowHeight, windowWidth } from '/functions/window.js'

export class PodcastVote extends HTMLElement {
  connectedCallback () {
    this.voted = this.getAttribute('aria-selected') !== null
    this.$button = this.querySelector('button')
    this.$count = this.querySelector('strong')
    this.endpoint = this.dataset.endpoint
    if (this.$button.getAttribute('disabled') === '' || !isAuthenticated()) {
      return
    }
    this.style.setProperty('cursor', 'pointer')
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
      // La personne a voté
      this.$button.innerText = 'votes--'
      this.$button.classList.add('btn-primary')
      this.$button.classList.remove('btn-secondary')
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
      // La personne peut voter
      this.$button.innerText = 'votes++'
      this.$button.classList.remove('btn-primary')
      this.$button.classList.add('btn-secondary')
      this.removeAttribute('aria-selected')
    }
  }
}
