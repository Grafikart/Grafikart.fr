import { SaveIcon } from "lucide-react"
import path from "@/actions/App/Http/Cms/BlogCategoryController.ts"
import { Form } from "@/components/form.tsx"
import { FormField } from "@/components/form-field.tsx"
import { withLayout } from "@/components/layout.tsx"
import { PageTitle } from "@/components/page-title.tsx"
import { Button } from "@/components/ui/button.tsx"
import type { BlogCategoryData } from "@/types"

type Props = {
  item: BlogCategoryData
}

export default withLayout<Props>(
  ({ item }) => {
    return (
      <Form
        id="form"
        className="space-y-4"
        {...(item.id ? path.update.form(item.id) : path.store.form())}
      >
        <PageTitle>{item.name || "Nouvelle technologie"}</PageTitle>
        <FormField label="Nom" name="name" defaultValue={item.name} />
        <FormField label="Slug" name="slug" defaultValue={item.slug} />
      </Form>
    )
  },
  {
    breadcrumb: (props) => [
      { label: "Catégories", href: path.index() },
      { label: props.item.name || "Nouvelle technologie" },
    ],
    top: (
      <Button form="form" type="submit">
        <SaveIcon /> Enregistrer
      </Button>
    ),
  },
)
