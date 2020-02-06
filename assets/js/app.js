import '../css/app.scss'

import RecapLiveElement from './elements/RecapLiveElement'
import PlayButton from './elements/PlayButton.js'
import YoutubePlayer from './elements/YoutubePlayer.js'

customElements.define('live-recap', RecapLiveElement)
customElements.define('play-button', PlayButton)
customElements.define('youtube-player', YoutubePlayer)

document.querySelector('.header__account').addEventListener('click', function (e) {
  e.preventDefault()
  document.body.classList.toggle('dark-mode')
})
