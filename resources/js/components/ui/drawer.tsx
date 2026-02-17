import { DrawerPreview as DrawerBase } from "@base-ui/react/drawer"
import { XIcon } from "lucide-react"
import type { ComponentProps, PropsWithChildren, ReactNode } from "react"
import { Button } from "@/components/ui/button.tsx"
import { cn } from "@/lib/utils.ts"

type Side = "right" | "bottom"

type Props = PropsWithChildren<{
  trigger: ComponentProps<typeof DrawerBase.Trigger>["render"]
  open?: boolean
  onOpenChange?: (v: boolean) => void
  className?: string
  side?: Side
  width?: number
  actions?: ReactNode
}>

export function Drawer({
  children,
  trigger,
  className,
  side = "right",
  width,
  actions,
  ...props
}: Props) {
  return (
    <DrawerBase.Root swipeDirection={swipeDirection(side)} {...props}>
      <DrawerBase.Trigger render={trigger} />
      <DrawerBase.Portal>
        <DrawerBase.Backdrop className="fixed inset-0 min-h-dvh bg-overlay z-1000 transition data-sarting-style:opacity-0 data-ending-style:opacity-0" />
        <DrawerBase.Viewport
          className={cn("fixed inset-0 flex z-1001", viewportStyles[side])}
        >
          <DrawerBase.Popup
            className={cn(
              "outline-none bg-card p-4 overflow-y-auto overscroll-contain touch-auto transition-all data-swiping:transition-none relative",
              "[--bleed:2rem]",
              "ease-[cubic-bezier(0.32,0.72,0,1)] duration-500",
              "data-nested-drawer-open:h-[calc(var(--drawer-frontmost-height)+var(--bleed))] data-nested-drawer-open:w-[calc(100%-var(--bleed)*2)] data-nested-drawer-open:mx-auto data-nested-drawer-open:max-h-none",
              "data-nested-drawer-open:after:absolute data-nested-drawer-open:after:inset-0 data-nested-drawer-open:after:bg-overlay/50",
              popupStyles[side],
            )}
          >
            <DrawerBase.Content
              className={cn("mx-auto w-full", className)}
              style={width ? { maxWidth: width } : undefined}
            >
              <div className="flex items-center mb-2 -mx-2">
                {actions}
                <DrawerBase.Close
                  className="flex ml-auto"
                  render={<Button variant="ghost" size="icon" />}
                >
                  <XIcon className="size-4" />
                </DrawerBase.Close>
              </div>
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
