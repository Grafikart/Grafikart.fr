import { Field } from '/components/Form.jsx'
import { AddressField } from '/components/AddressField.jsx'
import { CountrySelect } from '/components/CountrySelect.jsx'
import { useCallback } from 'preact/hooks'
import { formDataToObj } from '/functions/dom.js'

/**
 * Formulaire permettant de remplir ces informations de facturations
 *
 * @param {function} onSubmit
 * @param {{firstname: string, lastname: string, city: string, postalCode: string, address: string, country: string}} address
 */
export function InvoiceAddress ({ onSubmit, address }) {
  const handleAutocomplete = useCallback(({ countryCode, city, address }) => {
    document.querySelector('#country').value = countryCode.toUpperCase()
    document.querySelector('#city').value = city
    document.querySelector('#address').value = address
  }, [])

  const handleSubmit = e => {
    e.preventDefault()
    onSubmit(formDataToObj(e.target))
  }

  return (
    <form onSubmit={handleSubmit}>
      <div class='grid2'>
        <Field name='firstname' required defaultValue={address.firstname}>
          Prénom
        </Field>
        <Field name='lastname' required defaultValue={address.lastname}>
          Nom
        </Field>
        <div class='full'>
          <Field
            defaultValue={address.address}
            required
            name='address'
            component={AddressField}
            onChange={handleAutocomplete}
          >
            Adresse
          </Field>
        </div>
        <div class='full grid3'>
          <Field name='city' defaultValue={address.city} required>
            Ville
          </Field>
          <Field name='postalCode' defaultValue={address.postalCode} required>
            Code postal
          </Field>
          <Field name='countryCode' required component={CountrySelect} defaultValue={address.countryCode}>
            Pays
          </Field>
        </div>
      </div>

      <hr class='my4' />

      <button class='btn-primary btn-block' type='submit'>
        Passer au paiement
      </button>
    </form>
  )
}
