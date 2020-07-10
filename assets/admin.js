import './css/admin.scss'

import './elements/DatePicker.js'
import './elements/admin/UserSelect.js'
import InputAttachment from './elements/admin/InputAttachment.js'
import FileManager from './elements/admin/filemanager/index.js'
import './elements/DiffEditor.js'
import './elements/admin/ChaptersEditor.js'
import './elements/admin/ItemSorter.js'
import './elements/admin/FormNotification.jsx'

customElements.define('input-attachment', InputAttachment, { extends: 'input' })
customElements.define('file-manager', FileManager)
