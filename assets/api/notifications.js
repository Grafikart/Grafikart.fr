import { strToDom } from '/functions/dom.js'
import { isAuthenticated } from '/functions/auth.js'
import { jsonFetch } from '/functions/api.js'

let isMercureConnected = false

/**
 * Écoute une notification provenant de mercure
 *
 * @param {string} type
 * @param {function} callback
 * @return {function(): void} Fonction permettant de retirer le listener
 */
export function onNotification (type, callback) {
  connectToMercure()
  const handler = e => callback(e.detail)
  window.addEventListener(type, handler)
  return () => window.removeEventListener(type, handler)
}

/**
 * Charge les notifications (en Ajax) et se connecte au SSE
 */
export async function loadNotifications () {
  // On récupère les dernières notifications et on simule des évènements mercure
  const notifications = await jsonFetch(`/api/notifications?count=15`)
  notifications.reverse()
  notifications.forEach(notification => emitEvent({ type: 'notification', data: notification }))
}

/**
 * Lance la connection au server mercure
 */
function connectToMercure () {
  // Si on n'est pas authentifié ou si on est déjà connecté, on quitte
  if (isMercureConnected === true || !isAuthenticated()) {
    return
  }

  // On se connecte à mercure et émet les évènements au niveau de window
  const url = new URL(window.grafikart.MERCURE_URL)
  url.searchParams.append('topic', '/notifications/{channel}')
  url.searchParams.append('topic', `/notifications/user/${window.grafikart.USER}`)
  const eventSource = new EventSource(url, { withCredentials: true })
  eventSource.onmessage = e => emitEvent(JSON.parse(e.data))
  isMercureConnected = true
}

/**
 * Redirige une notification mercure vers un évènement dans le DOM
 */
function emitEvent ({ type, data }) {
  window.dispatchEvent(
    new CustomEvent(type, {
      detail: data
    })
  )
}

// On se connecte au SSE
if (isMercureConnected === false) {
  const url = new URL(window.grafikart.MERCURE_URL)
  url.searchParams.append('topic', `/badges/user/${window.grafikart.USER}`)
  const eventSource = new EventSource(url, { withCredentials: true })
  eventSource.onmessage = e => {
    console.log(e)
    const badge = JSON.parse(e.data)
    document.body.append(
      strToDom(
        `<badge-unlock name="${badge.name}" description="${badge.description}" image="${badge.image}" theme="${badge.theme}"></badge-unlock>`
      )
    )
  }
}
