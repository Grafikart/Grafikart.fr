/**
 * Returns the window height
 */
export function windowHeight(): number {
  return window.innerHeight
}

const uuid = crypto.randomUUID?.() ?? Date.now().toString()

/**
 * Returns true if the window is active or was the last active window
 */
export function isActiveWindow(): boolean {
  if (localStorage) {
    return uuid === localStorage.getItem("windowId")
  }
  return true
}

if (localStorage) {
  localStorage.setItem("windowId", uuid)
  window.addEventListener("focus", () => {
    localStorage.setItem("windowId", uuid)
  })
}
