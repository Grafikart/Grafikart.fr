import { jsonFetch } from '/functions/api.js'
import { slideUpAndRemove } from '/functions/animation.js'
import LoaderOverlay from '/elements/LoaderOverlay.js'
import { FloatingAlert } from '/elements/Alert.js'
import { closest } from '/functions/dom.js'

/**
 * Bouton pour appeler une URL avec la méthode DELETE et masquer le parent en cas de retour
 */
export class AjaxDelete extends HTMLElement {
  connectedCallback () {
    this.addEventListener('click', async e => {
      e.preventDefault()

      if (!confirm('Voulez vous vraiment effectuer cette action ?')) {
        return
      }

      // On affiche le loader
      const target = this.getAttribute('target')
      const parent = target ? closest(this, this.getAttribute('target')) : this.parentNode
      const loader = new LoaderOverlay()
      parent.style.position = 'relative'
      parent.appendChild(loader)

      // On fait l'appel
      try {
        await jsonFetch(this.getAttribute('url'), { method: 'DELETE' })
        loader.hide()
        await slideUpAndRemove(parent)
      } catch (e) {
        loader.hide()
        document.body.appendChild(new FloatingAlert({ message: e.detail }))
      }
    })
  }
}
