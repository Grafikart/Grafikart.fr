import type { PaginatedData, TechnologyItemData } from "@/types";
import { withLayout } from "@/components/layout.tsx";
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table.tsx";
import { ButtonLink } from "@/components/ui/button.tsx";
import { EditIcon, WrenchIcon } from "lucide-react";
import { adminPath } from "@/lib/url.ts";
import { Pagination } from "@/components/ui/pagination.tsx";
import { PageTitle } from "@/components/page-title.tsx";
import { Link } from "@inertiajs/react";

type Props = {
  pagination: PaginatedData<TechnologyItemData>;
};

export default withLayout<Props>(
  (props) => {
    return (
      <div className="space-y-4">
        <PageTitle>Technologies</PageTitle>
        <h1 className="flex gap-2 items-center text-xl font-semibold">
          <WrenchIcon className="text-primary" />
          Technologies
        </h1>
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead className="w-10">ID</TableHead>
              <TableHead>Nom</TableHead>
              <TableHead className="w-32">Tutoriels</TableHead>
              <TableHead className="text-end">Actions</TableHead>
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
    breadcrumb: [{ label: "Technologies", href: adminPath("technologies") }],
  },
);

function Item({ item }: { item: TechnologyItemData }) {
  const href = adminPath(`/technologies/${item.id}`);
  return (
    <TableRow className="group">
      <TableCell className="text-muted-foreground">{item.id}</TableCell>
      <TableCell>
        <Link href={href} className="flex items-center gap-2">
          {item.image && (
            <img src={`/uploads/icons/${item.image}`} alt={item.name} className="size-6 object-contain rounded-sm" />
          )}
          {item.name}
        </Link>
      </TableCell>
      <TableCell>{item.tutorialCount}</TableCell>
      <TableCell className="text-right">
        <ButtonLink href={href} variant="secondary">
          <EditIcon />
        </ButtonLink>
      </TableCell>
    </TableRow>
  );
}
