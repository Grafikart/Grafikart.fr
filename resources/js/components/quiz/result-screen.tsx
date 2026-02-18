import { ChevronDownIcon, RotateCcwIcon } from "lucide-react"
import { useEffect } from "react"
import { Button } from "@/components/ui/button.tsx"
import { useApiMutation } from "@/hooks/use-api-fetch.ts"
import type { useQuestions } from "@/hooks/use-questions.ts"

export function ResultScreen({
  quiz,
  courseId,
  onClose,
}: {
  quiz: ReturnType<typeof useQuestions>
  courseId: string
  onClose: () => void
}) {
  const { mutate, isPending } = useApiMutation<void, { score: number }>(
    `/api/courses/${courseId}/progress`,
    { method: "POST" },
  )

  const resultMessages = [
    { percentage: 100, message: "Parfait, sans faute !" },
    { percentage: 80, message: "Bravo, vous maîtrisez bien le sujet !" },
    {
      percentage: 60,
      message: "Bien joué, vous avez retenu l'essentiel !",
    },
    { percentage: 40, message: "Pas mal, mais vous pouvez faire mieux." },
    { percentage: 20, message: "Il y a du progrès à faire, courage !" },
    {
      percentage: 0,
      message: "Aïe, c'est pas encore ça... On recommence ?",
    },
  ]

  useEffect(() => {
    mutate({ score: quiz.percentage })
  }, [mutate, quiz.percentage])

  return (
    <div className="flex flex-col items-center gap-4 text-center">
      <p className="text-muted text-lg">
        {resultMessages.find((m) => quiz.percentage >= m.percentage)?.message}
      </p>
      <div className="text-5xl font-bold">
        {quiz.score}/{quiz.total}
      </div>
      {quiz.percentage > 60 && <con-fetti />}
      <div className="flex gap-2">
        {quiz.percentage < 100 && (
          <Button variant="outline" size="lg" onClick={quiz.restart}>
            <RotateCcwIcon className="size-4" />
            Recommencer
          </Button>
        )}
        <Button size="lg" onClick={onClose} disabled={isPending}>
          <ChevronDownIcon />
          Terminer
        </Button>
      </div>
    </div>
  )
}
