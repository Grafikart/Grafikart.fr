import preactCustomElement from '/functions/preact.js'
import { CreateMessage } from './CreateMessage.jsx'
import { ForumActions } from '/elements/forum/ForumActions.jsx'
import { ForumDelete } from '/elements/forum/ForumDelete.jsx'
import { ForumEdit } from '/elements/forum/ForumEdit.jsx'

preactCustomElement('forum-delete', ForumDelete)
preactCustomElement('forum-edit', ForumEdit)
preactCustomElement('forum-create-message', CreateMessage, ['topic'])
preactCustomElement('forum-actions', ForumActions, ['message', 'topic'])
