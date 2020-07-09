import './RecapLiveElement.js'
import './PlayButton.js'
import './YoutubePlayer.js'
import './Waves.js'
import './Alert.js'
import './Switch.js'
import './Modal.js'
import './comments/index.js'
import './TimeAgo.js'
import './Choices.js'
import './editor/index.js'
import './AjaxDelete.js'
import './AutoScroll.js'
import './AnimatedEditor.js'
import './AutoSubmit.js'
import './Notifications.jsx'
import './forum/index.js'
import '@grafikart/spinning-dots-element'
import {ModalDialog, NavTabs, TextareaAutogrow} from '@sb-elements/all'

customElements.define('nav-tabs', NavTabs)
customElements.define('textarea-autogrow', TextareaAutogrow, {extends: 'textarea'})
customElements.define('modal-dialog', ModalDialog)
