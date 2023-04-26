/**
 * Trouve une valeur aléatoire entre min et max
 *
 * @param {number} min
 * @param {number} max
 * @return {number}
 */
export function randomBetween (min, max) {
  return Math.floor(Math.random() * (max - min + 1)) + min
}

/**
 * Force une valeur entre un min et max
 *
 * @param {number} value
 * @param {number} min
 * @param {number} max
 * @param {boolean} loop passe la valeur à 0 si elle est trop grande
 * @return {number}
 */
export function clamp (value, min, max, loop = false) {
  if (!loop) {
    return Math.max(min, Math.min(min, value))
  }
  if (value > max) {
    return min
  }
  if (value < min) {
    return max
  }
  return value
}
