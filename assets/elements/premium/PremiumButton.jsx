import { Button, PrimaryButton } from '/components/Button.jsx'
import { useToggle } from '/functions/hooks.js'
import { SlideIn } from '/components/Animation/SlideIn.jsx'
import { useEffect, useRef, useState } from 'preact/hooks'
import { classNames } from '/functions/dom.js'
import { vatPrice } from '/functions/vat.js'
import { ApiError, jsonFetch } from '/functions/api.js'
import { redirect } from '/functions/url.js'
import { flash } from '/elements/Alert.js'
import scriptjs from 'scriptjs'
import { Stack } from '/components/Layout.jsx'
import { Checkbox, Field } from '/components/Form.jsx'
import { CountrySelect } from '/components/CountrySelect.jsx'
import { importScript } from '/functions/script.js'
import { isAuthenticated } from '/functions/auth.js'

export function PremiumButton ({ children, plan, price, duration, stripeKey, paypalId }) {
  const [payment, togglePayment] = useToggle(false)
  const description = `Compte premium ${duration} mois`

  if (!isAuthenticated()) {
    return (
      <a href='/connexion?redirect=/premium' class='btn-primary btn-block'>
        {children}
      </a>
    )
  }

  if (payment === false) {
    return <PrimaryButton onClick={togglePayment}>{children}</PrimaryButton>
  }

  return (
    <SlideIn show={true}>
      <PaymentMethods plan={plan} price={price} description={description} stripeKey={stripeKey} paypalId={paypalId} />
    </SlideIn>
  )
}

const PAYMENT_CARD = 'CARD'
const PAYMENT_PAYPAL = 'PAYPAL'

function PaymentMethods ({ plan, onPaypalApproval, description, price, stripeKey, paypalId }) {
  const [method, setMethod] = useState(PAYMENT_CARD)

  return (
    <div class='text-left'>
      <div className='form-group mb2'>
        <label>Méthode de paiement</label>
        <div class='btn-group'>
          <button
            onClick={() => setMethod(PAYMENT_CARD)}
            class={classNames('btn-secondary btn-small', method === PAYMENT_CARD && 'active')}
          >
            <img src='/images/payment-methods.png' width='76' class='mr1' />
          </button>
          <button
            onClick={() => setMethod(PAYMENT_PAYPAL)}
            class={classNames('btn-secondary btn-small', method === PAYMENT_PAYPAL && 'active')}
          >
            <img src='/images/paypal.svg' width='20' class='mr1' />
          </button>
        </div>
      </div>
      {method === PAYMENT_PAYPAL ? (
        <PaymentPaypal
          planId={plan}
          price={price}
          description={description}
          onApprove={onPaypalApproval}
          paypalId={paypalId}
        />
      ) : (
        <PaymentCard plan={plan} publicKey={stripeKey} />
      )}
    </div>
  )
}

function PaymentPaypal ({ planId, price, description, paypalId }) {
  const container = useRef(null)
  const approveRef = useRef(null)
  const currency = 'EUR'
  const [country, setCountry] = useState(null)
  const [loading, toggleLoading] = useToggle(false)
  const vat = country ? vatPrice(price, country) : null

  approveRef.current = async orderId => {
    toggleLoading()
    try {
      await jsonFetch(`/api/premium/paypal/${orderId}`, { method: 'POST' })
      await redirect('?success=1')
    } catch (e) {
      if (e instanceof ApiError) {
        flash(e.name, 'danger', null)
      }
    }
    toggleLoading()
  }

  useEffect(() => {
    if (vat === null) {
      return
    }
    toggleLoading()
    const priceWithoutTax = price - vat
    scriptjs(
      `https://www.paypal.com/sdk/js?client-id=${paypalId}&disable-funding=card,credit&integration-date=2020-12-10&currency=${currency}`,
      () => {
        toggleLoading()
        container.current.innerHTML = ''
        window.paypal
          .Buttons({
            style: {
              label: 'pay'
            },
            createOrder: (data, actions) => {
              return actions.order.create({
                purchase_units: [
                  {
                    description,
                    custom_id: planId,
                    items: [
                      {
                        name: description,
                        quantity: '1',
                        unit_amount: {
                          value: priceWithoutTax,
                          currency_code: currency
                        },
                        tax: {
                          value: vat,
                          currency_code: currency
                        },
                        category: 'DIGITAL_GOODS'
                      }
                    ],
                    amount: {
                      currency_code: currency,
                      value: price,
                      breakdown: {
                        item_total: {
                          currency_code: currency,
                          value: priceWithoutTax
                        },
                        tax_total: {
                          currency_code: currency,
                          value: vat
                        }
                      }
                    }
                  }
                ]
              })
            },
            onApprove: data => {
              approveRef.current(data.orderID)
            }
          })
          .render(container.current)
        return () => {
          container.current.innerHTML = ''
        }
      }
    )
  }, [description, planId, price, vat])

  return (
    <Stack>
      <Field
        name='countryCode'
        required
        component={CountrySelect}
        value={country}
        onChange={e => setCountry(e.target.value)}
      >
        Pays de résidence
      </Field>
      {country && <div style={{ minHeight: 52, display: loading ? 'none' : null }} ref={container} />}
      {loading && (
        <Button class='btn-primary btn-block' loading>
          Chargement...
        </Button>
      )}
    </Stack>
  )
}

function PaymentCard ({ plan, publicKey }) {
  const [subscription, toggleSubscription] = useToggle(true)
  const [loading, toggleLoading] = useToggle(false)
  const startPayment = async () => {
    toggleLoading()
    try {
      const Stripe = await importScript('https://js.stripe.com/v3/', 'Stripe')
      const stripe = new Stripe(publicKey)
      const { id } = await jsonFetch(`/api/premium/${plan}/stripe/checkout?subscription=${subscription ? '1' : '0'}`, {
        method: 'POST'
      })
      stripe.redirectToCheckout({ sessionId: id })
    } catch (e) {
      flash(e instanceof ApiError ? e.name : e, 'error')
      toggleLoading()
    }
  }

  return (
    <Stack gap={2}>
      <Checkbox id='subscription' name='subscription' checked={subscription} onChange={toggleSubscription}>
        Renouveller automatiquement
      </Checkbox>
      <PrimaryButton size='block' onClick={startPayment} loading={loading}>
        {subscription ? "S'abonner via" : 'Payer via '}
        <img src='/images/stripe.svg' height='20' style={{ marginLeft: '.4rem' }} />
      </PrimaryButton>
    </Stack>
  )
}
