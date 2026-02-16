import { CogIcon, SaveIcon } from "lucide-react"
import route from "@/actions/App/Http/Cms/PostController"
import { Form } from "@/components/form.tsx"
import { FormField } from "@/components/form-field.tsx"
import { withLayout } from "@/components/layout.tsx"
import { PageTitle } from "@/components/page-title.tsx"
import { Button } from "@/components/ui/button.tsx"
import { DatetimePicker } from "@/components/ui/form/datetime-picker.tsx"
import type { SettingsFormData } from "@/types"

type Props = {
  settings: SettingsFormData
}

export default withLayout<Props>(
  ({ settings }) => {
    const formAction = route.store()
    return (
      <Form id="form" {...formAction} className="space-y-2">
        <PageTitle>Paramètres</PageTitle>
        <h1 className="mb-4 flex items-center gap-2 font-semibold text-xl">
          <CogIcon className="text-primary" />
          Paramètres
        </h1>
        <FormField
          type="date"
          label="Live"
          name="live_at"
          render={<DatetimePicker />}
          defaultValue={settings.liveAt}
        />
        <FormField
          type="textarea"
          label="Mots spam"
          name="spam_words"
          defaultValue={settings.spamWords}
        />
      </Form>
    )
  },
  {
    breadcrumb: () => [{ label: "Paramètres", href: route.index() }],
    top: (
      <Button form="form" type="submit">
        <SaveIcon /> Enregistrer
      </Button>
    ),
  },
)
