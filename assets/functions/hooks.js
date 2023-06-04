import { useEffect, useState, useCallback, useRef } from 'preact/hooks'
import { ApiError, jsonFetch } from '/functions/api.js'
import { flash } from '/elements/Alert.js'
import { strToDom } from '/functions/dom.js'

/**
 * Alterne une valeur
 */
export function useToggle (initialValue = null) {
  const [value, setValue] = useState(initialValue)
  return [value, useCallback(() => setValue(v => !v), [])]
}

/**
 * Valeur avec la possibilité de push un valeur supplémentaire
 */
export function usePrepend (initialValue = []) {
  const [value, setValue] = useState(initialValue)
  return [
    value,
    useCallback(item => {
      setValue(v => [item, ...v])
    }, [])
  ]
}

/**
 * Hook d'effet pour détecter le clique en dehors d'un élément
 */
export function useClickOutside (ref, cb) {
  useEffect(() => {
    if (cb === null) {
      return
    }
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
  const fetch = useCallback(
    async (localUrl, localParams) => {
      setState(s => ({ ...s, loading: true }))
      try {
        const response = await jsonFetch(localUrl || url, localParams || params)
        setState(s => ({ ...s, loading: false, data: response, done: true }))
        return response
      } catch (e) {
        if (e instanceof ApiError) {
          flash(e.name, 'danger', 4)
        } else {
          flash(e, 'danger', 4)
        }
      }
      setState(s => ({ ...s, loading: false }))
    },
    [url, params]
  )
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

export const PROMISE_PENDING = 0
export const PROMISE_DONE = 1
export const PROMISE_ERROR = -1

/**
 * Décore une promesse et renvoie son état
 */
export function usePromiseFn (fn) {
  const [state, setState] = useState(null)
  const resetState = useCallback(() => {
    setState(null)
  }, [])

  const wrappedFn = useCallback(
    async (...args) => {
      setState(PROMISE_PENDING)
      try {
        await fn(...args)
        setState(PROMISE_DONE)
      } catch (e) {
        setState(PROMISE_ERROR)
        throw e
      }
    },
    [fn]
  )

  return [state, wrappedFn, resetState]
}

/**
 * Hook permettant de détecter quand un élément devient visible à l'écran
 *
 * @export
 * @param {DOMNode reference} node
 * @param {Boolean} once
 * @param {Object} [options={}]
 * @returns {object} visibility
 */
export function useVisibility (node, once = true, options = {}) {
  const [visible, setVisibilty] = useState(false)
  const isIntersecting = useRef()

  const handleObserverUpdate = entries => {
    const ent = entries[0]

    if (isIntersecting.current !== ent.isIntersecting) {
      setVisibilty(ent.isIntersecting)
      isIntersecting.current = ent.isIntersecting
    }
  }

  const observer = once && visible ? null : new IntersectionObserver(handleObserverUpdate, options)

  useEffect(() => {
    const element = node instanceof HTMLElement ? node : node.current

    if (!element || observer === null) {
      return
    }

    observer.observe(element)

    return function cleanup () {
      observer.unobserve(element)
    }
  })

  return visible
}

let favIconBadge = null

export function useNotificationCount (n) {
  useAsyncEffect(async () => {
    if (favIconBadge === null) {
      if (n === 0) {
        return
      }
      await import('favicon-badge')
      favIconBadge = strToDom(`<favicon-badge src="/favicon.ico" badge="true" badgeSize="6"/>`)
      document.head.appendChild(favIconBadge)
      return
    }
    favIconBadge.setAttribute('badge', n === 0 ? 'false' : 'true')
  }, [n])
}

export function useRefSync(v) {
  const ref = useRef(v)
  ref.current = v
  return ref
}
