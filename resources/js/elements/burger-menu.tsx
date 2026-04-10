import { Drawer } from "@base-ui/react/drawer"
import { useEffect, useMemo, useState } from "react"

type Props = {
  element: HTMLElement
}

export function BurgerMenu(props: Props) {
  const [open, setOpen] = useState(false)
  const items = useMenu()
  const footer = useRightMenu()

  useEffect(() => {
    const onClick = () => {
      setOpen((v) => !v)
    }
    props.element.addEventListener("click", onClick)
    return () => {
      props.element.removeEventListener("click", onClick)
    }
  }, [props.element])

  return (
    <Drawer.Root swipeDirection="right" open={open} onOpenChange={setOpen}>
      <Drawer.Portal>
        <Drawer.Backdrop className="fixed inset-0 bg-overlay z-100 transition-all data-ending-style:opacity-0 data-starting-style:opacity-0" />
        <Drawer.Viewport className="fixed inset-0 z-101 flex items-stretch justify-end overflow-hidden">
          <Drawer.Popup className="border-l max-w-screen w-70 bg-card transition-all data-starting-style:translate-x-full data-ending-style:translate-x-full outline-none">
            <Drawer.Content className="p-4 flex flex-col h-full">
              {items.map((item) => (
                <a
                  key={item.href}
                  href={item.href}
                  dangerouslySetInnerHTML={{
                    __html: item.html,
                  }}
                  className="flex border-b items-center py-2 px-4 gap-2"
                />
              ))}
              <div
                className="mt-auto flex items-center justify-between"
                dangerouslySetInnerHTML={{ __html: footer }}
              />
            </Drawer.Content>
          </Drawer.Popup>
        </Drawer.Viewport>
      </Drawer.Portal>
    </Drawer.Root>
  )
}

function useMenu() {
  return useMemo(() => {
    return Array.from(
      document.querySelectorAll<HTMLAnchorElement>("#navigation a"),
    ).map((a) => ({
      href: a.getAttribute("href") ?? "#",
      html: a.innerHTML,
    }))
  }, [])
}

function useRightMenu() {
  return useMemo(() => {
    return document.getElementById("navigation-right")?.innerHTML ?? ""
  }, [])
}
