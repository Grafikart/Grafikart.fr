import { SaveIcon } from "lucide-react";
import route from "@/actions/App/Http/Cms/BadgeController";
import { Form } from "@/components/form.tsx";
import { FormField } from "@/components/form-field.tsx";
import { withLayout } from "@/components/layout.tsx";
import { PageTitle } from "@/components/page-title.tsx";
import { Button } from "@/components/ui/button.tsx";
import { Card, CardContent } from "@/components/ui/card.tsx";
import { Switch } from "@/components/ui/switch.tsx";
import type { BadgeFormData } from "@/types";

type Props = {
  item: BadgeFormData;
};

export default withLayout<Props>(
  ({ item }) => {
    const formAction = item.id
      ? route.update.form(item.id)
      : route.store.form();

    return (
      <Form
        className="grid lg:grid-cols-[1fr_300px] gap-4"
        id="form"
        {...formAction}
      >
        <PageTitle>{item.name || "Nouveau badge"}</PageTitle>
        <main className="space-y-4">
          <input
            name="name"
            defaultValue={item.name}
            className="block w-full font-semibold text-2xl outline-none"
            placeholder="Nom du badge"
          />
          <textarea
            name="description"
            defaultValue={item.description}
            className="min-h-40 w-full outline-none"
            placeholder="Description"
          />
          <div className="grid grid-cols-2 gap-4">
            <FormField
              label="Action"
              name="action"
              defaultValue={item.action}
            />
            <FormField
              label="Objectif"
              name="actionCount"
              type="number"
              defaultValue={item.actionCount}
            />
            <FormField
              label="Position"
              name="position"
              type="number"
              defaultValue={item.position}
            />
            <FormField label="Thème" name="theme" defaultValue={item.theme} />
          </div>
        </main>
        <aside className="space-y-4">
          <Card>
            <CardContent className="space-y-4">
              <FormField
                label="Image"
                name="image"
                defaultValue={item.image ?? ""}
              />
              <div className="flex items-center gap-2">
                <Switch
                  id="unlockable"
                  name="unlockable"
                  defaultChecked={item.unlockable}
                />
                <label htmlFor="unlockable">Déblocable</label>
              </div>
            </CardContent>
          </Card>
        </aside>
      </Form>
    );
  },
  {
    breadcrumb: (props) => [
      { label: "Badges", href: route.index() },
      { label: props.item.name || "Nouveau badge" },
    ],
    top: (
      <Button form="form" type="submit">
        <SaveIcon /> Enregistrer
      </Button>
    ),
  },
);
