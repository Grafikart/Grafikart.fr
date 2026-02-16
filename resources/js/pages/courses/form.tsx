import { CircleCheckIcon, SaveIcon, UploadIcon } from "lucide-react"
import {
  default as CourseController,
  default as route,
} from "@/actions/App/Http/Cms/CourseController"
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
import { AttachmentSelector } from "@/components/ui/form/attachment-selector.tsx"
import { DatetimePicker } from "@/components/ui/form/datetime-picker.tsx"
import { LevelSelector } from "@/components/ui/form/level-selector.tsx"
import { MDEditor } from "@/components/ui/form/mdeditor.tsx"
import { SlugInput } from "@/components/ui/form/slug-input.tsx"
import { TechnologySelector } from "@/components/ui/form/technology-selector.tsx"
import { Input } from "@/components/ui/input.tsx"
import { Label } from "@/components/ui/label.tsx"
import { Switch } from "@/components/ui/switch.tsx"
import type { CourseFormData } from "@/types"

type Props = {
  item: CourseFormData
}

function ItemForm({ item }: Props) {
  const url = item.id ? `/tutoriels/${item.slug}-${item.id}` : undefined
  const formAction = item.id ? route.update.form(item.id) : route.store.form()

  return (
    <Form
      className="grid grid-cols-[1fr_300px] gap-4"
      id="form"
      {...formAction}
    >
      <PageTitle>{item.title || "Nouveau tutoriel"}</PageTitle>
      <main>
        <input
          name="title"
          defaultValue={item.title}
          className="mb-1 block font-semibold text-2xl outline-none"
          placeholder="Titre"
        />
        <SlugInput
          defaultValue={item.slug}
          prefix="grafikart.fr/tutoriels/"
          url={url}
        />
        <MDEditor defaultValue={item.content} name="content" />
      </main>
      <aside className="space-y-6">
        <div className="flex justify-end gap-4">
          <div className="flex items-center space-x-2">
            <Switch id="premium" name="premium" defaultChecked={item.premium} />
            <Label htmlFor="premium">Premium</Label>
          </div>
          <div className="flex items-center space-x-2">
            <Switch id="online" name="online" defaultChecked={item.online} />
            <Label htmlFor="online">En ligne</Label>
          </div>
        </div>
        {/* Métadonnées */}
        <Card className="overflow-hidden pt-0">
          <div className="grid grid-cols-2 gap-1">
            <AttachmentSelector
              name="image"
              className="aspect-8/9"
              attachableType="Course"
              attachableId={item.id}
              defaultValue={item.attachment?.id}
              preview={item.attachment?.url}
            />
            <AttachmentSelector
              name="youtubeThumbnail"
              className="aspect-8/9"
              attachableId={item.id}
              attachableType="Course"
              defaultValue={item.youtubeThumbnail?.id}
              preview={item.youtubeThumbnail?.url}
            />
          </div>
          <CardContent className="space-y-4">
            <FormField
              label="Publié le"
              name="createdAt"
              defaultValue={item.createdAt}
              render={<DatetimePicker />}
            />
            <FormField label="Vidéo" name="videoPath">
              <div className="flex items-center gap-2">
                <Input
                  name="videoPath"
                  defaultValue={item.videoPath}
                  id="videoPath"
                />
                {item.id && (
                  <Button
                    render={
                      <a
                        href={
                          CourseController.upload({
                            query: {
                              state: item.id,
                            },
                          }).url
                        }
                      />
                    }
                    type="button"
                    variant="secondary"
                    size="icon"
                  >
                    <UploadIcon />
                  </Button>
                )}
              </div>
            </FormField>
            <FormField
              label="Source"
              right={
                item.source && (
                  <CircleCheckIcon className="size-4 text-primary" />
                )
              }
              type="file"
              name="source"
            />
          </CardContent>
        </Card>

        {/* Technologies */}
        <Card>
          <CardContent>
            <FormField
              label="Outils & Langages"
              name="usages"
              render={
                <TechnologySelector defaultValue={item.technologies ?? []} />
              }
            />
          </CardContent>
        </Card>

        {/* Secondaire */}
        <Card>
          <CardHeader>
            <CardTitle>Informations</CardTitle>
          </CardHeader>
          <CardContent className="space-y-4">
            <FormField
              label="Difficulté"
              name="level"
              defaultValue={item.level}
              render={<LevelSelector />}
            />
            <FormField label="Démo" name="demo" defaultValue={item.demo} />
            <div className="flex items-end gap-2">
              <FormField
                label="Déprécié par"
                name="deprecatedBy"
                defaultValue={item.deprecatedBy ?? ""}
              />
              <div className="pb-1">
                <Switch
                  name="forceRedirect"
                  defaultChecked={item.forceRedirect}
                />
              </div>
            </div>
            <FormField
              label="ID Youtube"
              name="youtubeId"
              defaultValue={item.youtubeId}
            />
          </CardContent>
        </Card>
      </aside>
    </Form>
  )
}

export default withLayout<Props>(
  ({ item }) => {
    return <ItemForm item={item} />
  },
  {
    breadcrumb: (props) => [
      { label: "Tutoriels", href: route.index() },
      { label: props.item.title || "Nouveau" },
    ],
    top: (
      <Button form="form" type="submit">
        <SaveIcon /> Enregistrer
      </Button>
    ),
  },
)
