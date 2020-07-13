import flatpickr from 'flatpickr'
import 'flatpickr/dist/flatpickr.min.css'
import '/css/libs/flatpickr.scss'

/**
 * @property {flatpickr} flatpickr
 */
export default class DatePicker extends HTMLInputElement {
  connectedCallback () {
    this.flatpickr = flatpickr(this, {
      locale: {
        firstDayOfWeek: 1,
        weekdays: {
          shorthand: ['dim', 'lun', 'mar', 'mer', 'jeu', 'ven', 'sam'],
          longhand: ['dimanche', 'lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi']
        },
        months: {
          shorthand: ['janv', 'févr', 'mars', 'avr', 'mai', 'juin', 'juil', 'août', 'sept', 'oct', 'nov', 'déc'],
          longhand: [
            'janvier',
            'février',
            'mars',
            'avril',
            'mai',
            'juin',
            'juillet',
            'août',
            'septembre',
            'octobre',
            'novembre',
            'décembre'
          ]
        },
        ordinal (nth) {
          if (nth > 1) {
            return ''
          }
          return 'er'
        },
        rangeSeparator: ' au ',
        weekAbbreviation: 'Sem',
        scrollTitle: 'Défiler pour augmenter la valeur',
        toggleTitle: 'Cliquer pour basculer',
        time_24hr: true
      },
      altFormat: 'd F Y H:i',
      dateFormat: 'Y-m-d H:i:s',
      altInput: true,
      enableTime: true,
      defaultHour: this.getAttribute('hour')
    })
  }

  disconnectedCallback () {
    this.flatpickr.destroy()
  }
}

customElements.define('date-picker', DatePicker, { extends: 'input' })
