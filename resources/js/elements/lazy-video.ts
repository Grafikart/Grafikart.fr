/**
 * Lazy YouTube player that responds to the hash "#t40" to update it's starting time
 */
export class LazyVideo extends HTMLElement {
  private video = ""
  private time = 0
  private _iframe: HTMLIFrameElement | null = null

  public onHashChange = () => {
    if (!window.location.hash.startsWith("#t")) {
      return
    }
    this.time = parseInt(window.location.hash.replace("#t", ""), 10)
    this.iframe.scrollIntoView({
      behavior: "smooth",
      inline: "center",
      block: "center",
    })
    this.iframe.setAttribute("url", this.url)
  }

  get iframe(): HTMLIFrameElement {
    if (this._iframe) {
      return this._iframe
    }
    this.innerHTML = `<iframe
        class="aspect-video w-full"
        src={url}
        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
        referrerPolicy="strict-origin-when-cross-origin"
        allowFullScreen
        />`
    this._iframe = this.querySelector("iframe")!
    return this._iframe
  }

  get url(): string {
    const baseUrl = `https://www.youtube-nocookie.com/embed/${this.video}?autoplay=1`
    const url = new URL(baseUrl)
    if (this.time) {
      url.searchParams.set("start", this.time.toString())
    }
    return url.toString()
  }

  private play = () => {
    this.removeEventListener("click", this.play)
    this.iframe.setAttribute("src", this.url)
  }

  connectedCallback() {
    this.video = this.getAttribute("video") ?? ""
    if (!this.video) {
      throw new Error("Cannot load a video without its id")
    }
    window.addEventListener("hashchange", this.onHashChange)
    this.addEventListener("click", this.play)
  }

  disconnectedCallback() {
    window.removeEventListener("hashchange", this.onHashChange)
  }
}
