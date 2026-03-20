import { SaveIcon } from "lucide-react"
import route from "@/actions/App/Http/Cms/PostController"
import { Form } from "@/components/form.tsx"
import { FormField } from "@/components/form-field.tsx"
import { withLayout } from "@/components/layout.tsx"
import { PageTitle } from "@/components/page-title.tsx"
import { Button } from "@/components/ui/button.tsx"
import { Card, CardContent } from "@/components/ui/card.tsx"
import { AttachmentSelector } from "@/components/ui/form/attachment-selector.tsx"
import { DatetimePicker } from "@/components/ui/form/datetime-picker.tsx"
import { MDEditor } from "@/components/ui/form/mdeditor.tsx"
import { SlugInput } from "@/components/ui/form/slug-input.tsx"
import { Label } from "@/components/ui/label.tsx"
import {
  NativeSelect,
  NativeSelectOption,
} from "@/components/ui/native-select.tsx"
import { Switch } from "@/components/ui/switch.tsx"
import type { OptionItemData, PostFormData } from "@/types"

type Props = {
  item: PostFormData
  categories: OptionItemData[]
}

export default withLayout<Props>(
  ({ item, categories }) => {
    const url = item.id ? `/blog/${item.slug}` : undefined
    const formAction = item.id ? route.update.form(item.id) : route.store.form()

    return (
      <Form
        className="grid grid-cols-[1fr_300px] gap-6"
        id="form"
        {...formAction}
      >
        <PageTitle>{item.title || "Nouvel article"}</PageTitle>
        <main>
          <input
            name="title"
            defaultValue={item.title}
            className="mb-1 block w-full font-semibold text-2xl outline-none"
            placeholder="Titre"
          />
          <SlugInput
            defaultValue={item.slug}
            prefix="grafikart.fr/blog/"
            className="mb-3"
            url={url}
          />
          <MDEditor defaultValue={item.content} name="content" />
        </main>
        <aside className="space-y-6">
          <div className="flex justify-end gap-4">
            <div className="flex items-center space-x-2">
              <Switch id="online" name="online" defaultChecked={item.online} />
              <Label htmlFor="online">En ligne</Label>
            </div>
          </div>

          <Card className="overflow-hidden pt-0">
            <AttachmentSelector
              name="attachmentId"
              className="aspect-video"
              attachableType="Post"
              attachableId={item.id}
              defaultValue={item.attachment?.id}
              preview={item.attachment?.url}
            />
            <CardContent className="space-y-4">
              <FormField
                label="Publié le"
                name="createdAt"
                defaultValue={item.createdAt}
                render={<DatetimePicker />}
              />
              <FormField label="Catégorie" name="categoryId">
                <NativeSelect
                  name="categoryId"
                  defaultValue={item.categoryId?.toString() ?? ""}
                  className="w-full"
                >
                  <NativeSelectOption value="">Aucune</NativeSelectOption>
                  {categories.map((cat) => (
                    <NativeSelectOption key={cat.id} value={cat.id}>
                      {cat.name}
                    </NativeSelectOption>
                  ))}
                </NativeSelect>
              </FormField>
            </CardContent>
          </Card>
        </aside>
      </Form>
    )
  },
  {
    breadcrumb: (props) => [
      { label: "Articles", href: route.index() },
      { label: props.item.title || "Nouveau" },
    ],
    top: (
      <Button form="form" type="submit">
        <SaveIcon /> Enregistrer
      </Button>
    ),
  },
)
