import {offsetTop} from '@fn/dom'
import {windowHeight} from '@fn/window'

/**
 * Scroll vers l'éménet en le plaçant au centre de la fenêtre si il n'est pas trop grand
 *
 * @param element
 */
export function scrollTo (element) {
  const elementOffset = offsetTop(element)
  const elementHeight = element.getBoundingClientRect().height
  const viewHeight = windowHeight()
  let top = elementOffset - 100
  if (elementHeight <= viewHeight) {
    top = elementOffset - (viewHeight - elementHeight) / 2
  }
  window.scrollTo({
    top,
    left: 0,
    behavior: 'smooth'
  })
}
