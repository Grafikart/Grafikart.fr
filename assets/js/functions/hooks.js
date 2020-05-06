import {useState} from 'preact/hooks'

/**
 * Hook permettant d'alterner une valeur
 *
 * @param {boolean} value
 * @return {unknown[]}
 */
export function useToggle (initialValue = null) {
  const [value, setValue] = useState(initialValue)
  return [
    value,
    () => setValue(!value)
  ]
}

export function usePush (initialValue = []) {
  const [value, setValue] = useState(initialValue)
  return [
    value,
    (item) => setValue([...value, item])
  ]
}
