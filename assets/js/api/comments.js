import {jsonFetch} from '../functions/api'

/**
 * Représentation d'un commentaire de l'API
 * @typedef {{id: number, username: string, avatar: string, content: string, createdAt: number}} CommentResource
 */

/**
 * @param {string} target
 * @return {Promise<CommentResource[]>}
 */
export async function findAllComments(target) {
  return jsonFetch('/api/comments?content=' + target)
}

/**
 * @param {{target: number, username: ?string, email: ?string, content: string}} data
 * @return {Promise<Object>}
 */
export async function addComment(data) {
  return jsonFetch('/api/comments', {
    method: 'POST',
    body: JSON.stringify(data)
  })
}

/**
 * @param {int} id
 * @return {Promise<null>}
 */
export async function deleteComment (id) {
  return jsonFetch(`/api/comments/${id}`, {
    method: 'DELETE'
  })
}
