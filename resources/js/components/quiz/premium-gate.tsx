import { StarIcon } from "lucide-react"
import { Button } from "@/components/ui/button.tsx"

export function PremiumGate() {
  return (
    <div className="flex flex-col items-center gap-4 py-8 text-center">
      <StarIcon className="size-10 text-amber-500" />
      <p className="text-lg font-medium">
        Le quiz est réservé aux membres premium
      </p>
      <p className="text-muted text-sm">
        Devenez premium pour tester vos connaissances après chaque cours.
      </p>
      <Button render={<a href="/premium" />}>Devenir premium</Button>
    </div>
  )
}
