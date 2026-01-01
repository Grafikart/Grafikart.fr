import type { CourseListItemData, PaginatedData } from "@/types";
import { withLayout } from "@/components/layout.tsx";
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table.tsx";
import { Button, ButtonLink } from "@/components/ui/button.tsx";
import { CheckCircle2Icon, CircleXIcon, CopyIcon, EditIcon, TrashIcon } from "lucide-react";
import { ButtonGroup } from "@/components/ui/button-group.tsx";
import { adminPath } from "@/lib/url.ts";
import { Pagination } from "@/components/ui/pagination.tsx";
import { formatDate } from "@/lib/date.ts";

type Props = {
  pagination: PaginatedData<CourseListItemData>;
};

const breadcrumb = [{ label: "Tutoriel", href: adminPath("courses") }];

export default withLayout<Props>(
  (props) => {
    return (
      <div className="space-y-4">
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead className="w-10">ID</TableHead>
              <TableHead>Nom</TableHead>
              <TableHead>Publication</TableHead>
              <TableHead>Technologies</TableHead>
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
    breadcrumb: breadcrumb,
  },
);

function Item({ item }: { item: CourseListItemData }) {
  return (
    <TableRow className="group">
      <TableCell className="text-muted-foreground">{item.id}</TableCell>
      <TableCell>{item.title}</TableCell>
      <TableCell>{formatDate(item.createdAt)}</TableCell>
      <TableCell>
        {item.technologies.map((tech, k) => (
          <>
            <span key={tech.name}>{tech.name}</span>
            {k > 0 && ","}
          </>
        ))}
      </TableCell>
      <TableCell>
        {item.isOnline ? (
          <CheckCircle2Icon className="text-card fill-success size-4" />
        ) : (
          <CircleXIcon className="text-card fill-ring" />
        )}
      </TableCell>
      <TableCell className="text-right">
        <div className="flex justify-end">
          <ButtonGroup className="opacity-0 group-hover:opacity-100">
            <Button variant="destructive">
              <TrashIcon />
            </Button>
            <ButtonLink href={adminPath(`/courses/${item.id}`)} variant="secondary">
              <CopyIcon />
            </ButtonLink>
          </ButtonGroup>
          <ButtonLink href={adminPath(`/courses/${item.id}`)} variant="secondary">
            <EditIcon />
          </ButtonLink>
        </div>
      </TableCell>
    </TableRow>
  );
}
