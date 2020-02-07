/**
 * Element permettant de gérer la liste des derniers lives
 *
 * @property {boolean} isPlaying
 * @property {HTMLDivElement} videoContainer
 * @property {HTMLDivElement} liveList
 * @property {YoutubePlayer} player
 * @property {HTMLAnchorElement} currentLive
 */
import YoutubePlayer from './YoutubePlayer'

export default class RecapLiveElement extends HTMLElement {
  connectedCallback () {
    this.liveList = this.querySelector('.live-list')
    const lives = this.querySelectorAll('.live')
    lives.forEach((live) => {
      live.addEventListener('click', this.play.bind(this))
    })
  }

  /**
   * Lance la lecture d'une vidéo
   * @param {MouseEvent} e
   */
  play (e) {
    e.preventDefault()
    e.stopPropagation()
    const live = e.currentTarget
    const id = live.dataset.youtube
    if (live.classList.contains('is-playing')) {
      return
    }
    if (this.player === undefined) {
      this.player = new YoutubePlayer({autoplay: 1})
      this.liveList.insertAdjacentElement('beforebegin', this.player)
    }
    live.classList.add('is-playing')
    live.querySelector('play-button').attachVideo(this.player)
    this.player.setAttribute('video', id)
    if (this.currentLive) {
      this.currentLive.querySelector('play-button').detachVideo()
      this.currentLive.classList.remove('is-playing')
    }
    this.currentLive = live
    this.classList.add('has-player')
    live.scrollIntoView({block: 'center', behavior: 'smooth', inline: 'nearest'})
    this.player.scrollIntoView({block: 'start', behavior: 'smooth', inline: 'nearest'})
  }

  /**
   * Génère l'URL pour l'embed
   * @param {string} id
   */
  youtubeURL (id) {
    return `https://www.youtube-nocookie.com/embed/${id}?controls=1&autoplay=1&loop=0&showinfo=0&rel=0&hd=1`
  }
}
