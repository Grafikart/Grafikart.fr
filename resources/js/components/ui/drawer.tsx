import { DrawerPreview as DrawerBase } from "@base-ui/react/drawer"
import { XIcon } from "lucide-react"
import type { ComponentProps, PropsWithChildren } from "react"
import { cn } from "@/lib/utils.ts"

type Side = "right" | "bottom"

type Props = PropsWithChildren<{
  trigger: ComponentProps<typeof DrawerBase.Trigger>["render"]
  open?: boolean
  onOpenChange?: (v: boolean) => void
  className?: string
  side?: Side
}>

export function Drawer({
  children,
  trigger,
  className,
  side = "right",
  ...props
}: Props) {
  return (
    <DrawerBase.Root swipeDirection={swipeDirection(side)} {...props}>
      <DrawerBase.Trigger render={trigger} />
      <DrawerBase.Portal>
        <DrawerBase.Backdrop className="fixed inset-0 min-h-dvh bg-overlay z-1000" />
        <DrawerBase.Viewport
          className={cn("fixed inset-0 flex z-1001", viewportStyles[side])}
        >
          <DrawerBase.Popup
            className={cn(
              "bg-card p-4 overflow-y-auto overscroll-contain touch-auto transition data-swiping:transition-none relative",
              popupStyles[side],
            )}
          >
            <DrawerBase.Content className={cn("mx-auto w-full", className)}>
              <DrawerBase.Close className="block mb-4 ml-auto">
                <XIcon className="size-4" />
              </DrawerBase.Close>
              {children}
            </DrawerBase.Content>
          </DrawerBase.Popup>
        </DrawerBase.Viewport>
      </DrawerBase.Portal>
    </DrawerBase.Root>
  )
}

function swipeDirection(side: Side) {
  switch (side) {
    case "bottom":
      return "down" as const
  }
  return "right" as const
}

const viewportStyles: Record<Side, string> = {
  right: "items-stretch justify-end",
  bottom: "items-end justify-stretch",
}

const popupStyles: Record<Side, string> = {
  right:
    "h-full data-ending-style:translate-x-full data-starting-style:translate-x-full",
  bottom:
    "w-full rounded-t-lg data-ending-style:translate-y-full data-starting-style:translate-y-full max-h-[calc(100dvh-5rem)]",
}
