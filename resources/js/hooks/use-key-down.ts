import { useEffect, useState } from "react"

/**
 * Custom hook to detect if a specific key is currently pressed down.
 */
export function useKeyDown(key: string) {
  const [down, setDown] = useState(false)

  useEffect(() => {
    const onKeyDown = (e: KeyboardEvent) => {
      if (e.key === key) {
        setDown(true)
        document.body.removeEventListener("keydown", onKeyDown)
      }
    }
    const onKeyUp = (e: KeyboardEvent) => {
      if (e.key === key) {
        setDown(false)
        document.body.addEventListener("keydown", onKeyDown)
      }
    }

    document.body.addEventListener("keydown", onKeyDown)
    document.body.addEventListener("keyup", onKeyUp)

    return () => {
      document.body.removeEventListener("keydown", onKeyDown)
      document.body.removeEventListener("keyup", onKeyUp)
    }
  }, [key])

  return down
}
