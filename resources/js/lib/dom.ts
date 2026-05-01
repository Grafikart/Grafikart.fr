export function onPageLoad(cb: () => void) {
  cb()
}

export function siblings<T extends HTMLElement>(el: T): T[] {
  return Array.from(el.parentElement!.children).filter(
    (child) => child !== el,
  ) as T[]
}

export function $(selector: string) {
  return document.querySelector(selector)
}

export function $$(selector: string) {
  return Array.from(document.querySelectorAll(selector))
}

export function strToDom<T extends HTMLElement>(str: string): T {
  return document.createRange().createContextualFragment(str).firstChild as T
}

type ViewTransitionCallback = () => void | Promise<void>

export function withViewTransition(
  callback: ViewTransitionCallback,
): Promise<void> {
  if (!document.startViewTransition) {
    return Promise.resolve(callback())
  }

  return document.startViewTransition(callback).ready
}

/**
 * Convert a form's data to a JSON object, supporting dot notation for nested keys.
 */
export function formToObject(form: HTMLFormElement): Record<string, unknown> {
  const data = new FormData(form)
  const result: Record<string, unknown> = {}

  for (const [key, value] of data.entries()) {
    const keys = key.split(".")
    let current: Record<string, unknown> = result

    for (let i = 0; i < keys.length - 1; i++) {
      if (
        !(keys[i] in current) ||
        typeof current[keys[i]] !== "object" ||
        current[keys[i]] === null
      ) {
        current[keys[i]] = /^\d+$/.test(keys[i + 1]) ? [] : {}
      }
      current = current[keys[i]] as Record<string, unknown>
    }

    current[keys[keys.length - 1]] = value
  }

  return result
}

/**
 * Add event listener to all elements matching the selector.
 */
export function onAll<T extends Element>(
  base: Element | Document,
  selector: string,
  eventName: Parameters<T["addEventListener"]>[0],
  callback: (e: Event & { currentTarget: T }) => void,
) {
  base.querySelectorAll<T>(selector).forEach((el) => {
    // @ts-expect-error callback is typed with a stronger specification on currentTarget
    el.addEventListener(eventName, callback)
  })
}

/**
 * Load script asynchronously
 */
const loadedJs = new Set<string>() // Cache the loaded scripts
export function loadJs<T>(path: string): Promise<T> {
  return new Promise<T>((resolve, reject) => {
    if (loadedJs.has(path)) {
      resolve(window as T)
      return
    }
    const e = document.createElement("script")
    e.src = path
    e.onload = function () {
      loadedJs.add(path)
      resolve(window as T)
    }
    e.onerror = function () {
      reject(new Error(`Failed to load script: ${path}`))
    }
    document.head.appendChild(e)
  })
}
