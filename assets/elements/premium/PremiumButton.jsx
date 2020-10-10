import { PrimaryButton } from '/components/Button.jsx'
import { Modal } from '/components/Modal.jsx'
import { useToggle } from '/functions/hooks.js'
import { Step, Stepper } from '/components/Stepper.jsx'
import { Slider } from '/components/Slider.jsx'
import { useCallback, useState } from 'preact/hooks'
import confetti from 'canvas-confetti'
import { PaymentMethods } from '/elements/premium/PaymentMethods.jsx'
import { InvoiceAddress } from '/elements/premium/InvoiceAddress.jsx'
import { ApiError, jsonFetch } from '/functions/api.js'
import { flash } from '/elements/Alert.js'

export function PremiumButton ({ children, plan, price, duration }) {
  const [modal, toggleModal] = useToggle(false)
  const [step, setStep] = useState(1)
  const [address, setAddressState] = useState({
    firstname: 'John',
    lastname: 'Doe',
    address: '120 Avenue de test',
    city: 'Montpellier',
    postalCode: '34000',
    countryCode: 'FR'
  })
  const description = `Compte premium ${duration} mois`
  price = parseFloat(price)
  const vat = address.countryCode === 'FR' ? parseFloat((0.2 * price).toFixed(2)) : 0

  const setAddress = address => {
    setAddressState(address)
    setStep(2)
  }

  const handlePaypal = useCallback(async orderId => {
    try {
      await jsonFetch(`/api/premium/paypal/${orderId}`, { method: 'POST' })
      setStep(3)
      confetti({
        particleCount: 100,
        zIndex: 3000,
        spread: 70,
        origin: { y: 0.6 }
      })
    } catch (e) {
      if (e instanceof ApiError) {
        flash(e.name, 'danger', null)
      }
    }
  }, [])

  return (
    <>
      <PrimaryButton onClick={toggleModal}>{children}</PrimaryButton>
      {modal && (
        <Modal class='premium-modal pb4' onClose={toggleModal}>
          <Stepper step={step} class='p4'>
            <Step>Facturations</Step>
            <Step>Paiement</Step>
            <Step>Récapitulatif</Step>
          </Stepper>

          <Slider slide={step}>
            <div class='px4'>
              <InvoiceAddress onSubmit={setAddress} address={address} />
            </div>

            <div class='px4'>
              <PaymentMethods
                plan={plan}
                price={price}
                vat={vat}
                description={description}
                onPaypalApproval={handlePaypal}
              />
            </div>

            <div class='px4'>
              <h1 class='h1 text-center mb2'>Bravo !</h1>
              <div class='text-center mb3'>
                <img src='/images/success.svg' alt='' style='max-width: 80%;' />
              </div>
              <p>Votre paiement a été accepté, vous êtes maintenant premium pour {duration} mois.</p>

              <hr class='my4' />

              <a class='btn-primary btn-block' href='/tutoriels/premium'>
                Voir les tutoriels premiums
              </a>
            </div>
          </Slider>
        </Modal>
      )}
    </>
  )
}
