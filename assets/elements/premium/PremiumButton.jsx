import { Button, PrimaryButton } from '/components/Button.jsx'
import { Modal } from '/components/Modal.jsx'
import { useToggle } from '/functions/hooks.js'
import { Field } from '/components/Form.jsx'
import { AddressField } from '/components/AddressField.jsx'
import { Stepper, Step } from '/components/Stepper.jsx'
import { CountryField } from '/components/CountryField.jsx'
import { Slider } from '/components/Slider.jsx'
import { useCallback, useState } from 'preact/hooks'
import confetti from 'canvas-confetti'
import { PaymentMethods } from '/elements/premium/PaymentMethods.jsx'

export function PremiumButton ({ children, plan, price, duration }) {
  const [modal, toggleModal] = useToggle(false)
  const [step, setStep] = useState(1)
  const description = `Compte premium ${duration} mois`

  const handleAutocomplete = useCallback(({ countryCode, city, address }) => {
    document.querySelector('#country').value = countryCode.toUpperCase()
    document.querySelector('#city').value = city
    document.querySelector('#address').value = address
  }, [])

  const nextStep = e => {
    e.preventDefault()
    if (step === 1) {
      setStep(2)
    } else {
      setStep(3)
      confetti({
        particleCount: 100,
        zIndex: 3000,
        spread: 70,
        origin: { y: 0.6 }
      })
    }
  }

  return (
    <>
      <PrimaryButton onClick={toggleModal}>{children}</PrimaryButton>
      {modal && (
        <Modal class='premium-modal' onClose={toggleModal}>
          <Stepper step={step} class='p4'>
            <Step>Facturations</Step>
            <Step>Paiement</Step>
            <Step>Récapitulatif</Step>
          </Stepper>

          <form action='' onSubmit={nextStep}>
            <Slider slide={step}>
              <div>
                <div class='grid2 px4'>
                  <Field name='firstname' required defaultValue={'John'}>
                    Prénom
                  </Field>
                  <Field name='lastname' required defaultValue={'Doe'}>
                    Nom
                  </Field>
                  <div class='full'>
                    <Field
                      defaultValue={'12 Adresse'}
                      required
                      name='address'
                      component={AddressField}
                      onChange={handleAutocomplete}
                    >
                      Adresse
                    </Field>
                  </div>
                  <Field name='city' defaultValue={'Montpellier'} required>
                    Ville
                  </Field>
                  <Field name='country' required component={CountryField}>
                    Pays
                  </Field>
                </div>

                <hr class='m4' />

                <div class='px4 pb4'>
                  {step === 3 ? (
                    <a href='#' className='btn-primary btn-block'>
                      Voir les tutoriels premiums
                    </a>
                  ) : (
                    <Button class='btn-primary btn-block' type='submit'>
                      {step === 1 && 'Passer au paiement'}
                      {step === 2 && 'Devenir premium'}
                    </Button>
                  )}
                </div>
              </div>

              <div class='px4'>
                <div class='mb2 section-title'>
                  <span>Formule 1 mois : </span> <span className='h4'>4.2€</span>
                </div>

                <PaymentMethods plan={plan} price={price} description={duration} />
              </div>
              <div class='px4'>
                <h1 className='h1'>Bravo !</h1>
                <div className='text-center'>
                  `
                  <img src='/images/success.svg' alt='' style='max-width: 80%;' />
                </div>
                <p>Vous êtes maintenant premium jusqu'au XX/XX/XXXX</p>
              </div>
            </Slider>
          </form>
        </Modal>
      )}
    </>
  )
}
