/**
 * Fonction qui permet de répercuter la progression de l'utilisateur sur le DOM
 *
 * @param {{[int]: number}} progress
 */
export function showHistory (progress) {
  /** @var {NodeListOf<HTMLElement>} elements **/
  const elements = document.querySelectorAll('[data-history]')
  elements.forEach(element => {
    // On récupère le % de progression correspondant à l'id du contenu
    const p = progress[element.dataset.history]
    if (p === undefined) {
      return
    }
    // Si un bouton play, on met à jour la progression
    if (element.tagName === 'PLAY-BUTTON') {
      element.setAttribute('progress', p)

      // Sinon on ajoute la class "is-completed" aux éléments qui ont été terminés
    } else if (p === 100) {
      element.classList.add('is-completed')
    }
  })
}
