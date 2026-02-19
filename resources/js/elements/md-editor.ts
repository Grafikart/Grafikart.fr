import EasyMDE from "easymde"
import "easymde/dist/easymde.min.css"
import { LazyComponent } from "@/lib/custom-element.ts"

export default class MdEditor extends LazyComponent {
  private editor: EasyMDE | null = null

  onMount(): void {
    const textarea = this.el.querySelector("textarea")
    if (!textarea) {
      return
    }

    this.el.classList.add("mdeditor")
    this.editor = new EasyMDE({
      element: textarea,
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
        "code",
        "|",
        "side-by-side",
        "fullscreen",
        "|",
        "guide",
      ],
    })
  }

  onUnmount(): void {
    this.editor?.toTextArea()
    this.editor = null
  }
}
