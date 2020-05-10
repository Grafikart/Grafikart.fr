import {Report} from './ForumReport.jsx'
import {CreateMessage} from './CreateMessage.jsx'
import preactCustomElement from '@fn/preact'

preactCustomElement(Report, 'forum-report', ['message'])
preactCustomElement(CreateMessage, 'forum-create-message', ['topic'])
