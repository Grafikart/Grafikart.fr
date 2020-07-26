import { jsonFetch } from '/functions/api.js'

/**
 * Représentation d'un commentaire de l'API
 * @typedef {{id: number, username: string, avatar: string, content: string, createdAt: number, replies: CommentResource[]}} CommentResource
 */

/**
 * @param {string} target
 * @return {Promise<CommentResource[]>}
 */
export async function findAllComments (target) {
  return await jsonFetch(`/api/comments?content=${target}`)
}

/**
 * @param {{target: number, username: ?string, email: ?string, content: string}} data
 * @return {Promise<Object>}
 */
export async function addComment (body) {
  return jsonFetch('/api/comments', {
    method: 'POST',
    body
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

/**
 * @param {int} id
 * @param {string} content
 * @return {Promise<CommentResource>}
 */
export async function updateComment (id, content) {
  return jsonFetch(`/api/comments/${id}`, {
    method: 'PUT',
    body: JSON.stringify({ content })
  })
}
