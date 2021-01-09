import { Switch } from '/elements/Switch.js'
import { isAuthenticated } from '/functions/auth.js'
import { jsonFetchOrFlash } from '/functions/api.js'

export class ThemeSwitcher extends Switch {
  connectedCallback () {
    this.addEventListener('change', e => {
      const themeToRemove = e.currentTarget.checked ? 'light' : 'dark'
      const themeToAdd = e.currentTarget.checked ? 'dark' : 'light'
      document.body.classList.add(`theme-${themeToAdd}`)
      document.body.classList.remove(`theme-${themeToRemove}`)
      if (!isAuthenticated()) {
        localStorage.setItem('theme', themeToAdd)
      } else {
        jsonFetchOrFlash('/api/profil/theme', {
          body: { theme: themeToAdd },
          method: 'POST'
        }).catch(console.error)
      }
    })

    // On lit le theme utilisateur
    if (!isAuthenticated()) {
      const savedTheme = localStorage.getItem('theme')
      // Si l'utilisateur n'a pas déjà de préférence
      if (savedTheme === null) {
        const mq = window.matchMedia('(prefers-color-scheme: dark)')
        this.checked = mq.matches
      } else {
        document.body.classList.add(`theme-${savedTheme}`)
        this.checked = savedTheme === 'dark'
      }
    } else {
      this.checked = document.body.classList.contains('theme-dark')
    }

    super.connectedCallback.bind(this)()
  }

  disconnectedCallback () {
    super.disconnectedCallback.bind(this)()
  }
}
