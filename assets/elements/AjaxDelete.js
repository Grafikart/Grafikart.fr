import { jsonFetch } from '/functions/api.js'
import { closest } from '/functions/dom.js'

/**
 * Bouton pour appeler une URL avec la méthode DELETE et masquer le parent en cas de retour
 */
export class AjaxDelete extends HTMLElement {
  connectedCallback () {
    this.addEventListener('click', async e => {
      e.preventDefault()

      if (this.getAttribute('noconfirm') === null && !confirm('Voulez vous vraiment effectuer cette action ?')) {
        return
      }

      // On affiche le loader
      const target = this.getAttribute('target')
      const parent = target ? closest(this, this.getAttribute('target')) : this.parentNode
      const loader = document.createElement('loader-overlay')
      parent.style.position = 'relative'
      parent.appendChild(loader)

      // On fait l'appel
      try {
        await jsonFetch(this.getAttribute('url'), { method: 'DELETE' })
        loader.hide()
        parent.remove()
      } catch (e) {
        loader.hide()
        const alert = document.createElement('alert-floating')
        alert.innerHTML = e.detail
        document.body.appendChild(alert)
      }
    })
  }
}
