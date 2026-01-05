import type { PlanItemData, PaginatedData } from "@/types";
import { withLayout } from "@/components/layout.tsx";
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table.tsx";
import { adminPath } from "@/lib/url.ts";
import { Pagination } from "@/components/ui/pagination.tsx";

type Props = {
  pagination: PaginatedData<PlanItemData>;
};

export default withLayout<Props>(
  (props) => {
    return (
      <div className="space-y-4">
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead className="w-10">ID</TableHead>
              <TableHead>Nom de la formule</TableHead>
              <TableHead>Prix</TableHead>
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
    breadcrumb: [{ label: "Plans", href: adminPath("/plans") }],
  },
);

function Item({ item }: { item: PlanItemData }) {
  return (
    <TableRow className="group">
      <TableCell className="text-muted-foreground">{item.id}</TableCell>
      <TableCell>{item.name}</TableCell>
      <TableCell>{item.price} €</TableCell>
    </TableRow>
  );
}
