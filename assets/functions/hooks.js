import { useEffect, useState } from 'preact/hooks'
import { ApiError, jsonFetch } from '/functions/api.js'
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
 * @return {{data: Object|null, fetch: fetch, loading: boolean, done: boolean}}
 */
export function useJsonFetchOrFlash (url, params = {}) {
  const [state, setState] = useState({
    loading: false,
    data: null,
    done: false
  })
  const fetch = async function () {
    setState(s => ({ ...s, loading: true }))
    try {
      const response = await jsonFetch(url, params)
      setState(s => ({ ...s, loading: false, data: response, done: true }))
    } catch (e) {
      if (e instanceof ApiError) {
        flash(e.name, 'danger', 4)
      } else {
        flash(e, 'danger', 4)
      }
    }
    setState(s => ({ ...s, loading: false }))
  }
  return { ...state, fetch }
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
