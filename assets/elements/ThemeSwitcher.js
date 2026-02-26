import { isAuthenticated } from '/functions/auth.js'
import { jsonFetchOrFlash } from '/functions/api.js'
import { cookie } from '/functions/cookie.js'

export class ThemeSwitcher extends HTMLElement {
  connectedCallback () {
    this.classList.add('theme-switcher')
    this.innerHTML = `
        <input type="checkbox" is="input-switch" id="theme-switcher" aria-label="Changer de thème">
        <label for="theme-switcher">
          <svg class="icon icon-moon">
            <use href="/sprite.svg#moon"></use>
          </svg>
          <svg class="icon icon-sun">
            <use href="/sprite.svg#sun"></use>
          </svg>
        </label>`
    const input = this.querySelector('input')
    input.addEventListener('change', e => {
      const themeToRemove = e.currentTarget.checked ? 'light' : 'dark'
      const themeToAdd = e.currentTarget.checked ? 'dark' : 'light'

      const applyTheme = () => {
        document.body.classList.add(`theme-${themeToAdd}`)
        document.body.classList.remove(`theme-${themeToRemove}`)
      }

      if (
        !document.startViewTransition ||
        window.matchMedia('(prefers-reduced-motion: reduce)').matches
      ) {
        applyTheme()
      } else {
        const { top, left, width, height } = e.currentTarget.getBoundingClientRect()
        const x = left + width / 2
        const y = top + height / 2
        const right = window.innerWidth - x
        const bottom = window.innerHeight - y
        const radius = Math.hypot(Math.max(x, right), Math.max(y, bottom))

        const transition = document.startViewTransition(() => {
          applyTheme()
        })

        transition.ready.then(() => {
          document.documentElement.animate(
            {
              clipPath: [
                `circle(0px at ${x}px ${y}px)`,
                `circle(${radius}px at ${x}px ${y}px)`
              ]
            },
            {
              duration: 500,
              easing: 'ease-in-out',
              pseudoElement: '::view-transition-new(root)'
            }
          )
        })
      }

      if (!isAuthenticated()) {
        cookie('theme', themeToAdd, { expires: 7 })
      } else {
        jsonFetchOrFlash('/api/profil/theme', {
          body: { theme: themeToAdd },
          method: 'POST'
        }).catch(console.error)
      }
    })

    // On lit le theme utilisateur
    if (!isAuthenticated()) {
      const savedTheme = cookie('theme')
      // Si l'utilisateur n'a pas déjà de préférence
      if (savedTheme === null) {
        const mq = window.matchMedia('(prefers-color-scheme: dark)')
        input.checked = mq.matches
      } else {
        document.body.classList.add(`theme-${savedTheme}`)
        input.checked = savedTheme === 'dark'
      }
    } else if (document.body.classList.contains('theme-dark')) {
      input.checked = true
    } else if (document.body.classList.contains('theme-light')) {
      input.checked = false
    } else {
      input.checked = window.matchMedia('(prefers-color-scheme: dark)').matches
    }
  }
}
