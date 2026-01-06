import type { CourseFormData } from "@/types";
import { withLayout } from "@/components/layout.tsx";
import { adminPath } from "@/lib/url.ts";
import "@mdxeditor/editor/style.css";
import { Button } from "@/components/ui/button.tsx";
import { Form } from "@/components/form.tsx";
import { FormField } from "@/components/form-field.tsx";
import { MDEditor } from "@/components/ui/form/mdeditor.tsx";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card.tsx";
import { CircleCheckIcon, LinkIcon, SaveIcon, UploadIcon } from "lucide-react";
import { Input } from "@/components/ui/input.tsx";
import { TechnologySelector } from "@/components/ui/form/technology-selector.tsx";
import { LevelSelector } from "@/components/ui/form/level-selector.tsx";
import { DatetimePicker } from "@/components/ui/form/datetime-picker.tsx";
import { Switch } from "@/components/ui/switch.tsx";
import { Label } from "@/components/ui/label.tsx";
import { AttachmentSelector } from "@/components/ui/form/attachment-selector.tsx";
import { PageTitle } from "@/components/page-title.tsx";

type Props = {
  item: CourseFormData;
};

function ItemForm({ item }: Props) {
  const url = `/tutoriels/${item.slug}-${item.id}`;
  return (
    <Form className="grid grid-cols-[1fr_300px] gap-4" id="form" method="post">
      <PageTitle>{item.title || "Nouveau tutoriel"}</PageTitle>
      <main>
        <input
          name="title"
          defaultValue={item.title}
          className="text-2xl font-semibold outline-none block mb-1"
          placeholder="Titre"
        />
        <div className="flex text-sm text-muted-foreground mb-3 items-center">
          <span className="opacity-50">grafikart.fr/tutoriels/</span>
          <input type="text" name="slug" defaultValue={item.slug} className="outline-none field-sizing-content" />
          <Button nativeButton={false} variant="ghost" render={<a target="_blank" href={url} />} size="icon-xs">
            <LinkIcon />
          </Button>
        </div>
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
        <Card className="pt-0 overflow-hidden">
          <div className="grid grid-cols-2 gap-1">
            <AttachmentSelector
              name="image"
              className="aspect-8/9"
              defaultValue={item.image?.id}
              preview={item.image?.url}
            />
            <AttachmentSelector
              name="youtubeThumbnail"
              className="aspect-8/9"
              defaultValue={item.youtubeThumbnail?.id}
              preview={item.youtubeThumbnail?.url}
            />
          </div>
          <CardContent className="space-y-4">
            <FormField label="Publié le" name="createdAt" defaultValue={item.createdAt} render={<DatetimePicker />} />
            <FormField label="Vidéo" name="videoPath">
              <div className="flex items-center gap-2">
                <Input name="videoPath" defaultValue={item.videoPath} id="videoPath" />
                <Button variant="secondary" size="icon">
                  <UploadIcon />
                </Button>
              </div>
            </FormField>
            <FormField
              label="Source"
              right={item.source && <CircleCheckIcon className="text-primary size-4" />}
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
              render={<TechnologySelector defaultValue={item.technologies} />}
            />
          </CardContent>
        </Card>

        {/* Secondaire */}
        <Card>
          <CardHeader>
            <CardTitle>Informations</CardTitle>
          </CardHeader>
          <CardContent className="space-y-4">
            <FormField label="Difficulté" name="level" defaultValue={item.level} render={<LevelSelector />} />
            <FormField label="Démo" name="demo" defaultValue={item.demo} />
            <div className="flex items-end gap-2">
              <FormField label="Déprécié par" name="deprecatedBy" defaultValue={item.deprecatedBy ?? ""} />
              <div className="pb-1">
                <Switch name="forceRedirect" defaultChecked={item.forceRedirect} />
              </div>
            </div>
            <FormField label="ID Youtube" name="youtubeId" defaultValue={item.youtubeId} />
          </CardContent>
        </Card>
      </aside>
    </Form>
  );
}

export default withLayout<Props>(
  ({ item }) => {
    return <ItemForm item={item} />;
  },
  {
    breadcrumb: (props) => [{ label: "Tutoriel", href: adminPath("/courses") }, { label: props.item.title }],
    top: (
      <Button form="form" type="submit">
        <SaveIcon /> Enregistrer
      </Button>
    ),
  },
);
