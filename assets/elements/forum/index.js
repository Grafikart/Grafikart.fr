import { CreateMessage } from './CreateMessage.jsx'
import preactCustomElement from '/functions/preact.js'
import { ForumActions } from '/elements/forum/ForumActions.jsx'

preactCustomElement(CreateMessage, 'forum-create-message', ['topic'])
preactCustomElement(ForumActions, 'forum-actions', ['message', 'topic'])
