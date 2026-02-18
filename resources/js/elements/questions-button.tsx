import { QueryClientProvider } from "@tanstack/react-query"
import { BookCheckIcon } from "lucide-react"
import { AnimatePresence } from "motion/react"
import { useEffect, useRef, useState } from "react"
import { CheatingPrevention } from "@/components/quiz/cheating-prevention.tsx"
import { PremiumGate } from "@/components/quiz/premium-gate.tsx"
import { QuizRunner } from "@/components/quiz/quiz-runner.tsx"
import { Drawer } from "@/components/ui/drawer.tsx"
import { queryClient } from "@/hooks/use-api-fetch.ts"
import type { Quiz } from "@/hooks/use-questions.ts"
import { isPremium } from "@/lib/auth.ts"

type Props = {
  course: string | null
}

export default function QuestionsButton({ course }: Props) {
  const [open, setOpen] = useState(false)
  const [isCheating, setCheating] = useState(false)
  const stateRef = useRef<Quiz["state"]>("start")

  const onOpenChange = (value: boolean) => {
    if (open && !value && stateRef.current === "question") {
      setCheating(true)
      return
    }
    setOpen(value)
  }

  useEffect(() => {
    if (isCheating) {
      const timeout = setTimeout(() => {
        setCheating(false)
      }, 3_500)
      return () => clearTimeout(timeout)
    }
  }, [isCheating])

  return (
    <QueryClientProvider client={queryClient}>
      <Drawer
        open={open}
        onOpenChange={onOpenChange}
        side="bottom"
        width={640}
        hideClose
        trigger={
          <button
            type="button"
            className="fixed bottom-4 right-4 z-500 flex items-center gap-2 rounded-full bg-primary px-4 py-2.5 text-primary-foreground shadow-lg hover:bg-primary/90 transition"
          >
            <BookCheckIcon className="size-5" />
            Passer le quiz
          </button>
        }
      >
        <div className="py-10">
          {isPremium() ? (
            <QuizRunner
              stateRef={stateRef}
              courseId={course!}
              onClose={() => setOpen(false)}
            />
          ) : (
            <PremiumGate />
          )}
        </div>
        <AnimatePresence>
          {isCheating && <CheatingPrevention />}
        </AnimatePresence>
      </Drawer>
    </QueryClientProvider>
  )
}
