import { useEffect, useState } from 'preact/hooks'
import { jsonFetch } from '/functions/api.js'
import { flash } from '/elements/Alert.js'

/**
 * Alterne une valeur
 */
export function useToggle (initialValue = null) {
  const [value, setValue] = useState(initialValue)
  return [value, () => setValue(!value)]
}

/**
 * Valeur avec la possibilité de push un valeur supplémentaire
 */
export function usePrepend (initialValue = []) {
  const [value, setValue] = useState(initialValue)
  return [
    value,
    item => {
      setValue(v => [item, ...v])
    }
  ]
}

/**
 * Hook d'effet pour détecter le clique en dehors d'un élément
 */
export function useClickOutside (ref, cb) {
  useEffect(() => {
    const escCb = e => {
      if (e.key === 'Escape' && ref.current) {
        cb()
      }
    }
    const clickCb = e => {
      if (ref.current && !ref.current.contains(e.target)) {
        cb()
      }
    }
    document.addEventListener('click', clickCb)
    document.addEventListener('keyup', escCb)
    return function cleanup () {
      document.removeEventListener('click', clickCb)
      document.removeEventListener('keyup', escCb)
    }
  }, [ref, cb])
}

/**
 * Focus le premier champs dans l'élément correspondant à la ref
 * @param {boolean} focus
 */
export function useAutofocus (ref, focus) {
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
 * Hook faisant un appel fetch et flash en cas d'erreur / succès
 *
 * @param {string} url
 * @param {object} params
 * @return {(boolean|*[]|{}|load)[]}
 */
export function useJsonFetchAndFlash (url, params = {}) {
  const [loading, setLoading] = useState(false)
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
  return { loading, data, fetch }
}

/**
 * useEffect pour une fonction asynchrone
 */
export function useAsyncEffect (fn, deps = []) {
  /* eslint-disable */
  useEffect(() => {
    fn()
  }, deps)
  /* eslint-enable */
}
