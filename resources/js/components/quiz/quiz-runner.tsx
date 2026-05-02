import { BookCheckIcon, ChevronRightIcon } from "lucide-react"
import type { RefObject } from "react"
import { ChoiceQuestion } from "@/components/quiz/choice-question.tsx"
import { ResultScreen } from "@/components/quiz/result-screen.tsx"
import { TextQuestion } from "@/components/quiz/text-question.tsx"
import { AnimatePresenceSlide } from "@/components/ui/animate-presence-slide.tsx"
import { Button } from "@/components/ui/button.tsx"
import { MDText } from "@/components/ui/md-text.tsx"
import { type Quiz, useQuestions } from "@/hooks/use-questions.ts"
import { times } from "@/lib/array.ts"
import { cn } from "@/lib/utils.ts"

export function QuizRunner({
  courseId,
  onClose,
  onComplete,
  stateRef,
}: {
  courseId: string
  onClose: () => void
  onComplete: () => void
  stateRef: RefObject<Quiz["state"]>
}) {
  const quiz = useQuestions(courseId)
  stateRef.current = quiz.state
  const panel = (() => {
    if (!["start", "end"].includes(quiz.state)) {
      return "question"
    }
    return quiz.state
  })()

  return (
    <AnimatePresenceSlide step={panel}>
      {panel === "start" && <StartingPanel onStart={() => quiz.next()} />}
      {panel === "end" && (
        <ResultScreen
          quiz={quiz}
          courseId={courseId}
          onClose={onClose}
          onComplete={onComplete}
        />
      )}
      {panel === "question" && <Question quiz={quiz} />}
    </AnimatePresenceSlide>
  )
}

function StartingPanel(props: { onStart: () => void }) {
  return (
    <div className="flex flex-col items-center gap-4 text-center">
      <BookCheckIcon className="size-10 text-primary" />
      <p className="text-2xl font-bold">Avez vous été bien attentif ?</p>
      <p className="text-muted text-lg max-w-md">
        Voici quelques questions pour vérifier ce que vous avez retenu du cours.
        Votre score sera enregistré à la fin du quiz.
      </p>
      <Button size="lg" onClick={props.onStart}>
        Commencer le quiz
      </Button>
    </div>
  )
}

function Question({ quiz }: { quiz: Quiz }) {
  return (
    <>
      <div className="flex items-center justify-between mb-4 relative">
        <div className="text-sm text-muted">
          Question {quiz.step + 1}/{quiz.total}
        </div>
        <div className="flex gap-1">
          {times(quiz.total, (i) => (
            <div
              key={i}
              className={cn(
                "size-2 rounded-full bg-border transition-all",
                i === quiz.step && "bg-primary",
              )}
            />
          ))}
        </div>
        {quiz.state === "answer" && (
          <Button
            onClick={quiz.next}
            className="absolute  -top-1 -right-px transition-opacity duration-150 starting:opacity-0 py-2"
          >
            Suivant
            <ChevronRightIcon />
          </Button>
        )}
      </div>

      <AnimatePresenceSlide step={quiz.step}>
        <p className="text-lg font-semibold text-foreground-title mb-4 prose">
          <MDText inline text={quiz.question} />
        </p>

        {quiz.type === "choice" ? (
          <ChoiceQuestion quiz={quiz} />
        ) : (
          <TextQuestion quiz={quiz} />
        )}
      </AnimatePresenceSlide>
    </>
  )
}
