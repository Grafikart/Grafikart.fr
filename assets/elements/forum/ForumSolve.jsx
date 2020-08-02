import { useRef, useState } from 'preact/hooks'
import { flash } from '/elements/Alert.js'
import { canManage } from '/functions/auth.js'
import { Loader } from '/components/Loader.jsx'
import { jsonFetch } from '/functions/api.js'
import { closest } from '/functions/dom.js'

/**
 * Bouton pour marquer un message comme solution
 */
export function ForumSolve ({ message, topicAuthor, disabled, parent }) {
  const button = useRef(null)
  const [success, setSuccess] = useState(disabled !== undefined)
  const [loading, setLoading] = useState(false)
  const handleClick = async () => {
    setLoading(true)
    try {
      await jsonFetch(`/api/forum/messages/${message}/solve`, {method: 'post'})
      flash('Le sujet a bien été marqué comme résolu')
      setSuccess(true)
      const messageElement = closest(button.current, '.forum-message')
      if (messageElement) {
        messageElement.classList.add('is-accepted')
      }
    } catch (e) {
      flash('Une erreur serveur est survenue', 'danger')
    }
    setLoading(false)
  }

  if (!canManage(topicAuthor)) {
    parent.remove()
    return null
  }

  return (
    <button
      ref={button}
      className='rounded-button success'
      onClick={handleClick}
      disabled={success}
      title='Répond à ma question !'
    >
      {loading ? <Loader /> : '✓'}
    </button>
  )
}
