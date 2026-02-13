import { Menu } from "@base-ui/react/menu"
import { type ComponentProps, useEffect, useState } from "react"
import { Spinner } from "@/components/ui/spinner.tsx"
import { useNotifications } from "@/hooks/use-notifications.ts"
import { formatRelative } from "@/lib/date.ts"
import { cn } from "@/lib/utils.ts"
import type { NotificationData } from "@/types"

type Props = {
  element: HTMLElement
  readAt: number
}

export function NotificationsMenu(props: Props) {
  const [open, setOpen] = useState(false)
  const { notifications, unread, markAsRead } = useNotifications(props.readAt)

  // Add trigger on the parent element
  useEffect(() => {
    const onClick = () => setOpen(true)
    props.element.addEventListener("click", onClick)
    return () => {
      props.element.removeEventListener("click", onClick)
    }
  }, [props.element])

  // Mark everything as read when the notification is open
  useEffect(() => {
    props.element.classList.toggle("text-primary", open)
    if (open) {
      markAsRead()
    }
  }, [open, props.element, markAsRead])

  return (
    <Menu.Root open={open} onOpenChange={setOpen}>
      {unread > 0 && (
        <div className="absolute -top-1 -right-1 grid size-4 place-items-center rounded-full bg-destructive text-white text-xs">
          {unread}
        </div>
      )}
      <Menu.Trigger
        className="absolute inset-0 cursor-pointer outline-none"
        aria-label="Voir les notifications"
      />
      <Menu.Portal className="z-1000">
        <Menu.Positioner sideOffset={8}>
          <Menu.Popup className="z-1000 w-85 rounded-sm border bg-card text-sm outline-none transition-all data-ending-style:scale-90 data-starting-style:scale-90 data-ending-style:opacity-0 data-starting-style:opacity-0">
            <Menu.Arrow>
              <ArrowSvg />
            </Menu.Arrow>
            <div className="border-b py-2 text-center text-muted">
              Nouveaux messages
            </div>
            <div className="max-h-85 overflow-auto">
              {notifications === null ? (
                <Spinner className="mx-auto my-3" />
              ) : (
                notifications.map((notification) => (
                  <NotificationItem
                    key={notification.url}
                    notification={notification}
                    readAt={props.readAt}
                  />
                ))
              )}
            </div>
            <Menu.Item
              className="block border-t p-2 text-center hover:bg-list-hover"
              render={<a href="/notifications" />}
            >
              Toutes les notifications
            </Menu.Item>
          </Menu.Popup>
        </Menu.Positioner>
      </Menu.Portal>
    </Menu.Root>
  )
}

function NotificationItem({
  notification,
  readAt,
}: {
  notification: NotificationData
  readAt: number
}) {
  const [read, setRead] = useState(
    () => new Date(notification.date).getTime() / 1000 <= readAt,
  )

  return (
    <Menu.Item
      render={<a href={notification.url} />}
      className={cn(
        "relative block border-b p-4 pr-7 last:border-b-0 hover:bg-list-hover focus-visible:ring focus-visible:ring-primary [&_strong]:text-foreground-title",
        !read && "bg-primary/5",
      )}
      data-unread={!read || undefined}
      onPointerEnter={() => setRead(true)}
    >
      {!read && (
        <span className="absolute top-1/2 right-3 -translate-y-1/2">
          <span className="absolute size-2 animate-ping rounded-full bg-primary" />
          <span className="relative block size-2 rounded-full bg-primary" />
        </span>
      )}
      <div dangerouslySetInnerHTML={{ __html: notification.message }} />
      <span className="text-muted text-xs">
        {formatRelative(notification.date)}
      </span>
    </Menu.Item>
  )
}

function ArrowSvg(props: ComponentProps<"svg">) {
  return (
    <svg
      width="20"
      height="10"
      viewBox="0 0 20 10"
      fill="none"
      className="absolute -top-2.5 left-1/2 -translate-x-2.5 text-card"
      {...props}
    >
      <path
        d="M9.66437 2.60207L4.80758 6.97318C4.07308 7.63423 3.11989 8 2.13172 8H0V10H20V8H18.5349C17.5468 8 16.5936 7.63423 15.8591 6.97318L11.0023 2.60207C10.622 2.2598 10.0447 2.25979 9.66437 2.60207Z"
        fill="currentColor"
      />
      <path
        d="M8.99542 1.85876C9.75604 1.17425 10.9106 1.17422 11.6713 1.85878L16.5281 6.22989C17.0789 6.72568 17.7938 7.00001 18.5349 7.00001L15.89 7L11.0023 2.60207C10.622 2.2598 10.0447 2.2598 9.66436 2.60207L4.77734 7L2.13171 7.00001C2.87284 7.00001 3.58774 6.72568 4.13861 6.22989L8.99542 1.85876Z"
        fill="currentColor"
        className="text-border"
      />
      <path
        d="M10.3333 3.34539L5.47654 7.71648C4.55842 8.54279 3.36693 9 2.13172 9H0V8H2.13172C3.11989 8 4.07308 7.63423 4.80758 6.97318L9.66437 2.60207C10.0447 2.25979 10.622 2.2598 11.0023 2.60207L15.8591 6.97318C16.5936 7.63423 17.5468 8 18.5349 8H20V9H18.5349C17.2998 9 16.1083 8.54278 15.1901 7.71648L10.3333 3.34539Z"
        fill="currentColor"
      />
    </svg>
  )
}
