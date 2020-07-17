import { useJsonFetchOrFlash } from '/functions/hooks.js'
import { isAuthenticated } from '/functions/auth.js'
import { SecondaryButton } from '/components/Button.jsx'
import { Icon } from '/components/Icon.jsx'
import { useEffect } from 'preact/hooks'
import { flash } from '/elements/Alert.js'

/**
 * Génère un bouton permettant de lire tous les sujets du forum
 * @param {Object} props
 * @param {string} props.endpoint
 */
export function ForumRead ({ endpoint }) {
  const { loading, done, fetch } = useJsonFetchOrFlash(endpoint, { method: 'POST' })
  useEffect(() => {
    if (done) {
      flash('Tous les sujets ont été marqués comme lu')
      Array.from(document.querySelectorAll('.forum-topic')).forEach(topic => topic.classList.add('is-read'))
    }
  }, [done])

  if (!isAuthenticated() || done) {
    return null
  }

  return (
    <SecondaryButton size='small' loading={loading} onClick={fetch}>
      {!loading && <Icon name='eye' />} Marquer tous comme lus
    </SecondaryButton>
  )
}
