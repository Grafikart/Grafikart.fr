import preactCustomElement from '/functions/preact.js'
import { CreateMessage } from './CreateMessage.jsx'
import { ForumReport } from '/elements/forum/ForumReport.jsx'
import { ForumDelete } from '/elements/forum/ForumDelete.jsx'
import { ForumEdit } from '/elements/forum/ForumEdit.jsx'
import { ForumRead } from '/elements/forum/ForumRead.jsx'
import { ForumSolve } from '/elements/forum/ForumSolve.jsx'
import { ForumFollow } from '/elements/forum/ForumFollow.jsx'

preactCustomElement('forum-delete', ForumDelete)
preactCustomElement('forum-edit', ForumEdit)
preactCustomElement('forum-create-message', CreateMessage, ['topic', 'disabled'])
preactCustomElement('forum-report', ForumReport, ['message', 'topic'])
preactCustomElement('forum-follow', ForumFollow, ['topic', 'subscribed'])
preactCustomElement('forum-solve', ForumSolve, ['message', 'topicAuthor', 'disabled'])
preactCustomElement('forum-read', ForumRead, ['endpoint'])
