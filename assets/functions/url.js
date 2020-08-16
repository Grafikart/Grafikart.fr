import Turbolinks from 'turbolinks'

/**
 * @param {object} obj
 * @return {URLSearchParams}
 */
export function objToSearchParams (obj) {
  if (obj === undefined || obj === null) {
    return new URLSearchParams()
  }
  const params = new URLSearchParams()
  Object.keys(obj).forEach(k => {
    params.append(k, obj[k])
  })
  return params
}

/**
 * Redirect to a specific url using turbolink
 */
export function redirect (url) {
  return new Promise((resolve) => {
    const onLoad = function () {
      resolve()
      document.removeEventListener('turbolinks:load', onLoad)
    }
    document.addEventListener('turbolinks:load', onLoad)
    Turbolinks.visit(url)
  })
}
