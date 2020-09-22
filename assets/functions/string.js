/**
 * Ajoute des sauts de ligne automatiquement sur une chaine
 *
 * @param {string} str
 * @return {string}
 */
export function nl2br (str) {
  return str.replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1<br>$2')
}
