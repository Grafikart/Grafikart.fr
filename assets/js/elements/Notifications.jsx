import {Fragment} from 'preact'
import {useEffect, useRef, useState} from 'preact/hooks'
import {useClickOutside, usePrepend} from '@fn/hooks'
import {Icon} from '@comp/Icon'
import {SlideIn} from '@comp/Animation/SlideIn'
import {isAuthenticated, lastNotificationRead} from '@fn/auth'
import {Spinner} from '@comp/Animation/Spinner'
import {loadNotifications} from '../api/notifications'
import preactCustomElement from '@fn/preact'

const OPEN = 0
const CLOSE = 1

function countUnread (notifications, notificationReadAt) {
  return notifications.filter(({createdAt}) => {
    return notificationReadAt < createdAt
  }).length
}

/**
 * Contient les notifications
 *
 * @return {*}
 * @constructor
 */
function Notifications () {
  // Le système de notification ne fonction que pour les utilisateurs
  if (!isAuthenticated()) return null

  // Hooks
  const [state, setState] = useState(OPEN)
  const [notifications, pushNotification] = usePrepend()
  const [notificationReadAt, setNotificationReadAt] = useState(lastNotificationRead())
  const [loading, setLoading] = useState(true)

  // Méthods
  const openMenu = e => {
    e.preventDefault()
    setState(OPEN)
  }
  const closeMenu = () => {
    setNotificationReadAt(new Date())
    setState(CLOSE)
  }

  // On charge les notification la première fois
  useEffect(async () => {
    await loadNotifications()
    setLoading(false)
  }, [])

  useEffect(() => {
    const onNotification = e => {
      pushNotification(e.detail)
    }
    window.addEventListener('gnotification', onNotification)
    return () => {
      window.removeEventListener('gnotification', onNotification)
    }
  }, [pushNotification])

  return <Fragment>
    <button onClick={openMenu}>
      <Icon name="bell"/>
    </button>
    <Badge count={countUnread(notifications, notificationReadAt)}/>
    <SlideIn className="notifications"  show={state === OPEN}>
      <Popup
        loading={loading}
        onClickOutside={closeMenu}
        notifications={notifications}
        notificationReadAt={notificationReadAt}
      />
    </SlideIn>
  </Fragment>
}

/**
 * Badge contenant le nombre de notifications
 */
function Badge ({count}) {
  return (count > 0 && <span className="notification-badge">{count}</span>)
}

/**
 * Popup contenant les notifications
 */
function Popup ({notifications = [], onClickOutside = () => {}, loading = false, notificationReadAt, ...props}) {
  const ref = useRef()

  useClickOutside(ref, onClickOutside)

  return <div ref={ref} {...props}>
    <div className="notifications_title">Nouveaux messages</div>
    <div className="notifications_body">
      {loading && <Spinner/>}
      {notifications.map(n => <Notification notificationReadAt={notificationReadAt} {...n} />)}
      <a href="/notifications" className="notifications_footer">Toutes les notifications</a>
    </div>
  </div>
}

/**
 * Représente une notification
 */
function Notification ({url, message, createdAt, notificationReadAt}) {
  const isRead = notificationReadAt > createdAt
  const className = `notifications_item ${isRead ? 'is-read' : ''}`
  return <a href={url} className={className}>
    <div className="notifications_text">
      <p>{message}</p>
    </div>
  </a>
}

preactCustomElement(Notifications, 'site-notifications')
