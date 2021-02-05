import { throttle } from '/functions/timers.js'
let $header = document.querySelector('.header')
let currentTop = 0
let previousTop = 0
let scrolling = false
let scrollDelta = 20
let scrollOffset = $header.offsetHeight

document.addEventListener('turbolinks:load', () => {
  $header = document.querySelector('.header')
  scrollOffset = $header.offsetHeight
})

// Les différents états possible du header
const FIXED = 0
const HIDDEN = 1
const DEFAULT = 2
let state = DEFAULT

/**
 * Fonction de changement d'état du header
 *
 * @param {number} newState
 */
function setState (newState) {
  // Le header n'a pas changé d'état
  if (newState === state) {
    return
  }

  if (newState === HIDDEN) {
    $header.classList.add('is-hidden')
  } else if (newState === FIXED) {
    $header.classList.remove('is-hidden')
    $header.classList.add('is-fixed')
  } else if (newState === DEFAULT) {
    $header.classList.remove('is-hidden')
    $header.classList.remove('is-fixed')
  }

  console.log(newState)
  state = newState
}

let autoHideHeader = function () {
  currentTop = document.documentElement.scrollTop
  // Opacité sur le header
  if (currentTop > $header.offsetHeight) {
    if (currentTop - previousTop > scrollDelta && currentTop > scrollOffset) {
      setState(HIDDEN)
    } else if (previousTop - currentTop > scrollDelta) {
      setState(FIXED)
    }
  } else {
    setState(DEFAULT)
  }
  /**
  // Masquage / affichage
  if (previousTop - currentTop > scrollDelta) {
    $header.classList.remove('is-hidden')
  } else if (currentTop - previousTop > scrollDelta && currentTop > scrollOffset) {
    $header.classList.add('is-hidden')
  }
   **/
  previousTop = currentTop
  scrolling = false
}

/**
 * Enregistre le comportement du header (fixed au scoll)
 * @return {function(): void}
 */
export function registerHeader () {
  const scrollListener = throttle(function () {
    if (!scrolling) {
      scrolling = true
      window.requestAnimationFrame(autoHideHeader)
    }
  }, 100)
  window.addEventListener('scroll', scrollListener)
  return () => {
    window.removeEventListener('scroll', scrollListener)
  }
}
