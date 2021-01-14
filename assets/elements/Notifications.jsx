import { useEffect, useRef, useState } from "preact/hooks";
import { useAsyncEffect, useClickOutside, useNotificationCount, usePrepend } from "/functions/hooks.js";
import { Icon } from "/components/Icon.jsx";
import { SlideIn } from "/components/Animation/SlideIn.jsx";
import { isAuthenticated, lastNotificationRead } from "/functions/auth.js";
import { Spinner } from "/components/Animation/Spinner.jsx";
import { loadNotifications, onNotification } from "/api/notifications.js";
import { jsonFetch } from "/functions/api.js";

const OPEN = 0;
const CLOSE = 1;
let notificationsCache = []
let notificationsLoaded = false

function countUnread(notifications, notificationReadAt) {
  return notifications.filter(({ createdAt }) => {
    return notificationReadAt < Date.parse(createdAt);
  }).length;
}

/**
 * Contient les notifications
 *
 * @return {*}
 * @constructor
 */
export function Notifications() {
  // Hooks
  const [state, setState] = useState(CLOSE);
  const [notifications, pushNotification] = usePrepend(notificationsCache);
  const [notificationReadAt, setNotificationReadAt] = useState(lastNotificationRead());
  const [loading, setLoading] = useState(!notificationsLoaded);
  const unreadCount = countUnread(notifications, notificationReadAt);
  useNotificationCount(unreadCount);
  notificationsCache = notifications

  // Méthodes
  const openMenu = (e) => {
    e.preventDefault();
    setState(OPEN);
    if (unreadCount > 0) {
      setNotificationReadAt(new Date());
      jsonFetch("/api/notifications/read", { method: "post" }).catch(console.error);
    }
  };
  const closeMenu = () => {
    setState(CLOSE);
  };

  // On charge les notification la première fois
  useAsyncEffect(async () => {
    if (isAuthenticated() && notificationsLoaded === false) {
      await loadNotifications();
      setLoading(false);
      notificationsLoaded = true
    }
  }, []);

  // On écoute l'arrivé de nouveaux évènement depuis l'API ou le SSE
  useEffect(() => onNotification("notification", pushNotification), [pushNotification]);

  // On écoute quand les messages sont marqués comme lu
  useEffect(() => {
    return onNotification("markAsRead", () => {
      setNotificationReadAt(new Date());
    });
  }, []);

  // Le système de notification ne fonction que pour les utilisateurs
  if (!isAuthenticated()) return null;

  return (
    <>
      <button onClick={openMenu}>
        <Icon name="bell" />
      </button>
      <Badge count={unreadCount} />
      <SlideIn className="notifications" show={state === OPEN}>
        <Popup
          loading={loading}
          onClickOutside={closeMenu}
          notifications={notifications}
          notificationReadAt={notificationReadAt}
        />
      </SlideIn>
    </>
  );
}

/**
 * Badge contenant le nombre de notifications
 */
function Badge({ count }) {
  return count > 0 && <span className="notification-badge">{count}</span>;
}

/**
 * Popup contenant les notifications
 */
function Popup({ notifications = [], onClickOutside = () => {}, loading = false, notificationReadAt, ...props }) {
  const ref = useRef();

  useClickOutside(ref, onClickOutside);

  return (
    <div ref={ref} {...props}>
      <div className="notifications_title">
        Nouveaux messages
        <button aria-label="Fermer" onClick={onClickOutside}>
          <Icon name="cross" />
        </button>
      </div>
      <div className="notifications_body">
        {loading && <Spinner />}
        {notifications.map((n) => (
          <Notification key={n.id} notificationReadAt={notificationReadAt} {...n} />
        ))}
      </div>
      <a href="/notifications" className="notifications_footer">
        Toutes les notifications
      </a>
    </div>
  );
}

/**
 * Représente une notification
 */
function Notification({ url, message, createdAt, notificationReadAt }) {
  const isRead = notificationReadAt > createdAt;
  const className = `notifications_item ${isRead ? "is-read" : ""}`;
  const time = Date.parse(createdAt) / 1000;
  // eslint-disable-next-line react/no-danger
  return (
    <a href={url} className={className}>
      <div dangerouslySetInnerHTML={{ __html: message }} />
      <small class="text-muted">
        <time-ago time={time} />
      </small>
    </a>
  );
}
