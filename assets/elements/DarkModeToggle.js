import { Switch } from '/elements/Switch.js'
import { jsonFetch } from '/functions/api.js'

const bodyClass = 'dark'

export class DarkModeToggle extends Switch {

  connectedCallback () {
    super.connectedCallback()
    this.addEventListener('change', async function () {
      if (this.checked) {
        document.body.classList.add(bodyClass)
      } else {
        document.body.classList.remove(bodyClass)
      }
      await jsonFetch('/api/dark', {method: 'post'})
    })
  }

}
