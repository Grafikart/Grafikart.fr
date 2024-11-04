import { flash } from '/elements/Alert.js'

export class ForumOnboarding extends HTMLElement {
  #step = 0

  onSubmit = e => {
    const form = e.currentTarget
    if (this.#step > 1) {
      return
    }
    e.preventDefault()
    const answer = new FormData(form)
      .get('challenge')
      .split('\n')
      .map(line => line.trim())
      .filter(line => line !== '')
      .join('\n')
    if (
      (this.#step === 0 && answer.includes('**vérifier**')) ||
      (this.#step === 1 && answer.includes('```\nconst') && answer.includes(')\n```'))
    ) {
      this.#step++
      form.classList.add('is-done')
      const nextForm = form.nextElementSibling
      nextForm.classList.remove('is-disabled')
      nextForm.querySelector('button[type="submit"]').removeAttribute('disabled')
    } else {
      flash(
        this.#step === 0 ? 'Vous devez mettre le mot "vérifier" en gras' : 'Vous devez entourer le code de ```',
        'danger'
      )
    }
  }

  connectedCallback () {
    this.querySelectorAll('form').forEach(form => {
      form.addEventListener('submit', this.onSubmit)
    })
  }
}
