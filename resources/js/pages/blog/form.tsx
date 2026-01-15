import type { PostFormData } from "@/types";
import { withLayout } from "@/components/layout.tsx";
import { adminPath } from "@/lib/url.ts";
import "@mdxeditor/editor/style.css";
import { Button } from "@/components/ui/button.tsx";
import { Form } from "@/components/form.tsx";
import { FormField } from "@/components/form-field.tsx";
import { MDEditor } from "@/components/ui/form/mdeditor.tsx";
import { Card, CardContent } from "@/components/ui/card.tsx";
import { SaveIcon } from "lucide-react";
import { SlugInput } from "@/components/ui/form/slug-input.tsx";
import { DatetimePicker } from "@/components/ui/form/datetime-picker.tsx";
import { Switch } from "@/components/ui/switch.tsx";
import { Label } from "@/components/ui/label.tsx";
import { AttachmentSelector } from "@/components/ui/form/attachment-selector.tsx";
import { PageTitle } from "@/components/page-title.tsx";
import { NativeSelect, NativeSelectOption } from "@/components/ui/native-select.tsx";

type Props = {
  item: PostFormData;
};

function ItemForm({ item }: Props) {
  const url = item.id ? `/blog/${item.slug}` : undefined;
  return (
    <Form className="grid grid-cols-[1fr_300px] gap-6" id="form" method="post">
      <PageTitle>{item.title || "Nouvel article"}</PageTitle>
      <main>
        <input
          name="title"
          defaultValue={item.title}
          className="text-2xl font-semibold outline-none block mb-1"
          placeholder="Titre"
        />
        <SlugInput defaultValue={item.slug} prefix="grafikart.fr/blog/" url={url} />
        <MDEditor defaultValue={item.content} name="content" />
      </main>
      <aside className="space-y-6">
        <div className="flex justify-end gap-4">
          <div className="flex items-center space-x-2">
            <Switch id="online" name="online" defaultChecked={item.online} />
            <Label htmlFor="online">En ligne</Label>
          </div>
        </div>

        <Card className="pt-0 overflow-hidden">
          <AttachmentSelector
            name="image"
            className="aspect-video"
            defaultValue={item.image?.id}
            preview={item.image?.url}
          />
          <CardContent className="space-y-4">
            <FormField label="Publié le" name="createdAt" defaultValue={item.createdAt} render={<DatetimePicker />} />
            <FormField label="Catégorie" name="category">
              <NativeSelect name="category" defaultValue={item.category?.toString() ?? ""} className="w-full">
                <NativeSelectOption value="">Aucune</NativeSelectOption>
                {item.categories.map((cat) => (
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
  );
}

export default withLayout<Props>(
  ({ item }) => {
    return <ItemForm item={item} />;
  },
  {
    breadcrumb: (props) => [{ label: "Articles", href: adminPath("/posts") }, { label: props.item.title || "Nouveau" }],
    top: (
      <Button form="form" type="submit">
        <SaveIcon /> Enregistrer
      </Button>
    ),
  },
);
