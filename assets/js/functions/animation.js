/**
 * Masque un élément avec un effet de repli
 * @param {HTMLElement} element
 * @param {Number} duration
 * @returns {Promise<boolean>}
 */
export function slideUp (element, duration = 500) {
  return new Promise(function (resolve, reject) {
    element.style.height = element.offsetHeight + 'px'
    element.style.transitionProperty = `height, margin, padding`
    element.style.transitionDuration = duration + 'ms'
    element.offsetHeight // eslint-disable-line no-unused-expressions
    element.style.overflow = 'hidden'
    element.style.height = 0
    element.style.paddingTop = 0
    element.style.paddingBottom = 0
    element.style.marginTop = 0
    element.style.marginBottom = 0
    window.setTimeout(function () {
      element.style.display = 'none'
      element.style.removeProperty('height')
      element.style.removeProperty('padding-top')
      element.style.removeProperty('padding-bottom')
      element.style.removeProperty('margin-top')
      element.style.removeProperty('margin-bottom')
      element.style.removeProperty('overflow')
      element.style.removeProperty('transition-duration')
      element.style.removeProperty('transition-property')
      resolve(element)
    }, duration)
  })
}

/**
 * Affiche un élément avec un effet de dépliement
 * @param {HTMLElement} element
 * @param {Number} duration
 * @returns {Promise<boolean>}
 */
export function slideDown(element, duration = 500)
{
  return new Promise(function (resolve, reject) {
    element.style.removeProperty('display')
    let display = window.getComputedStyle(element).display
    if (display === 'none') display = 'block'
    element.style.display = display
    let height = element.offsetHeight
    element.style.overflow = 'hidden'
    element.style.height = 0
    element.style.paddingTop = 0
    element.style.paddingBottom = 0
    element.style.marginTop = 0
    element.style.marginBottom = 0
    element.offsetHeight // eslint-disable-line no-unused-expressions
    element.style.transitionProperty = `height, margin, padding`
    element.style.transitionDuration = duration + 'ms'
    element.style.height = height + 'px'
    element.style.removeProperty('padding-top')
    element.style.removeProperty('padding-bottom')
    element.style.removeProperty('margin-top')
    element.style.removeProperty('margin-bottom')
    window.setTimeout(function () {
      element.style.removeProperty('height')
      element.style.removeProperty('overflow')
      element.style.removeProperty('transition-duration')
      element.style.removeProperty('transition-property')
      resolve(element)
    }, duration)
  })
}
