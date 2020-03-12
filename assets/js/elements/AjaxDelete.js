import {jsonFetch} from '@fn/api'
import {slideUpAndRemove} from '@fn/animation'
import LoaderOverlay from '@el/LoaderOverlay'
import {FloatingAlert} from '@el/Alert'
import {closest} from '@fn/dom'

/**
 * Bouton pour appeler une URL avec la méthode DELETE et masquer le parent en cas de retour
 */
class AjaxDelete extends HTMLElement {

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
        await jsonFetch(this.getAttribute('url'), {method: 'DELETE'})
        loader.hide()
        await slideUpAndRemove(parent)
      } catch (e) {
        loader.hide()
        document.body.appendChild(new FloatingAlert({message: e.detail}))
      }

    })
  }

}

customElements.define('ajax-delete', AjaxDelete)
