/**
 * Vérifie si l'utilisateur est admin
 *
 * @return {boolean}
 */
export function isAdmin () {
  return window.grafikart.ADMIN === true
}

/**
 * Vérifie si l'utilisateur est connecté
 *
 * @return {boolean}
 */
export function isAuthenticated () {
  return window.grafikart.USER !== null
}

/**
 * Vérifie si l'utilisateur connecté correspond à l'id passé en paramètre
 *
 * @param {number} userId
 * @return {boolean}
 */
export function canManage (userId) {
  if (isAdmin()) {
    return true
  }
  if (!userId) {
    return false
  }
  return window.grafikart.USER === userId
}
