import { loadJs } from "@/lib/dom.ts"

type Turnstile = {
  render: (
    element: HTMLElement,
    options: { sitekey: string; appearance: string; size: string },
  ) => void
}

/**
 * Load a captcha into a custom element
 */
export class CaptchaChallenge extends HTMLElement {
  async connectedCallback() {
    const key = this.getAttribute("data-key")
    if (!key) return
    const { turnstile } = await loadJs<{ turnstile: Turnstile }>(
      "https://challenges.cloudflare.com/turnstile/v0/api.js",
    )
    turnstile.render(this, {
      sitekey: key,
      appearance: "interaction-only",
      size: "flexible",
    })
  }
}
