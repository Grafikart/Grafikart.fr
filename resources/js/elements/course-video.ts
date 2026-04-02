import { createElement } from "react"
import { createRoot, type Root } from "react-dom/client"
import { Spinner } from "@/components/ui/spinner.tsx"
import { StickyVideo } from "@/components/ui/video/sticky-video.tsx"
import { apiFetch } from "@/hooks/use-api-fetch.ts"
import { isAuthenticated } from "@/lib/auth.ts"

type State = "poster" | "loading" | "loaded"

/**
 * Player for the video on the courses
 */
export class CourseVideo extends HTMLElement {
  private state: State = "poster"
  private root: Root | null = null

  private getRequiredAttribute(name: string): string {
    const value = this.getAttribute(name)
    if (!value) {
      throw new Error(`A ${name} attribute must be set on <course-video>`)
    }
    return value
  }

  private setOptionalAttribute(name: string, value: string | null) {
    if (!value) {
      this.removeAttribute(name)
      return
    }
    this.setAttribute(name, value)
  }

  connectedCallback() {
    this.addEventListener("click", this.init)
    window.addEventListener("hashchange", this.onHashChange, {
      capture: true,
    })
    this.onHashChange()
  }

  disconnectedCallback() {
    window.removeEventListener("hashchange", this.onHashChange)
    this.root?.unmount()
  }

  onHashChange = async () => {
    if (window.location.hash === "#autoplay") {
      this.init()
      return
    }
    if (!window.location.hash.startsWith("#t")) {
      return
    }
    const time = parseInt(window.location.hash.replace("#t", ""), 10)
    this.scrollIntoView({
      behavior: "smooth",
      inline: "center",
      block: "center",
    })
    await this.play(time)
  }

  init = () => {
    const meta = document.querySelector('meta[name="video:start"]')
    if (!meta) {
      return this.play(0)
    }
    this.play(parseInt(meta.getAttribute("content") ?? "0", 10))
  }

  play = async (time: number) => {
    if (this.state === "loading") {
      return
    }
    if (this.state !== "loaded") {
      this.removeEventListener("click", this.init)
      this.state = "loading"
      this.root = createRoot(this)
      this.root.render(
        createElement(Spinner, { className: "text-white size-10" }),
      )
    }
    const { VideoPlayer } =
      await import("../components/ui/video/video-player.tsx")
    this.state = "loaded"
    const hasChapters = Boolean(document.querySelector('a[href^="#t"]'))
    this.root?.render(
      createElement(StickyVideo, {
        parent: this,
        children: createElement(VideoPlayer, {
          key: "video",
          id: this.getAttribute("video")!,
          chapters: hasChapters
            ? `/api/courses/${this.getAttribute("course")}/vtt`
            : undefined,
          poster: this.getAttribute("poster"),
          start: time,
          onProgress: isAuthenticated() ? this.onProgress : undefined,
        }),
      }),
    )
  }

  onProgress = async (n: number) => {
    const progress = Math.round(n * 1000)
    const r = await apiFetch<{ html?: string }>(
      `/api/courses/${this.course}/progress`,
      {
        method: "post",
        body: JSON.stringify({
          progress,
        }),
      },
    )

    // Emit an event on course completion
    if (progress === 1000) {
      this.dispatchEvent(
        new CustomEvent("course:completed", {
          bubbles: true,
          detail: {
            courseId: parseInt(this.course, 10),
          },
        }),
      )
    }

    // If we receive a dialog, inject it into the HTML
    if (r.html) {
      document.body.insertAdjacentHTML("beforeend", r.html)
      const dialog = document.getElementById("completion") as HTMLDialogElement
      dialog?.showModal()
    }
  }

  get course(): string {
    return this.getRequiredAttribute("course")
  }

  set course(value: string) {
    this.setOptionalAttribute("course", value)
  }

  get video(): string {
    return this.getRequiredAttribute("video")
  }

  set video(value: string) {
    this.setOptionalAttribute("video", value)
  }

  get poster(): string | null {
    return this.getAttribute("poster")
  }

  set poster(value: string | null) {
    this.setOptionalAttribute("poster", value)
  }
}
