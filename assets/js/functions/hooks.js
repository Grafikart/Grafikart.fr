import {useEffect, useState} from 'preact/hooks'
import {jsonFetch} from '@fn/api'
import {flash} from '@el/Alert'

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
    const escCb = e => {
      if (e.key === 'Escape' && ref.current) { cb() }
    }
    const clickCb = e => {
      if (ref.current && !ref.current.contains(e.target)) {
        cb()
      }
    }
    document.addEventListener('click', clickCb)
    document.addEventListener('keyup', escCb)
    return function cleanup() {
      document.removeEventListener('click', clickCb)
      document.removeEventListener('keyup', escCb)
    }
  }, [ref, cb])
}

/**
 * Focus le premier champs dans l'élément correspondant à la ref
 * @param {boolean} focus
 */
export function useAutofocus(ref, focus) {
  useEffect(() => {
    if (focus && ref.current) {
      const input = ref.current.querySelector('input, textarea')
      if (input) {
        input.focus()
      }
    }
  }, [focus, ref])
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

/**
 * Hook faisant un appel fetch et flash en cas d'erreur / succès
 *
 * @param {string} url
 * @param {object} params
 * @return {(boolean|*[]|{}|load)[]}
 */
export function useJsonFetchOrFlash (url, success, params = {}) {
  const [loading, setLoading] = useState(true)
  const [data, setData] = useState(null)
  const fetch = async function () {
    setLoading(true)
    try {
      const response = await jsonFetch(url, params)
      setData(response)
    } catch (e) {
      flash(e, 'error')
    }
    setLoading(false)
  }
  return [loading, data, fetch]
}
