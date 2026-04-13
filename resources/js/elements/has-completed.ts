import { SimpleCachedValue } from "@/lib/cache.ts"

/**
 * Build-in custom element that adds "data-completed" attribute when a content is completed
 * Use the <meta name="user:completed"> to track the list of completed ids
 */
export class HasCompletedElement extends HTMLAnchorElement {
  connectedCallback() {
    if (!this.dataset.id) {
      console.error(
        'The element with is="has-completed" attribute must have a data-id',
      )
      return
    }
    const id = parseInt(this.dataset.id, 10)
    const ids = completedIds.getValue()
    if (ids.includes(id)) {
      this.setAttribute("data-completed", "")
    }
  }
}

const completedIds = new SimpleCachedValue(
  () => window.location.href,
  (): number[] => {
    const meta = document.querySelector("meta[name='user:completed']")
    if (!meta) {
      return []
    }

    return (
      meta
        .getAttribute("content")
        ?.split(",")
        .map((s: string) => parseInt(s, 10)) ?? []
    )
  },
)
