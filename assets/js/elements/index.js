import './RecapLiveElement'
import './PlayButton.js'
import './YoutubePlayer.js'
import './Waves'
import './Alert'
import './Switch'
import './Modal'
import './Comments'
import './TimeAgo'
import './Choices'
import './editor'
import './AjaxDelete'
import './AutoScroll'
import './AnimatedEditor'
import './AutoSubmit'
import '@grafikart/spinning-dots-element'
import {NavTabs, TextareaAutogrow} from '@sb-elements/all'
import preactCustomElement from '@fn/preact'
import {Notifications} from '../components/Notifications/Notifications'

customElements.define('nav-tabs', NavTabs)
customElements.define('textarea-autogrow', TextareaAutogrow, {extends: 'textarea'})
preactCustomElement(Notifications, 'site-notifications')
