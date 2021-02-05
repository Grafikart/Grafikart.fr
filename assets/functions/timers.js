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
 * Throttle un callback
 */
export function throttle(callback, delay) {
  let last;
  let timer;
  return function () {
    let context = this;
    let now = +new Date();
    let args = arguments;
    if (last && now < last + delay) {
      // le délai n'est pas écoulé on reset le timer
      clearTimeout(timer);
      timer = setTimeout(function () {
        last = now;
        callback.apply(context, args);
      }, delay);
    } else {
      last = now;
      callback.apply(context, args);
    }
  };
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
