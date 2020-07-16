import preactCustomElement from '/functions/preact.js'
import { CreateMessage } from './CreateMessage.jsx'
import { ForumReport } from '/elements/forum/ForumReport.jsx'
import { ForumDelete } from '/elements/forum/ForumDelete.jsx'
import { ForumEdit } from '/elements/forum/ForumEdit.jsx'
import { ForumRead } from '/elements/forum/ForumRead.jsx'

preactCustomElement('forum-delete', ForumDelete)
preactCustomElement('forum-edit', ForumEdit)
preactCustomElement('forum-create-message', CreateMessage, ['topic'])
preactCustomElement('forum-report', ForumReport, ['message', 'topic'])
preactCustomElement('forum-read', ForumRead, ['endpoint'])
