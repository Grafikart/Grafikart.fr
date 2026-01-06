import type { TransactionItemData, PaginatedData } from "@/types";
import { withLayout } from "@/components/layout.tsx";
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table.tsx";
import { adminPath } from "@/lib/url.ts";
import { Pagination } from "@/components/ui/pagination.tsx";
import { formatDate } from "@/lib/date.ts";
import { PageTitle } from "@/components/page-title.tsx";
import { CheckCircle2Icon, CircleXIcon, HandCoinsIcon, RotateCcwIcon, UsersIcon } from "lucide-react";
import { Badge } from "@/components/ui/badge.tsx";
import { ButtonLink } from "@/components/ui/button.tsx";

type Props = {
  pagination: PaginatedData<TransactionItemData>;
};

export default withLayout<Props>(
  (props) => {
    return (
      <div className="space-y-4">
        <PageTitle>Transactions</PageTitle>
        <h1 className="flex gap-2 items-center text-xl font-semibold">
          <HandCoinsIcon className="text-primary" />
          Transactions
        </h1>
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead className="w-10">ID</TableHead>
              <TableHead>Utilisateur</TableHead>
              <TableHead>Prix</TableHead>
              <TableHead>Durée</TableHead>
              <TableHead>Méthode</TableHead>
              <TableHead>Remboursé</TableHead>
              <TableHead>Date</TableHead>
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
    breadcrumb: [{ label: "Transactions", href: adminPath("transactions") }],
  },
);

function Item({ item }: { item: TransactionItemData }) {
  return (
    <TableRow className="group">
      <TableCell className="text-muted-foreground">{item.id}</TableCell>
      <TableCell>
        <div className="flex flex-col">
          <span>{item.username}</span>
          <span className="text-muted-foreground text-xs">{item.email}</span>
        </div>
      </TableCell>
      <TableCell>{item.price.toFixed(2)} €</TableCell>
      <TableCell>{item.duration} mois</TableCell>
      <TableCell>
        <Badge variant={item.method === "stripe" ? "default" : "secondary"}>{item.method}</Badge>
      </TableCell>
      <TableCell>
        {item.refunded ? (
          <CheckCircle2Icon className="text-background fill-success size-4" />
        ) : (
          <CircleXIcon className="text-background fill-ring size-4" />
        )}
      </TableCell>
      <TableCell>{formatDate(item.createdAt)}</TableCell>
      <TableCell className="text-right">
        <ButtonLink
          href={adminPath(`/transactions/${item.id}`)}
          disabled={item.refunded}
          method="delete"
          variant="destructive"
          confirm="Voulez-vous vraiment rembourser cette transaction ?"
        >
          <RotateCcwIcon />
          {item.refunded ? "Remboursé" : "Rembourser"}
        </ButtonLink>
      </TableCell>
    </TableRow>
  );
}
