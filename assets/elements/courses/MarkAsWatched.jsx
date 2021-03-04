/**
 * @property {HTMLVideoElement} video
 * @property {number} timeBeforeTracking
 * @property {number} lastTickTime
 * @property {contentId} string
 */
import { isAuthenticated } from '/functions/auth.js'
import { useJsonFetchOrFlash } from '/functions/hooks.js'
import { SecondaryButton } from '/components/Button.jsx'
import { useEffect } from 'preact/hooks'
import { flash } from '/elements/Alert.js'
import { Icon } from '/components/Icon.jsx'

export function MarkAsWatched ({ contentId }) {
  const { loading, done, fetch } = useJsonFetchOrFlash(`/api/progress/${contentId}/1000`, {
    method: 'post'
  })

  useEffect(() => {
    if (done) {
      flash('Le tutoriel a bien été marqué comme vu')
    }
  }, [done])

  if (!isAuthenticated() || done) {
    return null
  }

  return (
    <SecondaryButton loading={loading} class='btn-secondary' onClick={() => fetch()}>
      <Icon name='eye' />
      Marquer comme vu
    </SecondaryButton>
  )
}
