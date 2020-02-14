import '../css/app.scss'

import RecapLiveElement from './elements/RecapLiveElement'
import PlayButton from './elements/PlayButton.js'
import YoutubePlayer from './elements/YoutubePlayer.js'
import Waves from './elements/Waves'
import Alert from './elements/Alert'
import Turbolinks from 'turbolinks'

import './modules/scrollreveal'

customElements.define('live-recap', RecapLiveElement)
customElements.define('play-button', PlayButton)
customElements.define('youtube-player', YoutubePlayer)
customElements.define('waves-shape', Waves)
customElements.define('alert-message', Alert)

document.querySelector('#dark-toggle').addEventListener('click', function (e) {
  e.preventDefault()
  document.body.classList.toggle('dark')
})

Turbolinks.start()

