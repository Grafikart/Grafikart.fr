import { useEffect, useRef, useState } from 'preact/hooks'
import { useAsyncEffect, useClickOutside, usePrepend } from '/functions/hooks.js'
import { Icon } from '/components/Icon.jsx'
import { SlideIn } from '/components/Animation/SlideIn.jsx'
import { isAuthenticated, lastNotificationRead } from '/functions/auth.js'
import { Spinner } from '/components/Animation/Spinner.jsx'
import { loadNotifications } from '/api/notifications.js'

const OPEN = 0
const CLOSE = 1

function countUnread (notifications, notificationReadAt) {
  return notifications.filter(({ createdAt }) => {
    return notificationReadAt < createdAt
  }).length
}

/**
 * Contient les notifications
 *
 * @return {*}
 * @constructor
 */
export function Notifications () {
  // Hooks
  const [state, setState] = useState(CLOSE)
  const [notifications, pushNotification] = usePrepend()
  const [notificationReadAt, setNotificationReadAt] = useState(lastNotificationRead())
  const [loading, setLoading] = useState(true)

  // Méthodes
  const openMenu = e => {
    e.preventDefault()
    setState(OPEN)
  }
  const closeMenu = () => {
    setNotificationReadAt(new Date())
    setState(CLOSE)
  }

  // On charge les notification la première fois
  useAsyncEffect(async () => {
    if (isAuthenticated()) {
      await loadNotifications()
      setLoading(false)
    }
  }, [])

  // On écoute l'arrivé de nouveaux évènement depuis l'API ou le SSE
  useEffect(() => {
    const onNotification = e => {
      pushNotification(e.detail)
    }
    window.addEventListener('gnotification', onNotification)
    return () => {
      window.removeEventListener('gnotification', onNotification)
    }
  }, [pushNotification])

  // Le système de notification ne fonction que pour les utilisateurs
  if (!isAuthenticated()) return null

  return (
    <>
      <button onClick={openMenu}>
        <Icon name='bell' />
      </button>
      <Badge count={countUnread(notifications, notificationReadAt)} />
      <SlideIn className='notifications' show={state === OPEN}>
        <Popup
          loading={loading}
          onClickOutside={closeMenu}
          notifications={notifications}
          notificationReadAt={notificationReadAt}
        />
      </SlideIn>
    </>
  )
}

/**
 * Badge contenant le nombre de notifications
 */
function Badge ({ count }) {
  return count > 0 && <span className='notification-badge'>{count}</span>
}

/**
 * Popup contenant les notifications
 */
function Popup ({ notifications = [], onClickOutside = () => {}, loading = false, notificationReadAt, ...props }) {
  const ref = useRef()

  useClickOutside(ref, onClickOutside)

  return (
    <div ref={ref} {...props}>
      <div className='notifications_title'>Nouveaux messages</div>
      <div className='notifications_body'>
        {loading && <Spinner />}
        {notifications.map(n => (
          <Notification key={n.id} notificationReadAt={notificationReadAt} {...n} />
        ))}
        <a href='/notifications' className='notifications_footer'>
          Toutes les notifications
        </a>
      </div>
    </div>
  )
}

/**
 * Représente une notification
 */
function Notification ({ url, message, createdAt, notificationReadAt }) {
  const isRead = notificationReadAt > createdAt
  const className = `notifications_item ${isRead ? 'is-read' : ''}`
  return (
    <a href={url} className={className}>
      <div className='notifications_text'>
        <p>{message}</p>
      </div>
    </a>
  )
}
