import { SaveIcon } from "lucide-react"
import route from "@/actions/App/Http/Cms/SupportController"
import { Form } from "@/components/form.tsx"
import { FormField } from "@/components/form-field.tsx"
import { withLayout } from "@/components/layout.tsx"
import { PageTitle } from "@/components/page-title.tsx"
import { Button } from "@/components/ui/button.tsx"
import {
  Card,
  CardContent,
  CardHeader,
  CardTitle,
} from "@/components/ui/card.tsx"
import { Field, FieldLabel } from "@/components/ui/field.tsx"
import { MDEditor } from "@/components/ui/form/mdeditor.tsx"
import {
  ValidationError,
  ValidationErrors,
} from "@/components/ui/form/validation-error.tsx"
import { Label } from "@/components/ui/label.tsx"
import { Switch } from "@/components/ui/switch.tsx"
import { formatDate, formatDuration } from "@/lib/date.ts"
import type { SupportQuestionFormData } from "@/types"

type Props = {
  item: SupportQuestionFormData
}

export default withLayout<Props>(
  ({ item }) => {
    return (
      <Form
        className="grid gap-6 lg:grid-cols-[minmax(0,1fr)_320px]"
        id="form"
        {...route.update.form(item.id)}
      >
        <PageTitle>{item.title}</PageTitle>
        <main className="space-y-6">
          <ValidationErrors />

          <FormField
            name="title"
            label="Titre"
            defaultValue={item.title}
            placeholder="Titre de la question"
          />
          <FormField
            name="content"
            label="Contenu"
            defaultValue={item.content}
            placeholder="Détails de la question"
            type="textarea"
          />

          <Field>
            <FieldLabel htmlFor="answer">Réponse</FieldLabel>
            <MDEditor defaultValue={item.answer} name="answer" />
            <ValidationError name="answer" />
          </Field>
        </main>

        <aside className="space-y-6">
          <div className="flex justify-end">
            <div className="flex items-center gap-2">
              <Switch id="online" name="online" defaultChecked={item.online} />
              <Label htmlFor="online">En ligne</Label>
            </div>
          </div>

          <Card>
            <CardHeader>
              <CardTitle>Contexte</CardTitle>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="space-y-1 text-sm">
                <div className="text-muted-foreground">
                  {formatDate(item.createdAt)}
                </div>
              </div>

              <FormField
                label="Timestamp"
                name="timestamp"
                type="number"
                min={0}
                defaultValue={item.timestamp}
                right={
                  <span className="text-muted-foreground text-xs">
                    {formatDuration(item.timestamp)}
                  </span>
                }
              />
            </CardContent>
          </Card>
        </aside>
      </Form>
    )
  },
  {
    breadcrumb: (props) => [
      { label: "Support", href: route.index() },
      { label: props.item.title },
    ],
    top: (
      <Button form="form" type="submit">
        <SaveIcon /> Enregistrer
      </Button>
    ),
  },
)
