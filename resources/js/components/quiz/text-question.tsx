import { type FormEventHandler, useState } from "react"
import { Button } from "@/components/ui/button.tsx"
import type { useQuestions } from "@/hooks/use-questions.ts"
import { cn } from "@/lib/utils.ts"

export function TextQuestion({
  quiz,
}: {
  quiz: ReturnType<typeof useQuestions>
}) {
  const [value, setValue] = useState("")
  const answered = quiz.state === "answer"
  const isCorrect = answered && quiz.isCorrect
  const isWrong = answered && !quiz.isCorrect

  const handleSubmit: FormEventHandler = (e) => {
    e.preventDefault()
    quiz.answer(value)
  }

  return (
    <div>
      <form onSubmit={handleSubmit} className="flex gap-2 items-stretch">
        <input
          value={value}
          onChange={(e) => setValue(e.target.value)}
          placeholder="Votre réponse..."
          disabled={answered}
          className={cn(
            "border w-full rounded-md focus:shadow-focus outline-none focus:border-primary px-2.5 py-2",
            isCorrect && "border-success shadow-success",
            isWrong &&
              "border-destructive shadow-destructive wiggle text-destructive",
          )}
          autoFocus
        />
        {!answered && (
          <Button
            type="submit"
            size="lg"
            disabled={!value.trim()}
            className="-m-px"
          >
            Valider
          </Button>
        )}
      </form>
      {answered && !quiz.isCorrect && (
        <div className="mt-3 rounded-lg border p-3 bg-background/30">
          <p className="text-sm text-muted mb-1">Réponse attendue :</p>
          <p className="font-semibold">{String(quiz.expectedAnswer)}</p>
        </div>
      )}
    </div>
  )
}
