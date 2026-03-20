import { QueryClientProvider } from "@tanstack/react-query"
import { BookCheckIcon, CheckCheckIcon } from "lucide-react"
import { AnimatePresence } from "motion/react"
import { useEffect, useRef, useState } from "react"
import { CheatingPrevention } from "@/components/quiz/cheating-prevention.tsx"
import { PremiumGate } from "@/components/quiz/premium-gate.tsx"
import { QuizRunner } from "@/components/quiz/quiz-runner.tsx"
import { Drawer } from "@/components/ui/drawer.tsx"
import { queryClient } from "@/hooks/use-api-fetch.ts"
import type { Quiz } from "@/hooks/use-questions.ts"
import { isPremium } from "@/lib/auth.ts"
import { cn } from "@/lib/utils.ts"

type Props = {
  course: string | null
  completed?: string | null
}

export function getQuizButtonState(isCompleted: boolean): {
  icon: "book" | "check"
  label: string
  className: string
} {
  if (isCompleted) {
    return {
      icon: "check",
      label: "Quiz terminé",
      className:
        "bg-success text-white hover:bg-green-700 dark:bg-green-500 dark:hover:bg-green-400",
    }
  }

  return {
    icon: "book",
    label: "Passer le quiz",
    className: "bg-primary text-primary-foreground hover:bg-primary/90",
  }
}

export default function QuestionsButton({ course, completed }: Props) {
  const [open, setOpen] = useState(false)
  const [isCheating, setCheating] = useState(false)
  const [isCompleted, setIsCompleted] = useState(completed === "true")
  const stateRef = useRef<Quiz["state"]>("start")
  const buttonState = getQuizButtonState(isCompleted)

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
            className={cn(
              "fixed bottom-4 right-4 z-500 flex items-center gap-2 rounded-full px-4 py-2.5 shadow-lg transition",
              buttonState.className,
            )}
          >
            {buttonState.icon === "check" ? (
              <CheckCheckIcon className="size-5" />
            ) : (
              <BookCheckIcon className="size-5" />
            )}
            {buttonState.label}
          </button>
        }
      >
        <div className="py-10">
          {isPremium() ? (
            <QuizRunner
              stateRef={stateRef}
              courseId={course!}
              onComplete={() => setIsCompleted(true)}
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
