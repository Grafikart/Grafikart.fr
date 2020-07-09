/**
 * Debounce un callback
 */
export function debounce(func, wait, immediate) {
  var timeout;
  return function() {
    var context = this, args = arguments;
    clearTimeout(timeout);
    timeout = setTimeout(function() {
      timeout = null;
      if (!immediate) func.apply(context, args);
    }, wait);
    if (immediate && !timeout) func.apply(context, args);
  };
}

/**
 * Version asynchrone du timeout
 *
 * @param {number} duration
 */
export function wait (duration) {
  return new Promise(function (resolve) {
    window.setTimeout(resolve, duration)
  })
}
