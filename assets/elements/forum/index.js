import { CreateMessage } from './CreateMessage.jsx'
import preactCustomElement from '/functions/preact'
import { ForumActions } from '/elements/forum/ForumActions'

preactCustomElement(CreateMessage, 'forum-create-message', ['topic'])
preactCustomElement(ForumActions, 'forum-actions', ['message', 'topic'])
