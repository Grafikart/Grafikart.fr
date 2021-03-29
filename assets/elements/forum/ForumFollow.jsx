import { isAuthenticated } from '/functions/auth.js'
import { Button } from '/components/Button.jsx'
import { Icon } from '/components/Icon.jsx'
import { useJsonFetchOrFlash } from '/functions/hooks.js'
import { classNames } from '/functions/dom.js'

/**
 * @param {string} topic
 * @param {boolean|null} subscribed
 */
export function ForumFollow ({ topic, subscribed = null }) {
  const { loading, fetch, data } = useJsonFetchOrFlash(`/api/forum/topics/${topic}/follow`, {
    method: 'POST'
  })
  const isSubscribed = data?.subscribed ?? subscribed === ''

  if (!isAuthenticated()) {
    return null
  }

  const className = classNames(isSubscribed ? 'btn-danger' : 'btn-secondary', 'topic-follow', 'relative')

  return (
    <Button onClick={() => fetch()} loading={loading} className={className} size='small'>
      {!loading && <Icon name='bell' />}
      {isSubscribed ? `Se désabonner` : `S'abonner`}
    </Button>
  )
}
