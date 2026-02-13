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

  connectedCallback() {
    this.addEventListener("click", this.init)
    window.addEventListener("hashchange", this.onHashChange, {
      capture: true,
    })
  }

  disconnectedCallback() {
    window.removeEventListener("hashchange", this.onHashChange)
    this.root?.unmount()
  }

  onHashChange = async () => {
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
    this.play(
      parseInt(
        document
          .querySelector('meta[name="video:start"]')!
          .getAttribute("content")!,
        10,
      ),
    )
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
    const { VideoPlayer } = await import(
      "../components/ui/video/video-player.tsx"
    )
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
    await apiFetch(`/api/courses/${this.course}/progress`, {
      method: "post",
      body: JSON.stringify({
        progress: Math.round(n * 1000),
      }),
    })
  }

  get course(): string {
    const course = this.getAttribute("course")
    if (!course) {
      throw new Error(`A course attribute must be set on <course-video>`)
    }
    return course
  }
}
