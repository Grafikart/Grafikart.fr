import { QueryClientProvider } from "@tanstack/react-query"
import { ChevronDownIcon, PlayIcon, StarIcon } from "lucide-react"
import { type FormEventHandler } from "react"
import { Button } from "@/components/ui/button.tsx"
import { Card } from "@/components/ui/card.tsx"
import { Input } from "@/components/ui/input.tsx"
import { Spinner } from "@/components/ui/spinner.tsx"
import { Textarea } from "@/components/ui/textarea.tsx"
import {
  queryClient,
  useApiFetch,
  useApiMutation,
} from "@/hooks/use-api-fetch.ts"
import { isPremium } from "@/lib/auth.ts"
import { formatRelative } from "@/lib/date.ts"
import { SupportQuestionData } from "@/types"
import { toast, Toaster } from "sonner"
import { Alert } from "@/components/ui/alert.tsx"
import { useVisible } from "@/hooks/use-visible.ts"
import { useToggle } from "@/hooks/use-toggle.ts"
import { clsx } from "clsx"
import { Nl2br } from "@/components/nl2br.tsx"
import { Badge } from "@/components/ui/badge.tsx"

type Props = {
  course: string | null
}

export default function SupportCourse(props: Props) {
  const endpoint = `/api/courses/${props.course}/support`
  return (
    <QueryClientProvider client={queryClient}>
      <Toaster position="top-center" richColors closeButton />
      <h2 className="font-serif font-bold text-4xl text-foreground-title mb-2">
        Support & Questions
      </h2>
      <p className="text-muted mb-4">
        Posez vos questions sur ce cours et consultez les réponses publiées.
      </p>

      <QuestionComposer endpoint={endpoint} />
      <Questions endpoint={endpoint} />
    </QueryClientProvider>
  )
}

function Questions({ endpoint }: { endpoint: string }) {
  const { ref, isVisible } = useVisible<HTMLDivElement>({ once: true })
  const { data, isFetching } = useApiFetch<SupportQuestionData[]>(endpoint, {
    enabled: isVisible,
  })
  const questions = data ?? []
  const myQuestions = questions.filter((q) => q.me)
  const allQuestions = questions.filter((q) => !q.me)

  return (
    <div ref={ref}>
      {myQuestions.length > 0 && (
        <div className="mt-8">
          <h2 className="font-serif font-bold text-4xl text-foreground-title mb-4">
            Mes questions
          </h2>
          {isFetching && (
            <div className="flex justify-center">
              <Spinner />
            </div>
          )}
          <div className="space-y-4">
            {myQuestions.map((question) => (
              <QuestionItem key={question.id} question={question} />
            ))}
          </div>
        </div>
      )}
      {allQuestions.length > 0 && (
        <div className="mt-8">
          <h2 className="font-serif font-bold text-4xl text-foreground-title mb-4">
            Toutes les questions
          </h2>
          {isFetching && (
            <div className="flex justify-center">
              <Spinner />
            </div>
          )}
          <div className="space-y-4">
            {allQuestions.map((question) => (
              <QuestionItem key={question.id} question={question} />
            ))}
          </div>
        </div>
      )}
    </div>
  )
}

