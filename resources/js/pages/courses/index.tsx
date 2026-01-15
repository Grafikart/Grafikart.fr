import type { CourseListItemData, PaginatedData } from "@/types";
import { withLayout } from "@/components/layout.tsx";
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table.tsx";
import { ButtonLink } from "@/components/ui/button.tsx";
import {
  CheckCircle2Icon,
  CircleXIcon,
  CopyIcon,
  EditIcon,
  MonitorPlayIcon,
  PlusCircleIcon,
  TrashIcon,
} from "lucide-react";
import { ButtonGroup } from "@/components/ui/button-group.tsx";
import { adminPath } from "@/lib/url.ts";
import { Pagination } from "@/components/ui/pagination.tsx";
import { formatDate } from "@/lib/date.ts";
import { Fragment } from "react";
import { PageTitle } from "@/components/page-title.tsx";
import { Link } from "@inertiajs/react";

type Props = {
  pagination: PaginatedData<CourseListItemData>;
};

export default withLayout<Props>(
  (props) => {
    return (
      <div className="space-y-4">
        <PageTitle>Tutoriels</PageTitle>
        <h1 className="flex gap-2 items-center text-xl font-semibold">
          <MonitorPlayIcon className="text-primary" />
          Tutoriels
        </h1>
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
    breadcrumb: [{ label: "Tutoriel", href: adminPath("courses") }],
    top: (
      <ButtonLink href={adminPath("/courses/new")}>
        <PlusCircleIcon />
        Créer un cours
      </ButtonLink>
    ),
  },
);

function Item({ item }: { item: CourseListItemData }) {
  const href = adminPath(`/courses/${item.id}`);
  return (
    <TableRow className="group">
      <TableCell className="text-muted-foreground">{item.id}</TableCell>
      <TableCell>
        <Link href={href}>{item.title}</Link>
      </TableCell>
      <TableCell>{formatDate(item.createdAt)}</TableCell>
      <TableCell>
        {item.technologies.map((tech, k) => (
          <Fragment key={k}>
            <span key={tech.name}>{tech.name}</span>
            {k > 0 && ","}
          </Fragment>
        ))}
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
            <ButtonLink href={adminPath(`/courses/new?clone=${item.id}`)} variant="secondary">
              <CopyIcon />
            </ButtonLink>
          </ButtonGroup>
          <ButtonLink href={href} variant="secondary">
            <EditIcon />
          </ButtonLink>
        </div>
      </TableCell>
    </TableRow>
  );
}
