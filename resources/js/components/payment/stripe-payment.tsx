import type { Stripe } from "@stripe/stripe-js"
import { useState } from "react"
import { Button } from "@/components/ui/button.tsx"
import { Spinner } from "@/components/ui/spinner.tsx"
import { apiFetch } from "@/hooks/use-api-fetch.ts"
import { toast } from "sonner"

type Props = {
  plan: number
}

type StripeSession = {
  url: string
}

export function StripePayment(props: Props) {
  const [fetching, setFetching] = useState(false)
  const [subscription, setSubscription] = useState(true)

  const startPayment = async () => {
    setFetching(true)
    try {
      const stripe = await fetchStripe()
      if (!stripe) {
        throw new Error("Impossible de charger le module de paiement stripe")
      }
      const session = await apiFetch<StripeSession>(
        `/api/premium/${props.plan}/stripe?subscription=${subscription ? 1 : 0}`,
        {
          method: "post",
        },
      )
      if (!session.url) {
        throw new Error("Impossible de résoudre l'url de redirection Stripe")
      }
      window.location.href = session.url
    } catch (e) {
      toast.error(`Impossible d'initialiser le paiement ${e}`)
      console.error(e)
    }
  }

  return (
    <>
      <div className="space-y-2">
        <label className="flex cursor-pointer items-center gap-3">
          <input
            type="checkbox"
            checked={subscription}
            onChange={(e) => setSubscription(e.target.checked)}
            className="size-5 rounded accent-primary"
          />
          <span className="font-medium text-xs uppercase tracking-wide">
            Renouveler automatiquement
          </span>
        </label>
        {subscription && (
          <p className="text-muted text-sm">
            Le renouvellement automatique est activé, vous serez prélevé
            automatiquement à la fin de chaque période. Vous pourrez interrompre
            l'abonnement à tout moment depuis votre compte.
          </p>
        )}
      </div>

      <Button
        disabled={fetching}
        variant="default"
        size="lg"
        onClick={startPayment}
        type="button"
        className="w-full"
      >
        {fetching && <Spinner />}
        {subscription ? "S'abonner" : "Payer"} via
        <strong>Stripe</strong>
      </Button>
    </>
  )
}

const stripePromise = { current: null as null | Promise<Stripe | null> }

const fetchStripe = () => {
  // The stripe promise is already set
  if (stripePromise.current) {
    return stripePromise.current
  }
  const stripePublic = document
    .querySelector('meta[name="stripe"]')
    ?.getAttribute("content")
  if (!stripePublic) {
    throw new Error(
      'Cannot load stripe public key, ensure <meta name="stripe"> is set',
    )
  }
  return (stripePromise.current = import("@stripe/stripe-js").then((m) =>
    m.loadStripe(stripePublic),
  ))
}
