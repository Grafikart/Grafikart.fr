import { LinkIcon, SaveIcon } from "lucide-react"
import route from "@/actions/App/Http/Cms/SponsorController"
import { Form } from "@/components/form.tsx"
import { FormField } from "@/components/form-field.tsx"
import { withLayout } from "@/components/layout.tsx"
import { PageTitle } from "@/components/page-title.tsx"
import { Card, CardContent } from "@/components/ui/card.tsx"
import { ImageInput } from "@/components/ui/form/image-input.tsx"
import {
  NativeSelect,
  NativeSelectOption,
} from "@/components/ui/native-select.tsx"
import type { SponsorFormData, SponsorType } from "@/types"
import { MDEditor } from "@/components/ui/form/mdeditor.tsx"
import { DatetimePicker } from "@/components/ui/form/datetime-picker.tsx"
import { Button } from "@/components/ui/button.tsx"
import { cn } from "@/lib/utils.ts"

type Props = {
  item: SponsorFormData
  types: { value: SponsorType; label: string }[]
}

export default withLayout<Props>(
  ({ item, types }) => {
    const formAction = item.id ? route.update.form(item.id) : route.store.form()

    return (
      <Form
        className="grid grid-cols-[1fr_300px] gap-4"
        id="form"
        {...formAction}
        encType="multipart/form-data"
      >
        <PageTitle>{item.name || "Nouveau sponsor"}</PageTitle>
        <main className="space-y-4">
          <input
            name="name"
            defaultValue={item.name}
            className="block w-full font-semibold text-2xl outline-none"
            placeholder="Nom du sponsor"
          />
          <UrlInput defaultValue={item.url} className="mb-3" />
          <MDEditor defaultValue={item.content} name="content" />
        </main>
        <aside className="space-y-4">
          <Card className="pt-0">
            <ImageInput
              defaultValue={item.logo ?? undefined}
              name="logoFile"
              className="aspect-video"
            />
            <CardContent className="space-y-4">
              <FormField label="Type" name="type">
                <NativeSelect
                  id="type"
                  name="type"
                  defaultValue={item.type}
                  className="w-full"
                >
                  {types.map((t) => (
                    <NativeSelectOption key={t.value} value={t.value}>
                      {t.label}
                    </NativeSelectOption>
                  ))}
                </NativeSelect>
              </FormField>
              <FormField
                label="Créé le"
                name="createdAt"
                defaultValue={item.createdAt}
                render={<DatetimePicker />}
              />
            </CardContent>
          </Card>
        </aside>
      </Form>
    )
  },
  {
    breadcrumb: (props) => [
      { label: "Sponsors", href: route.index() },
      { label: props.item.name || "Nouveau sponsor" },
    ],
    top: (
      <Button form="form" type="submit">
        <SaveIcon /> Enregistrer
      </Button>
    ),
  },
)

type SlugInputProps = {
  defaultValue: string
  className?: string
}

function UrlInput({ defaultValue, className }: SlugInputProps) {
  return (
    <div
      className={cn(
        "flex text-sm text-muted-foreground items-center",
        className,
      )}
    >
      <span className="opacity-50">url:</span>
      <input
        type="text"
        name="url"
        placeholder="https://"
        defaultValue={defaultValue}
        className="outline-none field-sizing-content w-full"
      />
      {defaultValue && (
        <Button
          nativeButton={false}
          variant="ghost"
          render={<a target="_blank" href={defaultValue} />}
          size="icon-xs"
        >
          <LinkIcon />
        </Button>
      )}
    </div>
  )
}
