import { Button, PrimaryButton } from '/components/Button.jsx'
import { Modal } from '/components/Modal.jsx'
import { useToggle } from '/functions/hooks.js'
import { Field } from '/components/Form.jsx'
import { AddressField } from '/components/AddressField.jsx'
import { Stepper, Step } from '/components/Stepper.jsx'

export function PremiumButton ({ children }) {
  const [modal, toggleModal] = useToggle(false)

  const handleAutocomplete = ({ country, city, address }) => {
    document.querySelector('#country').value = country
    document.querySelector('#city').value = city
    document.querySelector('#address').value = address
  }

  return (
    <>
      <PrimaryButton onClick={toggleModal}>{children}</PrimaryButton>
      {modal && (
        <Modal className='premium-modal'>
          <Stepper step={1} class='mb4'>
            <Step>Facturations</Step>
            <Step>Méthode de paiement</Step>
            <Step>Récapitulatif</Step>
          </Stepper>

          <div className='grid2'>
            <Field name='firstname'>Prénom</Field>
            <Field name='lastname'>Nom</Field>
            <div className='full'>
              <Field name='address' component={AddressField} onChange={handleAutocomplete}>
                Adresse
              </Field>
            </div>
            <Field name='city'>Ville</Field>
            <Field name='country'>Pays</Field>
          </div>

          <hr class='my4' />

          <Button class='btn-primary btn-block'>Devenir premium</Button>
        </Modal>
      )}
    </>
  )
}
