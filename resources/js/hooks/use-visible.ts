import { useEffect, useRef, useState } from "react"

type UseVisibleOptions = IntersectionObserverInit & {
  once?: boolean
}

export function useVisible<T extends Element>({
  once = false,
  root = null,
  rootMargin,
  threshold,
}: UseVisibleOptions = {}) {
  const ref = useRef<T>(null)
  const [isVisible, setIsVisible] = useState(false)

  useEffect(() => {
    if (!ref.current) {
      return
    }

    const observer = new IntersectionObserver(
      ([entry]) => {
        if (entry.isIntersecting && once) {
          observer.disconnect()
        }
        setIsVisible(entry.isIntersecting)
      },
      {
        root,
        rootMargin,
        threshold,
      },
    )

    observer.observe(ref.current)

    return () => {
      observer.disconnect()
    }
  }, [once, root, rootMargin, threshold])

  return {
    ref,
    isVisible,
  }
}
