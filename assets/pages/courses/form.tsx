import type { CourseFormData } from "@/types";
import { withLayout } from "@/components/layout.tsx";
import { adminPath } from "@/lib/url.ts";
import "@mdxeditor/editor/style.css";
import { Button } from "@/components/ui/button.tsx";
import { Form } from "@/components/form.tsx";
import { FormField } from "@/components/form-field.tsx";
import { MDEditor } from "@/components/ui/form/mdeditor.tsx";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card.tsx";
import { LinkIcon, SaveIcon, UploadIcon } from "lucide-react";
import { Input } from "@/components/ui/input.tsx";
import { TechnologySelector } from "@/components/ui/form/technology-selector.tsx";
import { LevelSelector } from "@/components/ui/form/level-selector.tsx";
import { ImageInput } from "@/components/ui/form/image-input.tsx";

type Props = {
  course: CourseFormData;
};

function ItemForm({ course }: Props) {
  return (
    <Form className="grid grid-cols-[1fr_300px] gap-4" id="form">
      <main>
        <input
          name="title"
          defaultValue={course.title}
          className="text-2xl font-semibold outline-none block mb-1"
          placeholder="Titre"
        />
        <div className="flex text-sm text-muted-foreground mb-3 items-center">
          <span className="opacity-50">grafikart.fr/tutoriels/</span>
          <input
            style={{ fieldSizing: "content" }}
            type="text"
            name="slug"
            defaultValue={course.slug}
            className="outline-none"
          />
          <Button nativeButton={false} variant="ghost" render={<a target="_blank" href={course.url} />} size="icon-xs">
            <LinkIcon />
          </Button>
        </div>
        <MDEditor defaultValue={course.content} name="content" />
      </main>
      <aside className="space-y-6">
        {/* Métadonnées */}
        <Card className="pt-0">
          <ImageInput defaultValue={course.image} className="aspect-video" name="image" />
          <CardHeader>
            <CardTitle>Informations</CardTitle>
          </CardHeader>
          <CardContent className="space-y-4">
            <FormField label="Difficulté" name="level" defaultValue={course.level} render={<LevelSelector />} />
            <FormField label="Vidéo" name="videoPath">
              <div className="flex items-center gap-2">
                <Input name="videoPath" defaultValue={course.videoPath} id="videoPath" />
                <Button variant="secondary" size="icon">
                  <UploadIcon />
                </Button>
              </div>
            </FormField>
            <div className="flex"></div>
          </CardContent>
        </Card>

        {/* Technologies */}
        <Card>
          <CardContent>
            <FormField
              label="Outils & Langages"
              name="usages"
              render={<TechnologySelector defaultValue={course.technologies} />}
            />
          </CardContent>
        </Card>
      </aside>
    </Form>
  );
}

export default withLayout<Props>(
  ({ course }) => {
    return <ItemForm course={course} />;
  },
  {
    breadcrumb: (props) => [{ label: "Tutoriel", href: adminPath("/courses") }, { label: props.course.title }],
    top: (
      <Button form="form" type="submit">
        <SaveIcon /> Enregistrer
      </Button>
    ),
  },
);
