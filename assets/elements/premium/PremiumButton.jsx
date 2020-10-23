import { PrimaryButton } from '/components/Button.jsx'
import { useToggle } from '/functions/hooks.js'
import { useCallback } from 'preact/hooks'
import confetti from 'canvas-confetti'
import { PaymentMethods } from '/elements/premium/PaymentMethods.jsx'
import { ApiError, jsonFetch } from '/functions/api.js'
import { flash } from '/elements/Alert.js'
import { SlideIn } from '/components/Animation/SlideIn.jsx'

export function PremiumButton ({ children, plan, price, duration }) {
  const [payment, togglePayment] = useToggle(false)
  const description = `Compte premium ${duration} mois`
  const handlePaypal = useCallback(async orderId => {
    try {
      console.log({ orderId })
      await jsonFetch(`/api/premium/paypal/${orderId}`, { method: 'POST' })
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

  if (payment === false) {
    return <PrimaryButton onClick={togglePayment}>{children}</PrimaryButton>
  }

  return (
    <SlideIn show={true}>
      <PaymentMethods plan={plan} onPaypalApproval={handlePaypal} price={price} description={description} />
    </SlideIn>
  )
}
