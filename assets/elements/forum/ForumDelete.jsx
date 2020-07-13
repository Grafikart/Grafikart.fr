import { useJsonFetchAndFlash } from '/functions/hooks.js'
import { closest } from '/functions/dom.js'
import { flash } from '/elements/Alert.js'
import { canManage } from '/functions/auth.js'
import { Loader } from '/components/Loader.jsx'

export function ForumDelete ({ message, topic, owner }) {
  let endpoint = null
  if (message) {
    endpoint = `/api/forum/messages/${message}`
  } else if (topic) {
    endpoint = `/api/forum/topics/${topic}`
  } else {
    throw new Error('Impossible de charger le composant de suppression')
  }

  // On prépare les hooks
  const { loading: deleteLoading, fetch: deleteFetch } = useJsonFetchAndFlash(endpoint, { method: 'DELETE' })

  // Handler
  const handleDeleteClick = async e => {
    if (!confirm('Voulez vous vraiment supprimer ce message ?')) {
      return
    }
    const message = closest(e.target, '.forum-message')
    await deleteFetch()
    flash('Votre message a bien été supprimé')
    message.remove()
  }

  // L'utilisateur ne peut pas intervenir sur ce sujet
  if (!canManage(owner)) {
    return null
  }

  return (
    <>
      {' '}
      -{' '}
      <button className='text-danger' onClick={handleDeleteClick} disabled={deleteLoading}>
        {deleteLoading && <Loader style={{ width: 12, marginRight: 5 }} />}
        Supprimer
      </button>
    </>
  )
}
