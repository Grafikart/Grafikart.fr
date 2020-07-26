/**
 * Debounce un callback
 */
export function debounce (func, wait, immediate) {
  let timeout
  return function (...args) {
    clearTimeout(timeout)
    timeout = setTimeout(() => {
      timeout = null
      if (!immediate) func.apply(this, args)
    }, wait)
    if (immediate && !timeout) func.apply(this, args)
  }
}

/**
 * Version asynchrone du timeout
 *
 * @param {number} duration
 */
export function wait (duration) {
  return new Promise(resolve => {
    window.setTimeout(resolve, duration)
  })
}
