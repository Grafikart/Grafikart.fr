import { useEffect, useRef, useState } from "react"
import EasyMDE from "easymde"
import "easymde/dist/easymde.min.css"
import { Dialog, DialogContent } from "@/components/ui/dialog.tsx"
import { FileExplorer } from "@/components/ui/form/file-explorer.tsx"
import type { AttachmentFileData } from "@/types"

type Props = {
  defaultValue: string
  name: string
  attachableType?: string
  attachableId?: number | null
}
export function MDEditor(props: Props) {
  const textareaRef = useRef<HTMLTextAreaElement>(null)
  const editorRef = useRef<EasyMDE | null>(null)
  const [fileExplorerOpen, setFileExplorerOpen] = useState(false)

  const onFileSelect = (file: AttachmentFileData) => {
    const editor = editorRef.current
    if (!editor) {
      return
    }
    const cm = editor.codemirror
    const pos = cm.getCursor()
    const url = new URL(file.url, window.location.origin)
    cm.replaceRange(`![${file.name}](${url.pathname})`, pos)
    setFileExplorerOpen(false)
  }

  useEffect(() => {
    if (!textareaRef.current || editorRef.current) {
      return
    }

    editorRef.current = new EasyMDE({
      element: textareaRef.current,
      status: false,
      spellChecker: false,
      unorderedListStyle: "-",
      sideBySideFullscreen: false,
      indentWithTabs: false,
      previewClass: ["prose", "p-8", "max-w-175", "mx-auto"],
      toolbar: [
        "bold",
        "italic",
        "heading",
        "|",
        "quote",
        "unordered-list",
        "ordered-list",
        "|",
        "link",
        {
          name: "image",
          action: () => setFileExplorerOpen(true),
          className: "fa fa-image",
          title: "Insert image",
        },
        "code",
        "|",
        "side-by-side",
        "fullscreen",
        "|",
        "guide",
      ],
    })

    return () => {
      editorRef.current?.toTextArea()
      editorRef.current = null
    }
  }, [])

  return (
    <div className="mdeditor">
      <textarea
        ref={textareaRef}
        name={props.name}
        defaultValue={props.defaultValue}
      />
      <Dialog open={fileExplorerOpen} onOpenChange={setFileExplorerOpen}>
        <DialogContent className="p-0 max-w-300">
          <FileExplorer
            onSelect={onFileSelect}
            attachableType={props.attachableType}
            attachableId={props.attachableId}
          />
        </DialogContent>
      </Dialog>
    </div>
  )
}
