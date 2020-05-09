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
import './Notifications'
import './forum'
import '@grafikart/spinning-dots-element'
import {ModalDialog, NavTabs, TextareaAutogrow} from '@sb-elements/all'

customElements.define('nav-tabs', NavTabs)
customElements.define('textarea-autogrow', TextareaAutogrow, {extends: 'textarea'})
customElements.define('modal-dialog', ModalDialog)
