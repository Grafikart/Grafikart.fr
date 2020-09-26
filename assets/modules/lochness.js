import { ApiError, HTTP_UNPROCESSABLE_ENTITY, jsonFetch } from '/functions/api.js'
import { strToDom } from '/functions/dom.js'
import { isAuthenticated } from '/functions/auth.js'

window.lochness = async () => {
  if (!isAuthenticated()) {
    console.warn('Vous devez être connecté pour trouver Nessie :(')
    return
  }

  try {
    const data = await jsonFetch('/api/badges/lochness/unlock', { method: 'POST' })
    document.body.append(
      strToDom(
        `<badge-unlock name="${data.name}" description="${data.description}" image="${data.image}" theme="${data.theme}"></badge-unlock>`
      )
    )
  } catch (e) {
    if (e instanceof ApiError) {
      if (e.status === HTTP_UNPROCESSABLE_ENTITY) {
        console.warn('Vous avez déjà trouvé Nessie')
      }
    }
  }
}
