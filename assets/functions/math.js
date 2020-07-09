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
