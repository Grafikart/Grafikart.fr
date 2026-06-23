import katex from "katex"
import { LazyComponent } from "@/lib/custom-element.ts"

const KATEX_STYLESHEET_URL =
  "https://cdn.jsdelivr.net/npm/katex@0.17.0/dist/katex.css"
const KATEX_STYLE_INTEGRITY =
  "sha384-xo3oXcu2FwjgZEcZnpFAF2BUCgyAPNcn8CwwPnaN0ajcbe0WxSoeKnFeeAfQnIZD"
let styleLoaded = false

export default class MathFormula extends LazyComponent {
  async onMount() {
    loadStyle()
    this.el.innerHTML = katex.renderToString(this.el.innerHTML, {
      throwOnError: false,
    })
  }
}

function loadStyle() {
  if (styleLoaded) {
    return
  }
  // CSS is already here in the page, do nothing
  const existingStylesheet = document.querySelector<HTMLLinkElement>(
    `link[href="${KATEX_STYLESHEET_URL}"]`,
  )
  if (existingStylesheet) {
    styleLoaded = true
    return
  }

  const stylesheet = document.createElement("link")
  stylesheet.rel = "stylesheet"
  stylesheet.integrity = KATEX_STYLE_INTEGRITY
  stylesheet.crossOrigin = "anonymous"
  stylesheet.href = KATEX_STYLESHEET_URL
  document.head.append(stylesheet)
  styleLoaded = true
}
