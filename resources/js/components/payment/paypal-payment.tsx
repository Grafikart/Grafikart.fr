import { loadScript } from "@paypal/paypal-js"
import { useEffect, useEffectEvent, useRef, useState } from "react"
import { toast } from "sonner"
import { type APIError, apiFetch, useApiFetch } from "@/hooks/use-api-fetch.ts"
import { Spinner } from "@/components/ui/spinner.tsx"
import type { CountryData } from "@/types"

type Props = {
  price: number
  duration: number
  clientId: string
  plan: number
}

export function vatRate(countryCode: string): number {
  if (countryCode === "FR") {
    return 0.2
  }
  return 0
}

export function vatPrice(price: number, countryCode: string) {
  return Math.floor((price - price / (1 + vatRate(countryCode))) * 100) / 100
}

export function PaypalPayment(props: Props) {
  const [country, setCountry] = useState("FR")
  const [loading, setLoading] = useState(false)
  const { data, isPending } = useApiFetch<CountryData[]>("/api/countries")
  const description = `Compte premium ${props.duration} mois`
  const tax = country ? vatPrice(props.price, country) : 0
  const currency = "EUR"
  const priceWithoutTax = props.price - tax
  const countries = data ?? []

  const loadButtons = useEffectEvent(async () => {
    try {
      setLoading(true)
      const paypal = await loadScript({
        clientId: props.clientId,
        currency: currency,
      })
      if (!paypal || !paypal.Buttons) {
        throw new Error("Cannot load paypal Button SDK")
      }
      container.current!.innerHTML = ""
      await paypal
        .Buttons({
          style: {
            label: "pay",
            color: "blue",
            tagline: false,
          },
          createOrder: (_data, actions) =>
            actions.order.create({
              intent: "CAPTURE",
              purchase_units: [
                {
                  description,
                  custom_id: props.plan.toString(),
                  items: [
                    {
                      name: description,
                      quantity: "1",
                      unit_amount: {
                        value: priceWithoutTax.toString(),
                        currency_code: currency,
                      },
                      tax: {
                        value: tax.toString(),
                        currency_code: currency,
                      },
                      category: "DIGITAL_GOODS",
                    },
                  ],
                  amount: {
                    currency_code: currency,
                    value: props.price.toString(),
                    breakdown: {
                      item_total: {
                        currency_code: currency,
                        value: priceWithoutTax.toString(),
                      },
                      tax_total: {
                        currency_code: currency,
                        value: tax.toString(),
                      },
                    },
                  },
                },
              ],
            }),
          onApprove: async (data) => {
            apiFetch(`/api/premium/paypal/${data.orderID}`, {
              method: "POST",
            })
              .then(() => {
                window.location.href = "?success=1"
              })
              .catch((e: APIError) => {
                toast.error(e.message)
              })
          },
        })
        .render(container.current!)
    } catch (e) {
      toast.error(
        `Une erreur est survenue lors du chargement des boutons PaypPal, ${e}`,
      )
    } finally {
      setLoading(false)
    }
  })
  const container = useRef<HTMLDivElement>(null)

  useEffect(() => {
    if (country && !isPending) {
      loadButtons()
    }
  }, [country, isPending])

  if (!countries) {
    return (
      <div className="flex py-2 justify-center">
        <Spinner />
      </div>
    )
  }

  const priorityCodes = ["FR", "BE", "MA", "CA", "CH", "CI"]
  const priorityCountries = countries
    .filter((c) => priorityCodes.includes(c.code))
    .sort(
      (a, b) => priorityCodes.indexOf(a.code) - priorityCodes.indexOf(b.code),
    )

  return (
    <div>
      <div className="space-y-2">
        <label className="font-medium text-muted text-xs uppercase tracking-wide">
          Pays de résidence
        </label>
        <select
          value={country}
          onChange={(e) => setCountry(e.target.value)}
          className="w-full rounded-md border border-border bg-background px-4 py-3"
        >
          <option value="">Veuillez sélectionner un pays</option>
          <optgroup label="Les plus utilisés">
            {priorityCountries.map((c) => (
              <option key={c.code} value={c.code}>
                {c.name}
              </option>
            ))}
          </optgroup>
          <optgroup label="Ordre alphabétique">
            {countries.map((c) => (
              <option key={c.code} value={c.code}>
                {c.name}
              </option>
            ))}
          </optgroup>
        </select>
        {loading && <div className="w-full flex justify-center"><Spinner className="mx-auto" /></div>}
        <div ref={container}/>
      </div>
    </div>
  )
}
