import type { PostRowData, PaginatedData } from "@/types";
import { withLayout } from "@/components/layout.tsx";
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table.tsx";
import { ButtonLink } from "@/components/ui/button.tsx";
import { CheckCircle2Icon, CircleXIcon, EditIcon, PlusCircleIcon, TrashIcon, NewspaperIcon } from "lucide-react";
import { ButtonGroup } from "@/components/ui/button-group.tsx";
import { adminPath } from "@/lib/url.ts";
import { Pagination } from "@/components/ui/pagination.tsx";
import { PageTitle } from "@/components/page-title.tsx";
import { Link } from "@inertiajs/react";

type Props = {
  pagination: PaginatedData<PostRowData>;
};

export default withLayout<Props>(
  (props) => {
    return (
      <div className="space-y-4">
        <PageTitle>Articles</PageTitle>
        <h1 className="flex gap-2 items-center text-xl font-semibold">
          <NewspaperIcon className="text-primary" />
          Articles
        </h1>
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead className="w-10">ID</TableHead>
              <TableHead>Titre</TableHead>
              <TableHead className="w-10">Status</TableHead>
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
    breadcrumb: [{ label: "Articles", href: adminPath("posts") }],
    top: (
      <ButtonLink href={adminPath("/posts/new")}>
        <PlusCircleIcon />
        Créer un article
      </ButtonLink>
    ),
  },
);

function Item({ item }: { item: PostRowData }) {
  const href = adminPath(`/posts/${item.id}`);
  return (
    <TableRow className="group">
      <TableCell className="text-muted-foreground">{item.id}</TableCell>
      <TableCell>
        <Link href={href}>{item.title}</Link>
      </TableCell>
      <TableCell>
        {item.online ? (
          <CheckCircle2Icon className="text-card fill-success size-4" />
        ) : (
          <CircleXIcon className="text-card fill-ring size-4" />
        )}
      </TableCell>
      <TableCell className="text-right">
        <div className="flex justify-end">
          <ButtonGroup className="opacity-0 group-hover:opacity-100">
            {item.online && (
              <ButtonLink variant="destructive" method="delete" href={href}>
                <TrashIcon />
              </ButtonLink>
            )}
          </ButtonGroup>
          <ButtonLink href={href} variant="secondary">
            <EditIcon />
          </ButtonLink>
        </div>
      </TableCell>
    </TableRow>
  );
}
