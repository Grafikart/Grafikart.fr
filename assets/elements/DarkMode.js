export default class DarkMode {

    constructor() {
        this.btn = document.createElement('button')
        this.mode = this.checkMode()
        this.init()
    }

    checkMode() {
        // Vérfie si l'utilisateur a déjà choisi un thème
        if (localStorage.getItem('theme')) {
            return localStorage.getItem('theme')
        // Sinon on regarde le thème du navigateur / système
        } else if(window.matchMedia("(prefers-color-scheme: dark)").matches) {
            return 'dark'
        // Light par défaut
        } else {
            return 'light'
        }
    }

    inverseMode(mode) {
        return mode === 'dark' ? 'light' : 'dark'
    }

    /**
     * On construit le bouton
     */
    buildBtn() {
        this.btn.classList.add('js-dark')
        this.btn.setAttribute('tabindex', "0")
        this.btn.setAttribute('role', 'button')
        document.body.appendChild(this.btn)

        this.activateMode(this.mode)
    }

    /**
     * @param {string} mode
     */
    activateMode (mode) {
        document.querySelector('body').className = mode
        this.createSwitchBtn(this.inverseMode(this.mode))
    }

   createSwitchBtn (mode) {
        this.btn.setAttribute('title', `Passer en ${mode} mode`)
        this.btn.innerHTML = `<svg class="icon" role="img" aria-label="icone">
        <use xlink:href="sprite.svg#${mode}"></use>
        </svg>`
    }

    handler() {
        this.mode = this.inverseMode(this.mode)
        localStorage.setItem('theme', this.mode)
        this.activateMode(this.mode)
    }

    init() {
        this.buildBtn()
        this.btn.addEventListener('click', e => {
            e.preventDefault()
            this.handler()
        })

        this.btn.addEventListener('keyUp', e => {
            if (e.keyCode === 32 ) {
                e.preventDefault()
                this.handler()
            }
        })
    }
}
