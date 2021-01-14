import { ApiError, HTTP_FORBIDDEN, HTTP_UNPROCESSABLE_ENTITY, jsonFetch } from '/functions/api.js'
import { strToDom } from '/functions/dom.js'
import { isAuthenticated } from '/functions/auth.js'
import { onNotification } from '/api/notifications.js'
import { isActiveWindow } from '/functions/window.js'
import { flash } from '/elements/Alert.js'

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

/**
 * Enregistre la détection du konamiCode
 */
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
      await jsonFetch('/api/badges/gamer/unlock', { method: 'POST' })
        .catch(e => {
          if (e instanceof ApiError && e.status === HTTP_UNPROCESSABLE_ENTITY) {
            flash('Vous avez déjà débloqué ce badge', 'error')
          }
        })
    }
  })

  listenerRegistered = true
}

/**
 * Enregistre l'alerte de déblocage de badege
 */
let cleanBadgeListener
export function registerBadgeAlert () {
  if (cleanBadgeListener) {
    cleanBadgeListener()
  }
  cleanBadgeListener = onNotification('badge', data => {
    if (!isActiveWindow()) {
      return
    }
    document.body.append(
      strToDom(
        `<badge-unlock name="${data.name}" description="${data.description}" image="${data.image}" theme="${data.theme}"></badge-unlock>`
      )
    )
  })
}

/**
 * Enregistre la fonction pour débloquer le monstre du loch-ness
 **/
window.nessie = () => {
  if (!isAuthenticated()) {
    console.warn('Vous devez être connecté pour trouver Nessie :(')
    return ''
  }
  jsonFetch('/api/badges/lochness/unlock', { method: 'POST' })
    .then(() => {
      console.log(
"%c                        _   _       _a_a      \n" +
"            _   _     _{.`=`.}_    {/ ''\\_    \n" +
"      _    {.`'`.}   {.'  _  '.}  {|  ._oo)   \n" +
"     { \\  {/ .-. \\} {/  .' '.  \\} {/  |       \n" +
"~^~^~`~^~`~^~`~^~`~^~^~`^~^~`^~^~^~^~^~^~`^~~`", `color: #FFF; background-color: #2234ae;
background-image: linear-gradient(315deg, #2234ae 0%, #191714 74%);`)
    })
    .catch((e) => {
      if (e instanceof ApiError) {
        if (e.status === HTTP_UNPROCESSABLE_ENTITY) {
          console.warn('Vous avez déjà trouvé Nessie :(')
        } else if (e.status === HTTP_FORBIDDEN) {
          console.warn("Vous n'avez pas l'autorisation de débloquer ce badge :(")
        }
      }
    })
  return '';
}
