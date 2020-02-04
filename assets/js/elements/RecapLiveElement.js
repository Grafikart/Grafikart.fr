/**
 * Element permettant de gérer la liste des derniers lives
 *
 * @property {boolean} isPlaying
 * @property {HTMLDivElement} videoContainer
 * @property {HTMLDivElement} liveList
 * @property {HTMLIFrameElement} iframe
 */
export default class RecapLiveElement extends HTMLElement {
  connectedCallback () {
    this.isPlaying = false
    this.videoContainer = this.querySelector('.js-video')
    this.liveList = this.querySelector('.js-videos')
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
    const live = e.currentTarget
    const id = live.dataset.youtube
    if (this.iframe) {
      this.iframe.setAttribute('src', this.youtubeURL(id))
    } else {
      this.videoContainer.innerHTML = `<iframe
            src="${this.youtubeURL(id)}"
            allowfullscreen></iframe>`
      this.iframe = this.videoContainer.querySelector('iframe')
    }
    this.classList.add('is-playing')
    this.classList.add('card')
    if (this.currentLive) {
      this.currentLive.classList.remove('is-playing')
    }
    this.currentLive = live
    this.isPlaying = true
    live.classList.add('is-playing')
    this.liveList.scrollTo({
      top: 100,
      left: 100,
      behavior: 'smooth'
    })
  }

  /**
   * Génère l'URL pour l'embed
   * @param {string} id
   */
  youtubeURL (id) {
    return `https://www.youtube-nocookie.com/embed/${id}?controls=1&autoplay=1&loop=0&showinfo=0&rel=0&hd=1`
  }
}
