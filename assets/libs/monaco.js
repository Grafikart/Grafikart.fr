import 'monaco-editor/esm/vs/editor/browser/controller/coreCommands.js'
import 'monaco-editor/esm/vs/editor/contrib/find/findController.js'
import 'monaco-editor/esm/vs/editor/editor.worker.js'
import * as m from 'monaco-editor/esm/vs/editor/editor.api.js'
import 'monaco-editor/esm/vs/basic-languages/markdown/markdown.contribution.js'

global.monaco = m

export default monaco
