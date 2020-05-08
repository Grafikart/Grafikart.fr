import {jsonFetch} from '@fn/api'

/**
 * Représentation d'un commentaire de l'API
 * @typedef {{id: number, username: string, avatar: string, content: string, createdAt: number, replies: CommentResource[]}} CommentResource
 */

/**
 * @param {string} target
 * @return {Promise<CommentResource[]>}
 */
export async function findAllComments(target) {
  const comments = await jsonFetch('/api/comments?content=' + target)
  const commentsHash = {}
  for (const comment of comments) {
    if (comment.parent === null) {
      comment.replies = []
      commentsHash[comment.id] = comment
    } else {
      commentsHash[comment.parent].replies.push(comment)
    }
  }
  return {count: comments.length, comments: Object.values(commentsHash)}
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

/**
 * @param {int} id
 * @param {string} content
 * @return {Promise<CommentResource>}
 */
export async function updateComment (id, content) {
  return jsonFetch(`/api/comments/${id}`, {
    method: 'PUT',
    body: JSON.stringify({content})
  })
}
