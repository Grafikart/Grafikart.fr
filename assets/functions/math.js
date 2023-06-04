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
 * Force une valeur entre min et max
 *
 * @param {number} value
 * @param {number} min
 * @param {number} max
 * @return {number}
 */
export function clamp(value, min, max) {
  if (value < min) {
    return min
  }
  if (value > max) {
    return max;
  }
  return value
}
