import { RecapLiveElement } from './RecapLiveElement.js'
import { PlayButton } from './PlayButton.js'
import { YoutubePlayer } from './player/YoutubePlayer.js'
import { Waves } from './Waves.js'
import { Alert, FloatingAlert } from './Alert.js'
import Skeleton from './Skeleton.js'
import { Switch } from './Switch.js'
import { Comments } from '../components/Comments.jsx'
import { TimeAgo } from './TimeAgo.js'
import { TimeCountdown } from './TimeCountdown.js'
import { InputChoices, SelectChoices } from './Choices.js'
import { MarkdownEditor } from './editor/index.js'
import { AjaxDelete } from './AjaxDelete.js'
import { AutoScroll } from './AutoScroll.js'
import { AnimatedEditor } from './AnimatedEditor.js'
import { AutoSubmit } from './AutoSubmit.js'
import { Notifications } from './Notifications.jsx'
import SpinningDots from '@grafikart/spinning-dots-element'
import { ModalDialog, NavTabs, TextareaAutogrow } from '@sb-elements/all'
import { ContactForm } from './ContactForm.jsx'
import preactCustomElement from '/functions/preact.js'
import EditButton from './admin/EditButton.js'
import './forum/index.js'
import { ProgressTracker } from '/elements/courses/ProgressTracker.js'
import Sharer from '/elements/Sharer.js'
import DeleteAccount from '/elements/DeleteAccount.jsx'
import { PremiumButton } from '/elements/premium/PremiumButton.jsx'
import { BadgeUnlock } from '/elements/BadgeUnlock.jsx'
import { Search, SearchInput } from '/elements/search/Search.jsx'
import { CycleClasses } from '/elements/CycleClasses.js'
import { Dropdown } from '/elements/Dropdown.js'
import { Confetti } from '/elements/Confetti.js'
import { PremiumPlayer } from '/elements/player/PremiumPlayer.js'
import LoaderOverlay from '/elements/LoaderOverlay.js'
import { ThemeSwitcher } from '/elements/ThemeSwitcher.js'

// Custom Elements
customElements.define('nav-tabs', NavTabs)
customElements.define('textarea-autogrow', TextareaAutogrow, { extends: 'textarea' })
customElements.define('modal-dialog', ModalDialog)
customElements.define('alert-message', Alert)
customElements.define('alert-floating', FloatingAlert)
customElements.define('youtube-player', YoutubePlayer)
customElements.define('live-recap', RecapLiveElement)
customElements.define('play-button', PlayButton)
customElements.define('waves-shape', Waves)
customElements.define('time-ago', TimeAgo)
customElements.define('time-countdown', TimeCountdown)
customElements.define('ajax-delete', AjaxDelete)
customElements.define('animated-editor', AnimatedEditor)
customElements.define('spinning-dots', SpinningDots)
customElements.define('admin-edit', EditButton)
customElements.define('progress-tracker', ProgressTracker)
customElements.define('skeleton-box', Skeleton)
customElements.define('social-share', Sharer, { extends: 'a' })
customElements.define('cycle-classes', CycleClasses)
customElements.define('drop-down', Dropdown)
customElements.define('con-fetti', Confetti)
customElements.define('premium-player', PremiumPlayer)
customElements.define('loader-overlay', LoaderOverlay)
preactCustomElement('site-notifications', Notifications)
preactCustomElement('contact-form', ContactForm)
preactCustomElement('comments-area', Comments, ['target'])
preactCustomElement('delete-account', DeleteAccount, ['url', 'csrf'])
preactCustomElement('premium-button', PremiumButton, ['plan', 'children'])
preactCustomElement('badge-unlock', BadgeUnlock, ['name', 'description'])
preactCustomElement('search-button', Search)
preactCustomElement('search-input', SearchInput)

// CustomElement étendus
customElements.define('input-switch', Switch, { extends: 'input' })
customElements.define('theme-switcher', ThemeSwitcher, { extends: 'input' })
customElements.define('input-choices', InputChoices, { extends: 'input' })
customElements.define('select-choices', SelectChoices, { extends: 'select' })
customElements.define('markdown-editor', MarkdownEditor, { extends: 'textarea' })
customElements.define('auto-scroll', AutoScroll, { extends: 'div' })
customElements.define('auto-submit', AutoSubmit, { extends: 'form' })
