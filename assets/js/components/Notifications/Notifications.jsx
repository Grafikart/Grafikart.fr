import {Fragment} from 'preact'
import {useRef, useState} from 'preact/hooks'
import {useClickOutside, useJsonFetch} from '@fn/hooks'
import {Icon} from '../Icon'
import {SlideIn} from '../Animation/SlideIn'
import {isAuthenticated, lastNotificationRead} from '@fn/auth'
import {Spinner} from '../Animation/Spinner'
import {fetchAll} from '../../api/notifications'

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
export function Notifications () {
  // Le système de notification ne fonction que pour les utilisateurs
  if (!isAuthenticated()) return null

  const [state, setState] = useState(CLOSE)
  const [notificationReadAt, setNotificationReadAt] = useState(lastNotificationRead())
  const openMenu = e => {
    e.preventDefault()
    setState(OPEN)
  }
  const closeMenu = () => {
    setNotificationReadAt(new Date())
    setState(CLOSE)
  }
  const [loading, notifications, error] = useJsonFetch(fetchAll, [4])

  return <Fragment>
    <button onClick={openMenu}>
      <Icon name="bell" />
    </button>
    <Badge count={countUnread(notifications, notificationReadAt)}/>
    <SlideIn className="notifications"  show={state === OPEN}>
      <Popup
        onClickOutside={closeMenu}
        loading={loading}
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
