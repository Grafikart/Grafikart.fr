import { RecapLiveElement } from './RecapLiveElement.js'
import { PlayButton } from './PlayButton.js'
import { YoutubePlayer } from './YoutubePlayer.js'
import { Waves } from './Waves.js'
import { Alert, FloatingAlert } from './Alert.js'
import { Switch } from './Switch.js'
import { Modal } from './Modal.js'
import { Comments } from './comments/index.js'
import { TimeAgo } from './TimeAgo.js'
import { Choices } from './Choices.js'
import { MarkdownEditor } from './editor/index.js'
import { AjaxDelete } from './AjaxDelete.js'
import { AutoScroll } from './AutoScroll.js'
import { AnimatedEditor } from './AnimatedEditor.js'
import { AutoSubmit } from './AutoSubmit.js'
import { Notifications } from './Notifications.jsx'
import SpinningDots from '@grafikart/spinning-dots-element'
import { ModalDialog, NavTabs, TextareaAutogrow } from '@sb-elements/all'
import { ContactForm } from '/elements/ContactForm.jsx'
import preactCustomElement from '/functions/preact.js'
import './forum/index.js'

// Custom Elements
customElements.define('nav-tabs', NavTabs)
customElements.define('textarea-autogrow', TextareaAutogrow, { extends: 'textarea' })
customElements.define('modal-dialog', ModalDialog)
customElements.define('alert-message', Alert)
customElements.define('alert-floating', FloatingAlert)
customElements.define('youtube-player', YoutubePlayer)
customElements.define('modal-box', Modal)
customElements.define('live-recap', RecapLiveElement)
customElements.define('play-button', PlayButton)
customElements.define('waves-shape', Waves)
customElements.define('comments-area', Comments)
customElements.define('time-ago', TimeAgo)
customElements.define('ajax-delete', AjaxDelete)
customElements.define('animated-editor', AnimatedEditor)
customElements.define('spinning-dots', SpinningDots)
preactCustomElement('site-notifications', Notifications)
preactCustomElement('contact-form', ContactForm)

// CustomElement étendus
customElements.define('input-switch', Switch, { extends: 'input' })
customElements.define('input-choices', Choices, { extends: 'input' })
customElements.define('markdown-editor', MarkdownEditor, { extends: 'textarea' })
customElements.define('auto-scroll', AutoScroll, { extends: 'div' })
customElements.define('auto-submit', AutoSubmit, { extends: 'form' })
