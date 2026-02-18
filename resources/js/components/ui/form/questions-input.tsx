import Ajv from "ajv"
import {
  BadgeQuestionMarkIcon,
  BotIcon,
  CircleCheckIcon,
  PencilIcon,
  PlusIcon,
  TrashIcon,
} from "lucide-react"
import { type FormEventHandler, useEffect, useRef, useState } from "react"
import { toast } from "sonner"
import {
  destroy,
  importMethod,
  index,
  store,
  update,
} from "@/actions/App/Http/Cms/QuestionController"
import { FormField } from "@/components/form-field.tsx"
import { Badge } from "@/components/ui/badge.tsx"
import { Button } from "@/components/ui/button.tsx"
import { CopyButton } from "@/components/ui/copy-button.tsx"
import { Drawer } from "@/components/ui/drawer.tsx"
import { Input } from "@/components/ui/input.tsx"
import { NativeSelect } from "@/components/ui/native-select.tsx"
import { RadioGroup, RadioGroupItem } from "@/components/ui/radio-group.tsx"
import { Skeleton } from "@/components/ui/skeleton.tsx"
import { Spinner } from "@/components/ui/spinner.tsx"
import { apiFetch, useApiFetch, useApiMutation } from "@/hooks/use-api-fetch.ts"
import { times } from "@/lib/array.ts"
import { formToObject } from "@/lib/dom.ts"
import { importSchema } from "@/lib/schema/questions-schema.ts"
import type { QuestionChoicesAnswer, QuestionData } from "@/types"

type Props = {
  courseId: number
  questionsCount: number
}

export function QuestionsInput({ courseId, questionsCount }: Props) {
  const [open, setOpen] = useState(false)

  return (
    <Drawer
      open={open}
      side="bottom"
      onOpenChange={setOpen}
      width={1024}
      trigger={
        <button
          className="flex items-center gap-2 fixed bottom-0 left-0 right-0 w-max px-6 py-2 mx-auto bg-card rounded-t-lg z-500 border shadow-lg outline-none focus:border-primary transition border-b-0"
          type="button"
        >
          <BadgeQuestionMarkIcon className="size-4 -mt-0.5 text-muted-foreground" />
          {questionsCount > 0
            ? `${questionsCount} question${questionsCount > 1 ? "s" : ""}`
            : "Ajouter des questions"}
        </button>
      }
    >
      <QuestionsManager courseId={courseId} />
    </Drawer>
  )
}

function parseQuestionsJson(
  text: string,
): { data: QuestionData[] } | { error: string } | null {
  let data: unknown
  try {
    data = JSON.parse(text)
  } catch {
    return null
  }

  if (validateImport(data)) {
    return { data }
  }

  const firstError = validateImport.errors?.[0]
  const error = firstError
    ? `${firstError.instancePath || "/"} ${firstError.message}`
    : "Format invalide"

  return { error }
}

function QuestionsManager({ courseId }: { courseId: number }) {
  const {
    data: questions,
    isLoading,
    setData,
  } = useApiFetch<QuestionData[]>(index.url(courseId), {
    staleTime: 600_000,
  })
  const [editing, setEditing] = useState<number | "new" | null>(null)
  const { mutate, isPending: isImporting } = useApiMutation<QuestionData[]>(
    importMethod(courseId).url,
    { method: "POST" },
  )

  // Allow pasting JSON to import questions
  useEffect(() => {
    const handlePaste = async (e: ClipboardEvent) => {
      const text = e.clipboardData?.getData("text/plain")
      if (!text) {
        return
      }

      const pasted = parseQuestionsJson(text)
      // The pasted content is not JSON let the normal behaviour happens
      if (!pasted) {
        return
      }

      e.preventDefault()
      if ("error" in pasted) {
        toast.error(pasted.error)
        return
      }

      mutate(pasted.data, {
        onError() {
          toast.error("Erreur lors de l'import des questions")
        },
        onSuccess(data) {
          setData((prev) => [...(prev ?? []), ...data])
          toast.success(
            `${data.length} question${data.length > 1 ? "s" : ""} importée${data.length > 1 ? "s" : ""}`,
          )
        },
      })
    }

    document.addEventListener("paste", handlePaste)
    return () => document.removeEventListener("paste", handlePaste)
  })

  const handleSaved = (question: QuestionData) => {
    setData((prev) => {
      if (!prev) {
        return [question]
      }
      const exists = prev.find((q) => q.id === question.id)
      if (exists) {
        return prev.map((q) => (q.id === question.id ? question : q))
      }
      return [...prev, question]
    })
    setEditing(null)
  }

  const handleDelete = async (questionId: number) => {
    await apiFetch(destroy.url(questionId), { method: "DELETE" })
    setData((prev) => (prev ? prev.filter((q) => q.id !== questionId) : []))
  }

  if (isLoading || isImporting) {
    return (
      <div className="py-8 space-y-3 max-w-5xl mx-auto">
        {times(10, (k) => (
          <Skeleton key={k} className="w-full h-12" />
        ))}
      </div>
    )
  }

  return (
    <div className="space-y-3">
      <div className="relative">
        <PromptDrawer />
      </div>
      {questions?.map((question) =>
        editing === question.id ? (
          <QuestionForm
            key={question.id}
            courseId={courseId}
            question={question}
            onSaved={handleSaved}
            onCancel={() => setEditing(null)}
          />
        ) : (
          <QuestionRow
            key={question.id}
            question={question}
            onEdit={() => setEditing(question.id)}
            onDelete={() => handleDelete(question.id)}
          />
        ),
      )}
      {editing === "new" ? (
        <QuestionForm
          courseId={courseId}
          onSaved={handleSaved}
          onCancel={() => setEditing(null)}
        />
      ) : (
        <Button
          type="button"
          variant="secondary"
          className="w-full"
          onClick={() => setEditing("new")}
        >
          <PlusIcon /> Ajouter une question
        </Button>
      )}
    </div>
  )
}

