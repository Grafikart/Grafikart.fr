import {CreateMessage} from './CreateMessage.jsx'
import preactCustomElement from '@fn/preact'
import {ForumActions} from '@el/forum/ForumActions'

preactCustomElement(CreateMessage, 'forum-create-message', ['topic'])
preactCustomElement(ForumActions, 'forum-actions', ['message', 'topic'])
