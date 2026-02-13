import { useEffect, useEffectEvent } from "react"

type Modifiers = {
  ctrlKey?: boolean
  shiftKey?: boolean
  altKey?: boolean
  metaKey?: boolean
  when?: boolean
}

export function useShortcut(
  key: string,
  modifiers: Modifiers,
  callback: () => void,
): void {
  const onKeyDown = useEffectEvent((e: KeyboardEvent) => {
    const lowerKey = key.toLowerCase()

    if (e.key.toLowerCase() !== lowerKey) {
      return
    }
    if (!!modifiers.ctrlKey !== e.ctrlKey) return
    if (!!modifiers.shiftKey !== e.shiftKey) return
    if (!!modifiers.altKey !== e.altKey) return
    if (!!modifiers.metaKey !== e.metaKey) return

    e.preventDefault()
    callback()
  })
  useEffect(() => {
    if (modifiers.when === false) {
      return
    }

    const lowerKey = key.toLowerCase()
    const eventName = lowerKey === "escape" ? "keyup" : "keydown"

    document.addEventListener(eventName, onKeyDown)

    return () => {
      document.removeEventListener(eventName, onKeyDown)
    }
  }, [key, modifiers.when])
}
