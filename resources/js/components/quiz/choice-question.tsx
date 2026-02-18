import { CheckIcon, XIcon } from "lucide-react"
import type { Quiz } from "@/hooks/use-questions.ts"
import { cn } from "@/lib/utils.ts"

export function ChoiceQuestion({ quiz }: { quiz: Quiz }) {
  const answered = quiz.state === "answer"

  return (
    <div className="space-y-2">
      {quiz.choices.map((choice, i) => {
        const isCorrect = answered && i === quiz.expectedAnswer
        const isWrong = answered && i === quiz.selected && !quiz.isCorrect
        return (
          <button
            type="button"
            key={i}
            disabled={answered}
            onClick={() => quiz.answer(i)}
            className={cn(
              "w-full text-left rounded-lg p-3 transition flex items-center gap-3 duration-150 border",
              "border hover:border-primary hover:bg-list-hover",
              isCorrect && "border-success shadow-success text-success",
              isWrong &&
                "border-destructive shadow-destructive text-destructive",
            )}
          >
            <span className="flex-1">{choice}</span>
            {isCorrect && <CheckIcon className="size-4 shrink-0" />}
            {isWrong && <XIcon className="size-4 shrink-0" />}
          </button>
        )
      })}
    </div>
  )
}
