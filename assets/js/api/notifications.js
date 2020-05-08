import {jsonFetch} from '@fn/api'

function emitEvent(notification) {
  notification.createdAt = new Date(notification.createdAt)
  window.dispatchEvent(new CustomEvent('gnotification', {
    detail: notification
  }))
}

/**
 * Charge les notifications (en Ajax) et se connecte au SSE
 */
export async function loadNotifications () {
  // On récupère les dernières notifications en AJAX
  const notifications = await fetchAll(4)
  notifications.reverse()
  notifications.forEach(emitEvent)

  // On se connecte au SSE
  const url = new URL(window.grafikart.MERCURE_URL);
  url.searchParams.append('topic', '/notifications/{channel}');
  const eventSource = new EventSource(url, {withCredentials: true});
  eventSource.onmessage = e => emitEvent(JSON.parse(e.data))
  return notifications
}

export async function fetchAll (count) {
  return await jsonFetch(`/api/notifications?count=${count}`)
}
