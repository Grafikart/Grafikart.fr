import { createElement } from "react"
import { createRoot, type Root } from "react-dom/client"
import { PremiumDialog } from "@/elements/premium-dialog.tsx"

export class PremiumButton extends HTMLElement {
  private dialogRoot: Root | null = null
  private dialogContainer: HTMLDivElement | null = null

  connectedCallback(): void {
    this.addEventListener("click", this.handleClick)
  }

  disconnectedCallback(): void {
    this.removeEventListener("click", this.handleClick)
    this.cleanup()
  }

  private handleClick = (): void => {
    const duration = parseInt(this.getAttribute("duration") || "1", 10)
    const plan = parseInt(this.getAttribute("plan") || "0", 10)
    const price = parseInt(this.getAttribute("price") || "0", 10)
    const paypalId = this.getAttribute("paypalid")!

    this.dialogContainer = document.createElement("div")
    document.body.appendChild(this.dialogContainer)
    this.dialogRoot = createRoot(this.dialogContainer)
    this.dialogRoot.render(
      createElement(PremiumDialog, {
        duration,
        plan,
        paypalId: paypalId,
        price,
        onClose: () => this.cleanup(),
      }),
    )
  }

  private cleanup(): void {
    if (this.dialogRoot) {
      this.dialogRoot.unmount()
      this.dialogRoot = null
    }
    if (this.dialogContainer) {
      this.dialogContainer.remove()
      this.dialogContainer = null
    }
  }
}
