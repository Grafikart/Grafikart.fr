import { Checkbox, Field } from '/components/Form.jsx'
import { useEffect, useRef, useState } from 'preact/hooks'
import scriptjs from 'scriptjs'
import { Stack } from '/components/Layout.jsx'
import { useToggle } from '/functions/hooks.js'
import { Button, PrimaryButton } from '/components/Button.jsx'
import { ApiError, jsonFetch } from '/functions/api.js'
import { flash } from '/elements/Alert.js'
import { importScript } from '/functions/script.js'
import { classNames } from '/functions/dom.js'
import { CountrySelect } from '/components/CountrySelect.jsx'
import { vatPrice } from '/functions/vat.js'

export const PAYMENT_CARD = 'CARD'
export const PAYMENT_PAYPAL = 'PAYPAL'

export function PaymentMethods ({ plan, onPaypalApproval, description, price }) {
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
        <PaymentPaypal planId={plan} price={price} description={description} onApprove={onPaypalApproval} />
      ) : (
        <PaymentCard plan={plan} />
      )}
    </div>
  )
}

function PaymentPaypal ({ planId, price, description, onApprove }) {
  const container = useRef(null)
  const approveRef = useRef(null)
  const currency = 'EUR'
  const [country, setCountry] = useState(null)
  const [loading, toggleLoading] = useToggle(false)
  const vat = country ? vatPrice(price, country) : null

  approveRef.current = orderId => {
    toggleLoading()
    onApprove(orderId).finally(toggleLoading)
  }

  useEffect(() => {
    if (vat === null) {
      return
    }
    const priceWithoutTax = price - vat
    scriptjs(
      `https://www.paypal.com/sdk/js?client-id=AVMID7UVEvfkxhAWbf_xKweK5tMQL66c-6OVtaFGnY_oU4CWtuYZkmLOck13vl2sDuebyJ6KJhznBXpY&disable-funding=card,credit&integration-date=2020-12-10&currency=${currency}`,
      () => {
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
          Traitement...
        </Button>
      )}
    </Stack>
  )
}

function PaymentCard ({ plan }) {
  const [subscription, toggleSubscription] = useToggle(true)
  const [loading, toggleLoading] = useToggle(false)
  const startPayment = async () => {
    toggleLoading()
    try {
      const Stripe = await importScript('https://js.stripe.com/v3/', 'Stripe')
      const stripe = new Stripe('pk_test_4xz0aH0LjCHk6XQOsKJy1qZh')
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
        <img src='/images/stripe.svg' height='20' style={{ marginBottom: '-2px', marginLeft: '.4rem' }} />
      </PrimaryButton>
    </Stack>
  )
}
