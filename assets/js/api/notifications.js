import {jsonFetch} from '../functions/api'

export async function fetchAll (count) {
  const response = await jsonFetch(`/api/notifications?count=${count}`)
  return response.map(notification => {
    notification.createdAt = new Date(notification.createdAt)
    return notification
  })
}