function QuestionRow({
  question,
  onEdit,
  onDelete,
}: {
  question: QuestionData
  onEdit: () => void
  onDelete: () => void
}) {
  return (
    <div className="flex items-center rounded-lg border p-3 gap-1">
      <span
        className="flex-1 min-w-0 font-medium text-sm cursor-pointer"
        onClick={onEdit}
      >
        {question.question}
      </span>
      <Badge variant="secondary" className="ml-auto">
        {question.type === "choice" ? "Choix" : "Texte"}
      </Badge>
      <Button type="button" variant="ghost" size="icon-sm" onClick={onEdit}>
        <PencilIcon />
      </Button>
      <Button
        type="button"
        variant="ghost"
        className="text-destructive -mr-0.5"
        size="icon-sm"
        onClick={onDelete}
      >
        <TrashIcon />
      </Button>
    </div>
  )
}

function QuestionForm({
  courseId,
  question,
  onSaved,
  onCancel,
}: {
  courseId: number
  question?: QuestionData
  onSaved: (q: QuestionData) => void
  onCancel: () => void
}) {
  const [type, setType] = useState<string>(question?.type ?? "choice")
  const { mutate, isPending } = useApiMutation<QuestionData>(
    question ? update.url(question.id) : store.url(courseId),
    {
      method: question ? "PUT" : "POST",
    },
  )

  const handleSubmit: FormEventHandler<HTMLFormElement> = (e) => {
    e.preventDefault()
    mutate(formToObject(e.currentTarget), {
      onError(e) {
        toast.error(e.toString())
      },
      onSuccess: onSaved,
    })
  }

  return (
    <form className="space-y-3 rounded-lg border p-3" onSubmit={handleSubmit}>
      <div className="flex items-center gap-2">
        <Input
          autoFocus
          name="question"
          placeholder="Question"
          defaultValue={question?.question}
        />
        <NativeSelect
          name="type"
          value={type}
          onValueChange={setType}
          className="w-40"
        >
          <option value="choice">Choix multiple</option>
          <option value="text">Texte libre</option>
        </NativeSelect>
      </div>

      {type === "choice" ? (
        <ChoiceEditor
          choices={
            (question?.answer as QuestionChoicesAnswer)?.choices ?? ["", ""]
          }
          answer={(question?.answer as QuestionChoicesAnswer)?.answer ?? 0}
        />
      ) : (
        <FormField
          placeholder="Réponse attendue"
          name="answer.answer"
          defaultValue={question?.answer?.answer}
        />
      )}

      <div className="flex justify-end gap-2">
        <Button type="button" variant="secondary" onClick={onCancel}>
          Annuler
        </Button>
        <Button disabled={isPending} type="submit">
          {isPending ? (
            <Spinner />
          ) : (
            <>
              <CircleCheckIcon /> Enregistrer
            </>
          )}
        </Button>
      </div>
    </form>
  )
}

function ChoiceEditor(props: { choices: string[]; answer: number }) {
  const [choices, setChoices] = useState(props.choices)
  const [answer, setAnswer] = useState(props.answer)

  const removeChoice = (index: number) => {
    setChoices(choices.filter((_, i) => i !== index))
    setAnswer(answer === index ? 0 : answer > index ? answer - 1 : answer)
  }

  const addChoice = () => {
    setChoices([...choices, ""])
  }

  return (
    <RadioGroup value={answer} onValueChange={setAnswer} name="answer.answer">
      <div className="space-y-2">
        {choices.map((choice, i) => (
          <div key={i} className="flex items-center gap-2">
            <RadioGroupItem value={i} className="shrink-0" />
            <Input
              placeholder={`Choix ${i + 1}`}
              name={`answer.choices.${i}`}
              value={choice}
            />
            {choices.length > 2 && (
              <Button
                type="button"
                variant="destructive"
                size="icon"
                onClick={() => removeChoice(i)}
              >
                <TrashIcon />
              </Button>
            )}
          </div>
        ))}
        <Button
          type="button"
          variant="secondary"
          onClick={addChoice}
          className="w-full"
        >
          <PlusIcon /> Ajouter un choix
        </Button>
      </div>
    </RadioGroup>
  )
}

function PromptDrawer() {
  const ref = useRef<HTMLDivElement>(null)
  return (
    <Drawer
      side="bottom"
      width={1024}
      trigger={
        <Button variant="ghost" size="icon" className="absolute -top-10">
          <BotIcon />
        </Button>
      }
      actions={<CopyButton text={() => ref.current?.innerText ?? ""} />}
    >
      <div className="text-lg" ref={ref}>
        <p className="mb-4">
          Tu es un formateur chargé de générer un quiz pour les personnes qui
          viennent de regarder un cours. La réponse à la question doit être
          incluse dans la transcription. Tu ne peux pas utiliser de
          connaissances que le spectateur aurait acquises en dehors de la vidéo.
        </p>
        <p className="mb-4">
          Ta réponse doit être uniquement en JSON en respectant le schéma
          fourni. Génère les questions en français. Choisi le bon type de
          question en fonction.
        </p>
        <pre className="bg-sidebar border rounded-md p-4">
          <code>
            ```{"\n"}
            {JSON.stringify(importSchema, null, 2)}
            {"\n"}
            ```
          </code>
        </pre>
      </div>
    </Drawer>
  )
}

const ajv = new Ajv({ allErrors: true, discriminator: true })
const validateImport = ajv.compile<QuestionData[]>(importSchema)
