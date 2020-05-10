import {useEffect, useState} from 'preact/hooks'
import {jsonFetch} from '@fn/api'

/**
 * Alterne une valeur
 */
export function useToggle (initialValue = null) {
  const [value, setValue] = useState(initialValue)
  return [
    value,
    () => setValue(!value)
  ]
}

/**
 * Valeur avec la possibilité de push un valeur supplémentaire
 */
export function usePush (initialValue = []) {
  let [value, setValue] = useState(initialValue)
  return [
    value,
    (item) => {
      setValue((v) => [...v, item])
    }
  ]
}

/**
 * Valeur avec la possibilité de push un valeur supplémentaire
 */
export function usePrepend (initialValue = []) {
  let [value, setValue] = useState(initialValue)
  return [
    value,
    (item) => {
      setValue((v) => [item, ...v])
    }
  ]
}

/**
 * Hook d'effet pour détecter le clique en dehors d'un élément
 */
export function useClickOutside(ref, cb) {
  useEffect(() => {
    if (!ref.current) {
      return
    }
    const escCb = e => {
      if (e.key === 'Escape') {
        cb()
      }
    }
    const stopPropagation = e => e.stopPropagation()
    document.addEventListener('click', cb)
    document.addEventListener('keyup', escCb)
    ref.current && ref.current.addEventListener('click', stopPropagation)
    return function cleanup() {
      document.removeEventListener('click', cb)
      document.removeEventListener('keyup', escCb)
      ref.current && ref.current.addEventListener('click', stopPropagation)
    }
  }, [])
}

/**
 * Hook pour un appel ajax
 *
 * @param url l'URL a appeler ou la méthode à utiliser
 * @param params les paramètres de fetch ou un tableau représentant les paramètres de la méthode
 */
export function useJsonFetch (url, params = {}, autofetch = true) {
  const [loading, setLoading] = useState(true)
  const [data, setData] = useState([])
  const [error, setError] = useState({})
  const [loaded, setLoaded] = useState(false)
  const load = function () {
    setLoaded(true)
    setLoading(true)
    let response = null
    if (typeof url !== 'string') {
      response = url.call(this, ...params)
    } else {
      response = jsonFetch(url, params)
    }
    response.then(setData).catch(setError).then(() => setLoading(false))
  }
  if(autofetch) {
    if (loaded === false) {
      load()
    }
  }
  return [
    loading, data, error, load
  ]
}
