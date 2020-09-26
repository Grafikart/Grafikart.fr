import { jsonFetch } from '/functions/api.js'
import { strToDom } from '/functions/dom.js'

const konamicode = [
  'ArrowUp',
  'ArrowUp',
  'ArrowDown',
  'ArrowDown',
  'ArrowLeft',
  'ArrowRight',
  'ArrowLeft',
  'ArrowRight',
  'b',
  'a'
]
const keys = []

let listenerRegistered = false

export function registerKonami () {
  if (listenerRegistered === true) {
    return
  }
  window.addEventListener('keydown', async function (e) {
    keys.push(e.key)
    while (keys.length > konamicode.length) {
      keys.shift()
    }
    if (keys.toString().indexOf(konamicode) >= 0) {
      try {
        const data = await jsonFetch('/api/badges/gamer/unlock', { method: 'POST' })
        document.body.append(
          strToDom(
            `<badge-unlock name="${data.name}" description="${data.description}" image="${data.image}" theme="${data.theme}"></badge-unlock>`
          )
        )
      } catch (e) {
        console.error(e)
      }
    }
  })

  listenerRegistered = true
}
