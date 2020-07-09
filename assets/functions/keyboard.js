/**
 * Filtre un listener pour ne le lancer que lors d'une pression sur Entrer
 *
 * @param {function(KeyboardEvent)} callback
 * @return {function(KeyboardEvent)}
 */
export function enterKeyListener (callback) {
  return function (e) {
    if (e.key === 'Enter') {
      callback(e)
    }
  }
}
