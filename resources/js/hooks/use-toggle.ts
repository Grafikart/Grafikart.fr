import { useCallback, useState } from "react"

export function useToggle(
  initial = false,
): [boolean, () => void, (v: boolean) => void] {
  const [state, setState] = useState(initial)
  return [
    state,
    useCallback(() => {
      setState((v) => !v)
    }, []),
    setState,
  ] as const
}
