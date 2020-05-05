import {useState} from 'preact/hooks'

/**
 * Hook permettant d'alterner une valeur
 *
 * @param {boolean} value
 * @return {unknown[]}
 */
export function useToggle (value) {
  const [v, setV] = useState(value)
  return [
    v,
    () => setV(!v)
  ]
}
