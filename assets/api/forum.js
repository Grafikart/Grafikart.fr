/**
 * Trouve le endpoint à contacter en fonction de l'élément à modifier
 *
 * @param {string|number|null} message
 * @param {string|number|null} topic
 * @return {string}
 */
export function resolveEndpoint ({ message, topic }) {
  if (message) {
    return `/api/forum/messages/${message}`
  } else if (topic) {
    return `/api/forum/topics/${topic}`
  }
  throw new Error("Impossible de charger le composant d'édition")
}
