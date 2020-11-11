import confetti from 'canvas-confetti'
import { windowHeight } from '/functions/window.js'

export class Confetti extends HTMLElement {
  connectedCallback () {
    const rect = this.getBoundingClientRect()
    const y = (rect.top + rect.height / 2) / windowHeight()
    confetti({
      particleCount: 100,
      zIndex: 3000,
      spread: 90,
      disableForReducedMotion: true,
      colors: ['#121c42', '#effbec', '#4869ee', '#f25353', '#41cf7c', '#feb32b'],
      origin: { y }
    })
  }
}
