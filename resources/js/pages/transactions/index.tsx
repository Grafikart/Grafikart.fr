import { HandCoinsIcon, RotateCcwIcon } from 'lucide-react';

import route from '@/actions/App/Http/Cms/TransactionController.ts';
import { withLayout } from '@/components/layout.tsx';
import { PageTitle } from '@/components/page-title.tsx';
import { Badge } from '@/components/ui/badge.tsx';
import { ButtonLink } from '@/components/ui/button.tsx';
import { Pagination } from '@/components/ui/pagination.tsx';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table.tsx';
import { formatDate } from '@/lib/date.ts';
import type { PaginatedData, TransactionRowData } from '@/types';

type Props = {
    pagination: PaginatedData<TransactionRowData>;
};

export default withLayout<Props>(
    (props) => {
        return (
            <div className="space-y-4">
                <PageTitle>Transactions</PageTitle>
                <h1 className="flex items-center gap-2 text-xl font-semibold">
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
                            <TableHead>Date</TableHead>
                            <TableHead className="text-end">Actions</TableHead>
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
        );
    },
    {
        breadcrumb: [{ label: 'Transactions', href: route.index() }],
    },
);

function Item({ item }: { item: TransactionRowData }) {
    return (
        <TableRow className="group">
            <TableCell className="text-muted-foreground">{item.id}</TableCell>
            <TableCell>
                <div className="flex flex-col">
                    <span>{item.username}</span>
                    <span className="text-muted-foreground text-xs">
                        {item.email}
                    </span>
                </div>
            </TableCell>
            <TableCell>{(item.price / 100).toFixed(2)} €</TableCell>
            <TableCell>{item.duration} mois</TableCell>
            <TableCell>
                <Badge
                    variant={item.method === 'stripe' ? 'default' : 'secondary'}
                >
                    {item.method}
                </Badge>
            </TableCell>
            <TableCell>{formatDate(item.createdAt)}</TableCell>
            <TableCell className="text-right">
                <ButtonLink
                    href={route.destroy(item.id)}
                    disabled={item.refunded}
                    variant={item.refunded ? 'secondary' : 'destructive'}
                    confirm="Voulez-vous vraiment rembourser cette transaction ?"
                >
                    <RotateCcwIcon />
                    {item.refunded ? 'Remboursé' : 'Rembourser'}
                </ButtonLink>
            </TableCell>
        </TableRow>
    );
}
