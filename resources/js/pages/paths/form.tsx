import { ReactFlowProvider } from "@xyflow/react"
import { SaveIcon } from "lucide-react"
import route from "@/actions/App/Http/Cms/PathController"
import { Form } from "@/components/form.tsx"
import { FormField } from "@/components/form-field.tsx"
import { withLayout } from "@/components/layout.tsx"
import { PageTitle } from "@/components/page-title.tsx"
import { Button } from "@/components/ui/button.tsx"
import { Card, CardContent } from "@/components/ui/card.tsx"
import { DatetimePicker } from "@/components/ui/form/datetime-picker.tsx"
import { Input } from "@/components/ui/input.tsx"
import { NodesInput } from "@/components/ui/form/nodes-input/nodes-input.tsx"
import { SlugInput } from "@/components/ui/form/slug-input.tsx"
import { Label } from "@/components/ui/label.tsx"
import { Switch } from "@/components/ui/switch.tsx"
import { Textarea } from "@/components/ui/textarea.tsx"
import type { PathFormData } from "@/types"

type Props = {
  item: PathFormData
}

export default withLayout<Props>(
  ({ item }) => {
    const formAction = item.id ? route.update.form(item.id) : route.store.form()
    const url = item.id ? `/tutoriels/${item.slug}-${item.id}` : undefined

    return (
      <Form className="space-y-4" id="form" {...formAction}>
        <PageTitle>{item.title || "Nouveau parcours"}</PageTitle>
        <div className="grid grid-cols-[1fr_300px] gap-6">
          <main className="space-y-4">
            <input
              name="title"
              defaultValue={item.title}
              className="mb-1 block w-full font-semibold text-2xl outline-none"
              placeholder="Titre"
            />
            <div className="flex justify-between align-center mb-3">
              <SlugInput
                defaultValue={item.slug}
                prefix="grafikart.fr/tutoriels/"
                url={url}
              />
              <FormField
                name="tags"
                wrapperClass="max-w-75 -mt-4"
                render={
                  <Input
                    name="tags"
                    defaultValue={item.tags}
                    placeholder="Tag1,Tag2,Tag3..."
                  />
                }
              />
            </div>
            <Textarea defaultValue={item.description} name="description" />
          </main>
          <aside className="space-y-6">
            <div className="flex justify-end gap-4">
              <div className="flex items-center space-x-2">
                <Switch
                  id="online"
                  name="online"
                  defaultChecked={item.online}
                />
                <Label htmlFor="online">En ligne</Label>
              </div>
            </div>

            <Card>
              <CardContent className="space-y-4">
                <FormField
                  label="Publié le"
                  name="createdAt"
                  defaultValue={item.createdAt}
                  render={<DatetimePicker />}
                />
              </CardContent>
            </Card>
          </aside>
        </div>
        <ReactFlowProvider>
          <NodesInput defaultValue={item.nodes} />
        </ReactFlowProvider>
      </Form>
    )
  },
  {
    breadcrumb: (props) => [
      { label: "Parcours", href: route.index() },
      { label: props.item.title || "Nouveau parcours" },
    ],
    top: (
      <Button form="form" type="submit">
        <SaveIcon /> Enregistrer
      </Button>
    ),
  },
)
