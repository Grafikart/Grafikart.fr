import { Radio } from '/components/Form.jsx'
import { useEffect, useRef, useState } from 'preact/hooks'
import scriptjs from 'scriptjs'
import { Flex, Stack } from '/components/Layout.jsx'
import { useToggle } from '/functions/hooks.js'
import { Button, PrimaryButton } from '/components/Button.jsx'
import { formatMoney } from '/functions/string.js'
import { ApiError, jsonFetch } from '/functions/api.js'
import { flash } from '/elements/Alert.js'
import { importScript } from '/functions/script.js'

export const PAYMENT_CARD = 'CARD'
export const PAYMENT_PAYPAL = 'PAYPAL'

export function PaymentMethods ({ plan, price, description, vat = 0, onPaypalApproval }) {
  const [method, setMethod] = useState(PAYMENT_CARD)

  const handleChange = function (e) {
    setMethod(e.target.value)
  }

  return (
    <>
      <div class='mb3 section-title'>
        <span style={{ fontWeight: 'normal' }}>{description} : </span>
        {formatMoney(price + vat)} {vat > 0 && <small>TTC</small>}
      </div>
      <Stack gap={1}>
        <Flex center>
          <Radio
            id='paymentcard'
            name='payment_method'
            checked={method === PAYMENT_CARD}
            onchange={handleChange}
            value={PAYMENT_CARD}
          >
            <img src='/images/payment-methods.png' width='76' class='mr1' />
            Paiement par carte bancaire
          </Radio>
        </Flex>
        <Flex center>
          <Radio
            id='paymentpaypal'
            name='payment_method'
            checked={method === PAYMENT_PAYPAL}
            onchange={handleChange}
            value={PAYMENT_PAYPAL}
          >
            <img src='/images/paypal.svg' width='20' class='mr1' />
            Paypal
          </Radio>
        </Flex>
      </Stack>
      <hr class='my4' />
      {method === PAYMENT_PAYPAL ? (
        <PaymentPaypal planId={plan} price={price} vat={vat} description={description} onApprove={onPaypalApproval} />
      ) : (
        <PaymentCard plan={plan} />
      )}
    </>
  )
}

function PaymentPaypal ({ planId, vat, price, description, onApprove }) {
  const container = useRef(null)
  const approveRef = useRef(null)
  const currency = 'EUR'
  const [loading, toggleLoading] = useToggle(false)

  approveRef.current = orderId => {
    toggleLoading()
    onApprove(orderId).finally(toggleLoading)
  }

  useEffect(() => {
    scriptjs(
      `https://www.paypal.com/sdk/js?client-id=AVMID7UVEvfkxhAWbf_xKweK5tMQL66c-6OVtaFGnY_oU4CWtuYZkmLOck13vl2sDuebyJ6KJhznBXpY&disable-funding=card,credit&integration-date=2020-12-10&currency=${currency}`,
      () => {
        window.paypal
          .Buttons({
            createOrder: (data, actions) => {
              return actions.order.create({
                payer: {
                  name: {
                    given_name: 'John',
                    surname: 'Doe'
                  },
                  email_address: 'john1@doe.fr',
                  address: {
                    address_line_1: 'Avenue de test',
                    admin_area_2: 'Montpellier',
                    country_code: 'FR',
                    postal_code: '34000'
                  }
                },
                purchase_units: [
                  {
                    description,
                    custom_id: planId,
                    items: [
                      {
                        name: description,
                        quantity: '1',
                        unit_amount: {
                          value: price,
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
                      value: price + vat,
                      breakdown: {
                        item_total: {
                          currency_code: currency,
                          value: price
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
      }
    )
  }, [description, planId, price, vat])

  return (
    <>
      <div style={{ minHeight: 52, display: loading ? 'none' : null }} ref={container} />
      {loading && (
        <Button class='btn-primary btn-block' loading>
          Traitement...
        </Button>
      )}
    </>
  )
}

function PaymentCard ({ plan }) {
  const [loading, toggleLoading] = useState(false)
  const startPayment = async () => {
    toggleLoading()
    try {
      const Stripe = await importScript('https://js.stripe.com/v3/', 'Stripe')
      const stripe = new Stripe('pk_test_4xz0aH0LjCHk6XQOsKJy1qZh')
      const { id } = await jsonFetch(`/api/premium/${plan}/stripe/checkout`, { method: 'POST' })
      stripe.redirectToCheckout({ sessionId: id })
    } catch (e) {
      flash(e instanceof ApiError ? e.name : e, 'error')
    }
    toggleLoading()
  }

  return (
    <div>
      <PrimaryButton size='block' onClick={startPayment} loading={loading}>
        Payer avec stripe
      </PrimaryButton>
    </div>
  )
}
