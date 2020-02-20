const ko = Math.pow(2, 10)

function ceil (n, decimals) {
  return Math.ceil(n * Math.pow(10, decimals)) / Math.pow(10, decimals)
}

/**
 * Convertit une taille de fichier en valeur humaine
 * @param {string} size
 */
export function human (size) {
  let k = size / ko
  let unit = 'k'
  if (k > ko) {
    k = k / ko
    unit = 'M'
  }
  k = ceil(k, k > 10 ? 0 : 1)
  return `${k}${unit}`
}
