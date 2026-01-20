import {
    CheckCircle2Icon,
    CircleXIcon,
    HandCoinsIcon,
    UserLockIcon,
    UserPlusIcon,
    UserSearchIcon,
    UsersIcon,
    XCircleIcon,
} from 'lucide-react';

import route from '@/actions/App/Http/Cms/TransactionController.ts';
import UserController from '@/actions/App/Http/Cms/UserController';
import { withLayout } from '@/components/layout.tsx';
import { PageTitle } from '@/components/page-title.tsx';
import { Button, ButtonLink } from '@/components/ui/button.tsx';
import { Card, CardContent } from '@/components/ui/card';
import { Pagination } from '@/components/ui/pagination.tsx';
import { SimpleChart } from '@/components/ui/simple-chart.tsx';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table.tsx';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { formatDate } from '@/lib/date.ts';
import type {
    DailyData,
    MonthlyData,
    PaginatedData,
    UserRowData,
} from '@/types';

type Props = {
    pagination: PaginatedData<UserRowData>;
    months?: MonthlyData[];
    days?: DailyData[];
    banned_filter: boolean;
};

export default withLayout<Props>(
    (props) => {
        return (
            <div className="space-y-6">
                <PageTitle>Utilisateurs</PageTitle>
                {props.months && props.days && (
                    <Chart months={props.months} days={props.days} />
                )}
                <div className="space-y-4">
                    <div className="flex items-center justify-between">
                        <h1 className="flex items-center gap-2 text-xl font-semibold">
                            <UsersIcon className="text-primary" />
                            Utilisateurs {props.banned_filter ? 'bannis' : ''}
                        </h1>
                        {!props.banned_filter && (
                            <ButtonLink
                                href={UserController.index({
                                    query: { banned: '1' },
                                })}
                                variant="secondary"
                            >
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
                                <TableHead className="text-end">
                                    Actions
                                </TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {props.pagination.data.map((item) => (
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
        breadcrumb: [{ label: 'Utilisateurs', href: UserController.index() }],
    },
);

function Item({ item }: { item: UserRowData }) {
    return (
        <TableRow className="group">
            <TableCell className="text-muted-foreground">{item.id}</TableCell>
            <TableCell>{item.username}</TableCell>
            <TableCell>{item.email}</TableCell>
            <TableCell>{formatDate(item.createdAt)}</TableCell>
            <TableCell>
                {item.isPremium ? (
                    <CheckCircle2Icon className="size-4 fill-success text-background" />
                ) : (
                    <CircleXIcon className="size-4 fill-ring text-background" />
                )}
            </TableCell>
            <TableCell>{item.lastLoginIp}</TableCell>
            <TableCell>
                <div className="flex items-center justify-end">
                    <Button
                        variant="secondary"
                        nativeButton={false}
                        render={<a href={`/?_ninja=${item.email}`} />}
                    >
                        <UserSearchIcon />
                    </Button>
                    <ButtonLink
                        variant="secondary"
                        nativeButton={false}
                        href={route.index({
                            query: { q: `user:${item.id}` },
                        })}
                    >
                        <HandCoinsIcon />
                        Transactions
                    </ButtonLink>
                    <ButtonLink
                        disabled={item.isBanned || item.isPremium}
                        variant="destructive"
                        href={UserController.destroy(item.id)}
                        confirm="Voulez vous vraiment supprimer cet utilisateur ?"
                    >
                        <XCircleIcon />
                        {item.isBanned ? 'Banni !' : 'Bannir'}
                    </ButtonLink>
                </div>
            </TableCell>
        </TableRow>
    );
}

function Chart({ days, months }: Pick<Required<Props>, 'days' | 'months'>) {
    return (
        <Tabs>
            <div className="flex items-center justify-between">
                <h1 className="mb-4 flex items-center gap-2 text-xl font-semibold">
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
                                return new Date(v.date).toLocaleDateString(
                                    'fr-FR',
                                    {
                                        month: 'short',
                                        day: 'numeric',
                                    },
                                );
                            }}
                        />
                    </TabsContent>
                    <TabsContent value="year">
                        <SimpleChart
                            data={months}
                            formatter={(v) => {
                                return new Date(
                                    v.year,
                                    v.month - 1,
                                    10,
                                ).toLocaleDateString('fr-FR', {
                                    month: 'long',
                                });
                            }}
                        />
                    </TabsContent>
                </CardContent>
            </Card>
        </Tabs>
    );
}
