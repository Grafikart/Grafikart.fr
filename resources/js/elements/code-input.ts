/**
 * Input for 2FA authentication code
 * Source : https://grafikart.fr/tutoriels/web-component-1201
 */
export class CodeInput extends HTMLElement {
  #inputs: HTMLInputElement[] = []
  #hiddenInput: HTMLInputElement | null = null

  static get observedAttributes() {
    return ["value"]
  }

  connectedCallback(): void {
    const legend = this.getAttribute("legend") ?? "Entrez votre code"
    const name = this.getAttribute("name") ?? ""
    const size = parseInt(this.getAttribute("size") ?? "6", 10)
    const value = this.getAttribute("value") ?? ""
    this.innerHTML = `
        <fieldset>
            <legend class="sr-only">${legend}</legend>
            <div class="flex gap-2">
                ${Array.from(
                  { length: size },
                  (_, k) => `<input
                  type="text"
                  class="border rounded-sm w-full text-center text-2xl aspect-square focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20"
                  inputmode="numeric"
                  aria-label="Chiffre ${k}"
                  pattern="[0-9]{1}"
                  value="${value.slice(k, k + 1)}"
                  required
                >`,
                ).join("")}
            </div>
            <input type="hidden" name="${name}" value="${value}">
        </fieldset>`
    this.#hiddenInput = this.querySelector('input[type="hidden"]')
    this.#inputs = Array.from(this.querySelectorAll('input[type="text"]'))
    this.#inputs.forEach((input) => {
      input.addEventListener("paste", this.#onPaste)
      input.addEventListener("input", this.#onInput)
      input.addEventListener("keydown", this.#onKeyDown)
    })
  }

  attributeChangedCallback(
    name: string,
    _oldValue: string | null,
    newValue: string | null,
  ): void {
    if (name === "value") {
      this.value = newValue
    }
  }

  set value(str: string | null) {
    if (!this.#inputs || this.#inputs.length <= 0) {
      return
    }
    const value = str ?? ""
    this.#inputs.forEach((input, k) => {
      input.value = value[k] ?? ""
    })
    this.#updateHiddenInput()
  }

  #onInput = (e: Event): void => {
    const input = e.currentTarget as HTMLInputElement
    input.value = input.value.replaceAll(/\D/g, "").slice(0, 1)
    this.#updateHiddenInput()
  }

  #onKeyDown = (e: KeyboardEvent): void => {
    const input = e.currentTarget as HTMLInputElement
    if (e.key.match(/\d/)) {
      e.preventDefault()
      input.value = e.key
      const nextInput = input.nextElementSibling as HTMLInputElement | null
      if (nextInput) {
        nextInput.focus()
      }
      this.#updateHiddenInput()
    }
    if (e.key === "Backspace" && input.value === "") {
      const previousInput =
        input.previousElementSibling as HTMLInputElement | null
      if (!previousInput) {
        return
      }
      previousInput.value = ""
      previousInput.focus()
      this.#updateHiddenInput()
    }
  }

  #updateHiddenInput(): void {
    this.#hiddenInput!.value = this.#inputs.map((input) => input.value).join("")
    if (this.#hiddenInput!.value.length === this.#inputs.length) {
      this.closest("form")?.requestSubmit()
    }
  }

  #onPaste = (e: ClipboardEvent): void => {
    e.preventDefault()
    const index = this.#inputs.findIndex((input) => input === e.currentTarget)
    const text = e.clipboardData!.getData("text").replaceAll(/\D/g, "")
    if (text.length === 0) {
      return
    }
    let lastInput: HTMLInputElement | undefined
    this.#inputs.slice(index).forEach((input, k) => {
      if (!text[k]) {
        return
      }
      input.value = text[k]
      lastInput = input
    })
    if (!lastInput) {
      return
    }
    const nextAfterLastInput =
      lastInput.nextElementSibling as HTMLInputElement | null
    if (nextAfterLastInput) {
      nextAfterLastInput.focus()
    } else {
      lastInput.focus()
    }
    this.#updateHiddenInput()
  }
}