function QuestionComposer({ endpoint }: { endpoint: string }) {
  const successMessage =
    "Merci pour votre question ! Vous recevrez un email dès que j'aurais une réponse"
  const { isPending, mutate, isSuccess } = useApiMutation<SupportQuestionData>(
    endpoint,
    {
      method: "POST",
    },
  )

  if (!isPremium()) {
    return (
      <Alert
        variant="warning"
        className="mt-4 text-md flex items-center justify-between"
      >
        <p> Pour poser une question vous devez être membre premium</p>
        <Button render={<a href="/premium" />}>
          <StarIcon />
          Devenir premium
        </Button>
      </Alert>
    )
  }

  const onSubmit: FormEventHandler<HTMLFormElement> = (e) => {
    e.preventDefault()
    const data = new FormData(e.currentTarget)
    data.set("timestamp", getCurrentCourseTimestamp().toString())
    mutate(data, {
      onError() {
        toast.error("Une erreur serveur est survenue")
      },
      onSuccess() {
        toast.success(successMessage)
      },
    })
  }
  const videoTimestamp = getCurrentCourseTimestamp()
  if (isSuccess) {
    return <Alert variant="success">{successMessage}</Alert>
  }
  return (
    <form className="space-y-4" onSubmit={onSubmit}>
      <div className="gap-4 flex items-end">
        <div className="space-y-2 flex-1">
          <label className="block text-sm font-medium" htmlFor="support-title">
            Titre
          </label>
          <Input
            name="title"
            id="support-title"
            placeholder="Ex. Pourquoi ce hook ne se déclenche pas ici ?"
            required
          />
        </div>
        <div className="flex flex-wrap items-center gap-3 rounded-lg bg-primary/6 p-1 text-sm w-max">
          <div className="rounded-md bg-primary/12 px-2 py-1 font-semibold text-primary">
            {formatVideoTimestamp(videoTimestamp)}
          </div>
        </div>
      </div>
      <div className="space-y-2">
        <label className="block text-sm font-medium" htmlFor="support-content">
          Détails
        </label>
        <Textarea
          name="content"
          id="support-content"
          placeholder="Ajoutez le contexte utile pour faciliter la réponse."
        />
      </div>

      <div className="flex items-center justify-between gap-4">
        <p className="text-sm text-muted">
          Votre question sera lu par un vrai humain (soyez patient ^^).
        </p>
        <Button disabled={isPending} type="submit" size="lg">
          {isPending ? "Envoi..." : "Poser ma question"}
        </Button>
      </div>
    </form>
  )
}

function QuestionItem({ question }: { question: SupportQuestionData }) {
  const [expanded, toggleExpanded] = useToggle(false)
  return (
    <Card className="overflow-hidden p-0 gap-2 border-l-primary border-l-2">
      {/* Question */}
      <div className="p-4 relative group space-y-2">
        <div className="flex flex-wrap items-center gap-2 text-xs text-muted">
          <a
            href={`#t${question.timestamp}`}
            className="inline-flex items-center gap-1 rounded-md bg-primary/12 px-2 py-1 font-semibold text-primary hover:bg-primary/18 z-3 relative"
          >
            <PlayIcon className="size-3" />
            {formatVideoTimestamp(question.timestamp)}
          </a>
          <span>•</span>
          <span>{formatRelative(question.createdAt)}</span>
          {question.answer ? (
            <button
              onClick={toggleExpanded}
              className="overlay ml-auto flex-1 flex justify-end hover:text-primary"
            >
              <ChevronDownIcon
                className={clsx(
                  "size-5 duration-300",
                  expanded && "rotate-180",
                )}
              />
            </button>
          ) : (
            <Badge className="ml-auto" variant="secondary">
              En attente de réponse
            </Badge>
          )}
        </div>
        <h3 className="text-lg font-semibold group-hover:text-primary">
          {question.title}
        </h3>
        {question.content && (
          <p className="leading-6 whitespace-pre-wrap text-foreground/85">
            <Nl2br text={question.content} />
          </p>
        )}
      </div>

      {/* Answer */}
      {question.answer && expanded && (
        <div className="border-t p-4">
          <div className="text-xs font-medium uppercase tracking-wide text-primary mb-1">
            Réponse
          </div>
          <div
            className="prose"
            dangerouslySetInnerHTML={{
              __html: question.answer,
            }}
          />
        </div>
      )}
    </Card>
  )
}

function getCurrentCourseTimestamp(): number {
  const element = document.querySelector<
    HTMLElement & { currentTime?: number }
  >(`course-video`)

  return Math.max(0, Math.round(element?.currentTime ?? 0))
}

function formatVideoTimestamp(totalSeconds: number): string {
  const hours = Math.floor(totalSeconds / 3600)
  const minutes = Math.floor((totalSeconds % 3600) / 60)
  const seconds = totalSeconds % 60

  if (hours > 0) {
    return `${hours}:${minutes.toString().padStart(2, "0")}:${seconds
      .toString()
      .padStart(2, "0")}`
  }

  return `${minutes.toString().padStart(2, "0")}:${seconds
    .toString()
    .padStart(2, "0")}`
}
