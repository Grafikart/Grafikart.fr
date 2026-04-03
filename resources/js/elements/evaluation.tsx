import { QueryClientProvider } from "@tanstack/react-query"
import { useRef } from "react"
import { PremiumGate } from "@/components/quiz/premium-gate.tsx"
import { QuizRunner } from "@/components/quiz/quiz-runner.tsx"
import { queryClient } from "@/hooks/use-api-fetch.ts"
import type { Quiz } from "@/hooks/use-questions.ts"
import { isPremium } from "@/lib/auth.ts"

type Props = {
  course: string | null
}

export default function Evaluation({ course }: Props) {
  const stateRef = useRef<Quiz["state"]>("start")

  return (
    <QueryClientProvider client={queryClient}>
      {isPremium() ? (
        <QuizRunner
          stateRef={stateRef}
          courseId={course!}
          onComplete={() => null}
          onClose={() => null}
        />
      ) : (
        <PremiumGate />
      )}
    </QueryClientProvider>
  )
}
