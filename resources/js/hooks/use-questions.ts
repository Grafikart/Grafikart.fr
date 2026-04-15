import { useCallback, useState } from "react"
import { useApiFetch } from "@/hooks/use-api-fetch.ts"
import type {
  QuestionChoicesAnswer,
  QuestionData,
  QuestionTextAnswer,
} from "@/types"

type QuizState = "start" | "question" | "answer" | "end"

function checkAnswer(current: QuestionData, value: number | string): boolean {
  if (current.type === "choice") {
    return value === (current.answer as QuestionChoicesAnswer).answer
  }
  return (
    String(value).trim().toLowerCase() ===
    (current.answer as QuestionTextAnswer).answer.trim().toLowerCase()
  )
}

export function useQuestions(courseId: string) {
  const { data, refetch } = useApiFetch<QuestionData[]>(
    `/api/courses/${courseId}/questions`,
    {
      staleTime: 600_000,
    },
  )
  const questions = data ?? []
  const [state, setState] = useState<QuizState>("start")
  const [step, setStep] = useState(0)
  const [score, setScore] = useState(0)
  const [selected, setSelected] = useState<number | string | null>(null)

  const current = questions[step]
  const type = current?.type ?? null
  const total = questions.length
  const choices =
    type === "choice" ? (current.answer as QuestionChoicesAnswer).choices : []
  const expectedAnswer = current
    ? type === "choice"
      ? (current.answer as QuestionChoicesAnswer).answer
      : (current.answer as QuestionTextAnswer).answer
    : ""
  const question = current?.question ?? ""

  const isCorrect =
    selected !== null && current ? checkAnswer(current, selected) : null
  const percentage = total > 0 ? Math.round((score / total) * 100) : 0

  // Answer a question or start the quiz
  const answer = useCallback(
    (value: number | string) => {
      // Prevent answer in unexpected stats
      if (state !== "question" || !current || value === undefined) {
        return
      }

      // Increment the score if the user was right
      if (checkAnswer(current, value)) {
        setScore((s) => s + 1)
      }
      setSelected(value)
      setState("answer")
    },
    [state, current],
  )

  // Move to the next question
  const next = useCallback(() => {
    // Start the quiz (called without value)
    if (state === "start") {
      setState("question")
      return
    }
    // Do nothing if the user has not answered
    if (state !== "answer") {
      return
    }
    // Use reached the end of the quiz
    if (step === questions.length - 1) {
      setState("end")
      return
    }
    // Increment the step
    setStep((s) => s + 1)
    setSelected(null)
    setState("question")
  }, [state, step, questions.length])

  const restart = useCallback(() => {
    setState("start")
    setStep(0)
    setScore(0)
    setSelected(null)
    refetch()
  }, [refetch])

  return {
    answer,
    next,
    restart,
    state,
    score,
    step,
    total,
    expectedAnswer,
    question,
    type,
    choices,
    selected,
    isCorrect,
    percentage,
  }
}

export type Quiz = ReturnType<typeof useQuestions>
