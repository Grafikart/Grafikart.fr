import { atom, getDefaultStore, useAtom } from "jotai"
import {
  useCallback,
  useEffect,
  useEffectEvent,
  useMemo,
  useState,
} from "react"
import { apiFetch } from "@/hooks/use-api-fetch.ts"
import { subscribeToMercure } from "@/lib/mercure.ts"
import { isActiveWindow } from "@/lib/window.ts"
import type { NotificationData } from "@/types"
import { userId } from "@/lib/auth.ts"

const notificationsAtom = atom<NotificationData[] | null>(null)
let isSubscribed = false

export function startSubscription(): void {
  if (isSubscribed) {
    return
  }
  const store = getDefaultStore()
  isSubscribed = true
  subscribeToMercure(["notification", `notification/${userId()}`], (event) => {
    if (event.type === "NotificationCreatedEvent") {
      store.set(notificationsAtom, (prev) => [
        event.notification,
        ...(prev ?? []),
      ])
      playNotification()
    } else {
      console.error("Unmanaged mercure event", event)
    }
  })
}

export function useNotifications(initialReadAt: number) {
  const [notifications, setNotifications] = useAtom(notificationsAtom)
  const fetched = notifications !== null
  const [readAt, setReadAt] = useState(initialReadAt)

  const fetchNotifications = useEffectEvent(() => {
    if (!fetched) {
      apiFetch<NotificationData[]>("/api/notifications").then(setNotifications)
    }
  })

  const markAsRead = useCallback(() => {
    setReadAt(Date.now() / 1000)
    apiFetch("/api/notifications/read", { method: "post" }).catch(console.error)
  }, [])

  // Fetch notifications instantly
  useEffect(() => {
    startSubscription()
    fetchNotifications()
  }, [])

  const unread = useMemo(() => {
    if (!notifications) {
      return 0
    }
    return notifications.filter(
      (n) => new Date(n.date).getTime() / 1000 > readAt,
    ).length
  }, [notifications, readAt])

  return {
    notifications,
    unread,
    markAsRead,
  }
}

let audio = null as null | HTMLAudioElement
function playNotification() {
  if (!isActiveWindow()) {
    return
  }
  if (audio === null) {
    audio = new Audio("/notification.mp3")
  }
  audio.volume = 0.5
  try {
    audio.play()
  } catch {
    // Audi may not play if the user did not interact with the page
  }
}
