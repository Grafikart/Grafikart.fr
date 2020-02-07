/**
 * Instance de l'API youtube iframe
 * @type {null|YT}
 */
let YT = null

/**
 * Element représentant une video youtube `<youtube-player video="UEINCHBN">`.
 *
 * @property {ShadowRoot} root
 * @property {?number} timer Timer permettant de suivre la progression de la lecture
 * @property {YT.Player} player
 */
export default class YoutubePlayer extends HTMLElement {
  static get observedAttributes () {
    return ['video']
  }

  constructor (attributes = {}) {
    super()
    Object.keys(attributes).forEach((k) => this.setAttribute(k, attributes[k]))
    this.root = this.attachShadow({mode: 'open'})
    this.onYoutubePlayerStateChange = this.onYoutubePlayerStateChange.bind(this)
    this.onYoutubePlayerReady = this.onYoutubePlayerReady.bind(this)
    this.root.innerHTML = `
      ${this.buildStyles()}
      <div><div class="player"></div></div>
    `
  }

  disconnectedCallback () {
    this.stopTimer()
  }

  async attributeChangedCallback (name, oldValue, newValue) {
    if (name === 'video' && newValue !== null) {
      await loadYoutubeApi()
      if (this.player) {
        this.player.cueVideoById(this.getAttribute('video'))
        this.player.playVideo()
        return
      }
      this.player = new YT.Player(this.root.querySelector('.player'), {
        videoId: this.getAttribute('video'),
        host: 'https://www.youtube-nocookie.com',
        playerVars: {
          autoplay: this.getAttribute('autoplay') ? 1 : 0,
          loop: 0,
          controls: 1,
          showinfo: 0,
          rel: 0
        },
        events: {
          onStateChange: this.onYoutubePlayerStateChange,
          onReady: this.onYoutubePlayerReady,
        }
      })
    }
  }

  /**
   * @param {YT.OnStateChangeEvent} event
   */
  onYoutubePlayerStateChange (event) {
    if (event.data === YT.PlayerState.PLAYING) {
      this.startTimer()
      this.dispatchEvent(new Event('play'))
    } else if (event.data === YT.PlayerState.ENDED) {
      this.stopTimer()
      this.dispatchEvent(new Event('ended'))
    }
  }

  /**
   * @param {YT.PlayerEvent} event
   */
  onYoutubePlayerReady (event) {
    this.startTimer()
    this.dispatchEvent(new Event('play'))
  }

  /**
   * Génère le style associé au player
   * @returns {string}
   */
  buildStyles () {
    return `<style>
      div {
        background-color:black;
        position: relative;
        padding-bottom: 56.25%;
      }
      iframe {
        position: absolute;
        top:0;
        left: 0;
        bottom: 0;
        right: 0;
        width: 100%;
        height: 100%;
      }
    </style>`
  }

  stopTimer () {
    if (this.timer) {
      window.clearInterval(this.timer)
      this.timer = null
    }
  }

  startTimer () {
    if (this.timer) {
      return null
    }
    this.dispatchEvent(new Event('timeupdate'))
    this.timer = window.setInterval(() => this.dispatchEvent(new Event('timeupdate')), 1000)
  }

  /**
   * Durée de la vidéo
   * @return {number}
   */
  get duration () {
    return this.player ? this.player.getDuration() : null
  }

  /**
   * Position de la lecture
   * @return {number}
   */
  get currentTime () {
    return this.player ? this.player.getCurrentTime() : null
  }

}

/**
 * Charge l'API Youtube Player
 * @returns {Promise<YT>}
 */
async function loadYoutubeApi () {
  return new Promise((resolve, reject) => {
    if (YT) {
      resolve(YT)
    }
    const tag = document.createElement('script')
    tag.src = 'https://www.youtube.com/iframe_api'
    const firstScriptTag = document.getElementsByTagName('script')[0]
    firstScriptTag.parentNode.insertBefore(tag, firstScriptTag)
    global.onYouTubeIframeAPIReady = function () {
      YT = global.YT
      global.onYouTubeIframeAPIReady = undefined
      resolve(YT)
    }
  })
}
