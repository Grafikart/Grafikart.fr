import { QueryClientProvider } from "@tanstack/react-query"
import { type FormEventHandler, type MouseEvent, useRef, useState } from "react"
import {
  AlertDialog,
  AlertDialogAction,
  AlertDialogCancel,
  AlertDialogContent,
  AlertDialogDescription,
  AlertDialogFooter,
  AlertDialogHeader,
  AlertDialogTitle,
} from "@/components/ui/alert-dialog.tsx"
import { Button } from "@/components/ui/button.tsx"
import { FormField } from "@/components/form-field.tsx"
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from "@/components/ui/dialog.tsx"
import { queryClient, useApiMutation } from "@/hooks/use-api-fetch.ts"
import { toast, Toaster } from "sonner"

type Props = {
  action: string
  "example-url": string
  credits: number
  subject: string
  message: string
}

type ImportPreviewResponse = {
  count: number
  months: number
  credits: number
  left: number
}

type ImportConfirmedResponse = {
  success: true
}

type State = "form" | "confirm" | "end"

export default function StudentImporterRoot(props: Props) {
  return (
    <QueryClientProvider client={queryClient}>
      <Toaster position="top-center" richColors closeButton />
      <StudentImporter {...props} />
    </QueryClientProvider>
  )
}

function StudentImporter({
  action,
  "example-url": exampleUrl,
  credits,
  subject,
  message,
}: Props) {
  const [state, setState] = useState<State>("form")
  const formRef = useRef<HTMLFormElement>(null)
  const [previewResponse, setPreviewResponse] =
    useState<ImportPreviewResponse | null>(null)

  const { mutate, isPending } = useApiMutation<
    ImportPreviewResponse | ImportConfirmedResponse,
    FormData
  >(
    action,
    {
      method: "POST",
    },
    {
      onError(e) {
        toast.error(e.message)
      },
    },
  )

  const xlsxExampleUrl = exampleUrl.replace(".csv", ".xslsx")
  const importedMonths = previewResponse?.months ?? 0
  const remainingCredits = previewResponse?.left ?? credits - importedMonths

  const onPreviewSubmit: FormEventHandler<HTMLFormElement> = (event) => {
    event.preventDefault()

    if (isPending) {
      return
    }

    const payload = new FormData(event.currentTarget)

    mutate(payload, {
      onSuccess(response) {
        if (isPreviewResponse(response)) {
          setPreviewResponse(response as ImportPreviewResponse)
          setState("confirm")
        } else {
          toast.error("Réponse serveur inattendue")
        }
      },
    })
  }

  const onConfirmImport = (event: MouseEvent<HTMLButtonElement>) => {
    event.preventDefault()

    if (isPending || !formRef.current) {
      return
    }

    const payload = new FormData(formRef.current)
    payload.set("confirmed", "1")

    mutate(payload, {
      onSuccess() {
        setState("end")
      },
    })
  }

  return (
    <>
      <form
        ref={formRef}
        action={action}
        className="space-y-4 mt-4"
        encType="multipart/form-data"
        method="post"
        onSubmit={onPreviewSubmit}
      >
        <div className="space-y-1">
          <FormField
            label="Fichier CSV"
            name="csv"
            type="file"
            required
            help={
              <>
                <a href={exampleUrl} download target="_blank" rel="noreferrer">
                  Fichier d'exemple
                </a>{" "}
                (format{" "}
                <a
                  href={xlsxExampleUrl}
                  download
                  target="_blank"
                  rel="noreferrer"
                >
                  xlsx
                </a>
                ), les en-têtes doivent être présentes.
              </>
            }
          />
        </div>

        <FormField
          label="Sujet de l'email"
          name="subject"
          required
          defaultValue={subject ?? ""}
        />

        <FormField
          label="Message"
          name="message"
          type="textarea"
          required
          className="min-h-40"
          defaultValue={message ?? ""}
        />

        <Button type="submit" disabled={isPending}>
          {isPending ? "Vérification..." : "Importer"}
        </Button>
      </form>

      <AlertDialog
        open={state === "confirm"}
        onOpenChange={() => setState("form")}
      >
        <AlertDialogContent className="max-w-100">
          <AlertDialogHeader className="items-start text-left">
            <AlertDialogTitle>Confirmer l'import</AlertDialogTitle>
            <AlertDialogDescription className="text-foreground text-base leading-relaxed">
              Vous êtes sur le point d'importer{" "}
              <strong className="text-foreground-title bold">
                {previewResponse?.count ?? 0} étudiants
              </strong>{" "}
              ({importedMonths} mois de compte premium). Il vous restera{" "}
              <strong className="text-foreground-title bold">
                {remainingCredits} mois
              </strong>{" "}
              après cette opération
            </AlertDialogDescription>
          </AlertDialogHeader>
          <AlertDialogFooter className="bg-background-light">
            <AlertDialogCancel disabled={isPending}>Annuler</AlertDialogCancel>
            <AlertDialogAction disabled={isPending} onClick={onConfirmImport}>
              {isPending ? "Import en cours..." : "Confirmer"}
            </AlertDialogAction>
          </AlertDialogFooter>
        </AlertDialogContent>
      </AlertDialog>

      <Dialog open={state === "end"}>
        <DialogContent
          className="max-w-110 overflow-visible text-foreground"
          showCloseButton={false}
        >
          <div className="text-center -mt-32">
            <img
              src="/images/illustrations/success.svg"
              alt=""
              className="inline max-w-75"
            />
          </div>
          <DialogHeader className="text-center items-center">
            <DialogTitle className="text-4xl text-foreground-title font-bold font-serif mt-4 mb-2">
              Félicitations !
            </DialogTitle>
            <DialogDescription className="text-lg text-pretty mb-4 text-foreground">
              Import des {previewResponse?.count} étudiants terminé avec succès.
            </DialogDescription>
          </DialogHeader>
          <DialogFooter className="bg-transparent border-none pt-0 justify-center">
            <Button
              className="w-full"
              type="button"
              size="lg"
              onClick={() => window.location.reload()}
            >
              Fermer
            </Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>
    </>
  )
}

function isPreviewResponse(
  response: ImportPreviewResponse | ImportConfirmedResponse,
): response is ImportPreviewResponse {
  return "count" in response && "months" in response
}
