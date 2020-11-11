import './css/admin.scss'
import { DatePicker } from './elements/DatePicker.js'
import { UserSelect } from './elements/admin/UserSelect.js'
import InputAttachment from './elements/admin/InputAttachment.js'
import FileManager from './elements/admin/filemanager/index.js'
import { DiffEditor } from './elements/DiffEditor.jsx'
import { ChaptersEditor } from './elements/admin/ChaptersEditor.js'
import { ItemSorter } from './elements/admin/ItemSorter.js'
import { FormNotification } from './elements/admin/FormNotification.jsx'
import preactCustomElement from './functions/preact'
import { Spotlight } from '/elements/admin/Spotlight.jsx'
import { AutosaveBlur } from '/elements/admin/AutosaveBlur.js'
import { LineChart } from '/elements/admin/LineChart.js'

customElements.define('input-attachment', InputAttachment, { extends: 'input' })
customElements.define('file-manager', FileManager)
customElements.define('date-picker', DatePicker, { extends: 'input' })
customElements.define('diff-editor', DiffEditor, { extends: 'textarea' })
customElements.define('chapters-editor', ChaptersEditor, { extends: 'textarea' })
customElements.define('item-sorter', ItemSorter)
customElements.define('user-select', UserSelect, { extends: 'select' })
customElements.define('autosave-blur', AutosaveBlur, { extends: 'form' })
customElements.define('line-chart', LineChart)
preactCustomElement('form-notification', FormNotification)
preactCustomElement('spotlight-bar', Spotlight)
