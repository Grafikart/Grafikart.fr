import type { PaginatedData, PlanItemData } from "@/types";
import { withLayout } from "@/components/layout.tsx";
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table.tsx";
import { adminPath } from "@/lib/url.ts";
import { Pagination } from "@/components/ui/pagination.tsx";
import { Button } from "@/components/ui/button.tsx";
import { CheckIcon, EditIcon, StarIcon, XIcon } from "lucide-react";
import { PageTitle } from "@/components/page-title.tsx";
import { useState } from "react";
import { Form } from "@/components/form.tsx";
import { FormField } from "@/components/form-field.tsx";

type Props = {
  pagination: PaginatedData<PlanItemData>;
};

export default withLayout<Props>(
  (props) => {
    return (
      <div className="space-y-4">
        <PageTitle>Plans</PageTitle>
        <h1 className="flex gap-2 items-center text-xl font-semibold">
          <StarIcon className="text-primary" />
          Formules
        </h1>
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead>Nom de la formule</TableHead>
              <TableHead className="w-25">Prix</TableHead>
              <TableHead className="w-25">Durée</TableHead>
              <TableHead className="text-right w-20">Actions</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            {props.pagination.items.map((item) => (
              <Item item={item} key={item.id} />
            ))}
          </TableBody>
        </Table>
        <Pagination pagination={props.pagination} />
      </div>
    );
  },
  {
    breadcrumb: [{ label: "Formules", href: adminPath("/plans") }],
  },
);

function Item({ item }: { item: PlanItemData }) {
  const [editing, setEditing] = useState(false);

  if (editing) {
    return (
      <TableRow>
        <TableCell colSpan={4}>
          <RowForm action={adminPath(`/plans/${item.id}`)} item={item} onCancel={() => setEditing(false)} />
        </TableCell>
      </TableRow>
    );
  }

  return (
    <TableRow className="group">
      <TableCell onClick={() => setEditing(true)}>{item.name}</TableCell>
      <TableCell>{item.price} €</TableCell>
      <TableCell>{item.duration} mois</TableCell>
      <TableCell className="text-right">
        <Button onClick={() => setEditing(true)} size="icon" variant="secondary">
          <EditIcon />
        </Button>
      </TableCell>
    </TableRow>
  );
}

function RowForm({ item, action, onCancel }: { item: PlanItemData; action: string; onCancel: () => void }) {
  return (
    <Form action={action} className="flex gap-2 items-end">
      <FormField defaultValue={item.name} name="name" placeholder="Nom" label="Nom" />
      <FormField defaultValue={item.price} name="price" placeholder="Prix" type="number" label="Prix" />
      <FormField defaultValue={item.duration} name="duration" placeholder="Durée" type="number" label="Durée" />
      <FormField defaultValue={item.stripeId ?? ""} name="stripeId" placeholder="ID Stripe" label="ID Stripe" />
      <div className="flex justify-end gap-1 pb-1">
        <Button size="icon" type="submit" variant="default">
          <CheckIcon />
        </Button>
        <Button onClick={onCancel} size="icon" type="button" variant="secondary">
          <XIcon />
        </Button>
      </div>
    </Form>
  );
}
