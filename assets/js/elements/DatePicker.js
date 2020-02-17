import flatpickr from 'flatpickr'
import 'flatpickr/dist/flatpickr.min.css'
import { French } from 'flatpickr/dist/l10n/fr.js'
import 'flatpickr/dist/themes/material_blue.css'

/**
 * @property {flatpickr} flatpickr
 */
export default class DatePicker extends HTMLInputElement {

  connectedCallback () {
    this.flatpickr = flatpickr(this, {
      locale: French,
      altFormat: 'd F Y H:i',
      dateFormat: "Y-m-d H:i:s",
      altInput: true,
      enableTime: true,
    })
  }

  disconnectedCallaback () {
    this.flatpickr.destroy()
  }

}

global.customElements.define('date-picker', DatePicker, {extends: 'input'})
