/**
 * Element permettant de gérer la liste des derniers lives
 *
 * @property {boolean} isPlaying
 * @property {HTMLDivElement} videoContainer
 * @property {HTMLDivElement} liveList
 * @property {YoutubePlayer} player
 * @property {HTMLAnchorElement} currentLive
 * @property {string} path URL vers les lives
 */
import { YoutubePlayer } from './YoutubePlayer.js'
import SpinningDots from '@grafikart/spinning-dots-element'

export class RecapLiveElement extends HTMLElement {
  connectedCallback () {
    this.path = this.getAttribute('path')
    this.play = this.play.bind(this)
    this.gotoYear = this.gotoYear.bind(this)
    this.liveList = this.querySelector('.live-list')
    this.querySelectorAll('.live').forEach(live => {
      live.addEventListener('click', this.play)
    })
    this.querySelectorAll('.live-years a').forEach(a => {
      a.addEventListener('click', this.gotoYear)
    })
  }

  /**
   *
   */
  async gotoYear (e) {
    e.preventDefault()
    e.stopPropagation()
    if (e.currentTarget.classList.contains('is-active')) {
      return
    }
    e.currentTarget.parentElement.querySelector('.is-active').classList.remove('is-active')
    this.showLoader()
    e.currentTarget.classList.add('is-active')
    const year = e.currentTarget.text
    const url = `${this.path}/${year}`
    const response = await fetch(`${url}?ajax=1`)
    if (response.status >= 200 && response.status < 300) {
      const data = await response.text()
      this.liveList.innerHTML = data
      this.liveList.querySelectorAll('.live').forEach(live => {
        live.addEventListener('click', this.play)
      })
      window.history.replaceState({}, '', url)
    } else {
      console.error(response)
    }
    this.hideLoader()
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
      this.player = new YoutubePlayer({ autoplay: 1 })
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
    live.scrollIntoView({ block: 'center', behavior: 'smooth', inline: 'nearest' })
    this.player.scrollIntoView({ block: 'start', behavior: 'smooth', inline: 'nearest' })
  }

  showLoader () {
    const loader = new SpinningDots()
    loader.style.width = '20px'
    loader.classList.add('loader')
    this.querySelector('.live-years').appendChild(loader)
  }

  hideLoader () {
    const loader = this.querySelector('.loader')
    if (loader) {
      loader.parentElement.removeChild(loader)
    }
  }
}
