import type { DailyData, MonthlyData, PaginatedData, UserItemData } from "@/types";
import { withLayout } from "@/components/layout.tsx";
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table.tsx";
import { adminPath } from "@/lib/url.ts";
import { Pagination } from "@/components/ui/pagination.tsx";
import { formatDate } from "@/lib/date.ts";
import { PageTitle } from "@/components/page-title.tsx";
import {
  CheckCircle2Icon,
  CircleXIcon,
  HandCoinsIcon,
  UserLockIcon,
  UserPlusIcon,
  UserSearchIcon,
  UsersIcon,
  XCircleIcon,
} from "lucide-react";
import { Button, ButtonLink } from "@/components/ui/button.tsx";
import { Card, CardContent } from "@/components/ui/card";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
import { SimpleChart } from "@/components/ui/simple-chart.tsx";

type Props = {
  pagination: PaginatedData<UserItemData>;
  months?: MonthlyData[];
  days?: DailyData[];
  banned_filter: boolean;
};

export default withLayout<Props>(
  (props) => {
    return (
      <div className="space-y-6">
        <PageTitle>Utilisateurs</PageTitle>
        {props.months && props.days && <Chart months={props.months} days={props.days} />}
        <div className="space-y-4">
          <div className="flex items-center justify-between">
            <h1 className="flex gap-2 items-center text-xl font-semibold">
              <UsersIcon className="text-primary" />
              Utilisateurs {props.banned_filter ? "bannis" : ""}
            </h1>
            {!props.banned_filter && (
              <ButtonLink href={adminPath("/users/?banned=1")} variant="secondary">
                <UserLockIcon />
                Bannis
              </ButtonLink>
            )}
          </div>
          <Table>
            <TableHeader>
              <TableRow>
                <TableHead className="w-10">ID</TableHead>
                <TableHead>Pseudo</TableHead>
                <TableHead>Email</TableHead>
                <TableHead>Inscription</TableHead>
                <TableHead>Premium</TableHead>
                <TableHead>IP</TableHead>
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
      </div>
    );
  },
  {
    breadcrumb: [{ label: "Utilisateurs", href: adminPath("/users") }],
  },
);

function Item({ item }: { item: UserItemData }) {
  return (
    <TableRow className="group">
      <TableCell className="text-muted-foreground">{item.id}</TableCell>
      <TableCell>{item.username}</TableCell>
      <TableCell>{item.email}</TableCell>
      <TableCell>{formatDate(item.createdAt)}</TableCell>
      <TableCell>
        {item.isPremium ? (
          <CheckCircle2Icon className="text-background fill-success size-4" />
        ) : (
          <CircleXIcon className="text-background fill-ring size-4" />
        )}
      </TableCell>
      <TableCell>{item.lastLoginIp}</TableCell>
      <TableCell>
        <div className="flex items-center justify-end">
          <Button variant="secondary" nativeButton={false} render={<a href={`/?_ninja=${item.email}`} />}>
            <UserSearchIcon />
          </Button>
          <ButtonLink variant="secondary" nativeButton={false} href={adminPath(`/transactions/?q=user:${item.id}`)}>
            <HandCoinsIcon />
            Transactions
          </ButtonLink>
          <ButtonLink
            href={adminPath(`/users/${item.id}/ban`)}
            disabled={item.isBanned || item.isPremium}
            method="delete"
            variant="destructive"
            confirm="Voulez vous vraiment supprimer cet utilisateur ?"
          >
            <XCircleIcon />
            {item.isBanned ? "Banni !" : "Bannir"}
          </ButtonLink>
        </div>
      </TableCell>
    </TableRow>
  );
}

function Chart({ days, months }: Pick<Required<Props>, "days" | "months">) {
  return (
    <Tabs>
      <div className="flex justify-between items-center">
        <h1 className="flex gap-2 items-center text-xl font-semibold mb-4">
          <UserPlusIcon className="text-primary" />
          Inscriptions
        </h1>
        <TabsList>
          <TabsTrigger value="month">30 derniers jours</TabsTrigger>
          <TabsTrigger value="year">24 derniers mois</TabsTrigger>
        </TabsList>
      </div>
      <Card>
        <CardContent className="py-0">
          <TabsContent value="month">
            <SimpleChart
              data={days}
              formatter={(v) => {
                return new Date(v.date).toLocaleDateString("fr-FR", {
                  month: "short",
                  day: "numeric",
                });
              }}
            />
          </TabsContent>
          <TabsContent value="year">
            <SimpleChart
              data={months}
              formatter={(v) => {
                return new Date(v.year, v.month - 1, 10).toLocaleDateString("fr-FR", {
                  month: "long",
                });
              }}
            />
          </TabsContent>
        </CardContent>
      </Card>
    </Tabs>
  );
}
