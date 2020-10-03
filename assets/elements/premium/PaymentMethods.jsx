import { Field } from '/components/Form.jsx'
import { useCallback, useEffect, useRef, useState } from 'preact/hooks'
import scriptjs from 'scriptjs'

export const PAYMENT_CARD = 'CARD'
export const PAYMENT_PAYPAL = 'PAYPAL'

export function PaymentMethods ({ plan, price, description, vat = 0 }) {
  const [method, setMethod] = useState(PAYMENT_PAYPAL)

  const handleChange = function (e) {
    setMethod(e.target.value)
  }

  const handleApprovePaypal = useCallback(orderID => {
    console.log('orderid : ', orderID)
  }, [])

  return (
    <>
      {JSON.stringify(method)}
      <div>
        <label htmlFor='paymentcard'>
          <input
            type='radio'
            id='paymentcard'
            name='payment_method'
            checked={method === PAYMENT_CARD}
            onchange={handleChange}
            value={PAYMENT_CARD}
          />{' '}
          Paiement par carte bancaire
        </label>
      </div>
      <div class='flex' style={{ justifyContent: 'flex-start' }}>
        <label htmlFor='paymentpaypal'>
          <input
            type='radio'
            id='paymentpaypal'
            name='payment_method'
            checked={method === PAYMENT_PAYPAL}
            onchange={handleChange}
            value={PAYMENT_PAYPAL}
          />{' '}
          <img src='/images/paypal.svg' width='20' class='mr1' /> Paypal
        </label>
      </div>
      <hr class='my3' />
      <div class='mb4'>
        {method === PAYMENT_PAYPAL ? (
          <PaymentPaypal
            planId={plan}
            price={price}
            vat={vat}
            description={description}
            onApprove={handleApprovePaypal}
          />
        ) : (
          <PaymentCard />
        )}
      </div>
    </>
  )
}

function PaymentPaypal ({ planId, vat, price, description, onApprove }) {
  const container = useRef(null)
  const currency = 'EUR'

  useEffect(() => {
    scriptjs(
      'https://www.paypal.com/sdk/js?client-id=AVMID7UVEvfkxhAWbf_xKweK5tMQL66c-6OVtaFGnY_oU4CWtuYZkmLOck13vl2sDuebyJ6KJhznBXpY&disable-funding=card,credit&integration-date=2020-12-10&currency=' +
        currency,
      () => {
        window.paypal
          .Buttons({
            createOrder: function (data, actions) {
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
                    description: description,
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
            onApprove: function (data, actions) {
              onApprove(data.orderID)
            }
          })
          .render(container.current)
      }
    )
  }, [onApprove])

  return <div ref={container} />
}

function PaymentCard () {
  return (
    <div class='grid2'>
      <div class='full'>
        <Field name='card' required defaultValue={'1212 1212 1212 1212'}>
          Numéro de carte
        </Field>
      </div>
      <Field name='card' required defaultValue={'03/19'}>
        Date d'expiration
      </Field>
      <Field name='card' required defaultValue={'123'}>
        Code de sécurité
      </Field>
    </div>
  )
}
