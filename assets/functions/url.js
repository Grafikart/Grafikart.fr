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
