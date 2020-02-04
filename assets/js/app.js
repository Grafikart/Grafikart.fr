import '../css/app.scss'

import RecapLiveElement from './elements/RecapLiveElement'

customElements.define('live-recap', RecapLiveElement)

document.querySelector('.header__account').addEventListener('click', function (e) {
  e.preventDefault()
  document.body.classList.toggle('dark-mode')
})
