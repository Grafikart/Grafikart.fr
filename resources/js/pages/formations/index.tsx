import { Link } from '@inertiajs/react';
import {
    CheckCircle2Icon,
    CircleXIcon,
    EditIcon,
    GraduationCapIcon,
    PlusCircleIcon,
    TrashIcon,
} from 'lucide-react';

import route from '@/actions/App/Http/Cms/FormationController';
import { withLayout } from '@/components/layout.tsx';
import { PageTitle } from '@/components/page-title.tsx';
import { Badge } from '@/components/ui/badge.tsx';
import { ButtonGroup } from '@/components/ui/button-group.tsx';
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
import type { FormationRowData, PaginatedData } from '@/types';

type Props = {
    pagination: PaginatedData<FormationRowData>;
};

export default withLayout<Props>(
    (props) => {
        return (
            <div className="space-y-4">
                <PageTitle>Formations</PageTitle>
                <h1 className="flex items-center gap-2 text-xl font-semibold">
                    <GraduationCapIcon className="text-primary" />
                    Formations
                </h1>
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead className="w-10">ID</TableHead>
                            <TableHead>Titre</TableHead>
                            <TableHead>Technologies</TableHead>
                            <TableHead className="w-10">Status</TableHead>
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
        breadcrumb: [{ label: 'Formations', href: route.index() }],
        top: (
            <ButtonLink href={route.create()}>
                <PlusCircleIcon />
                Créer une formation
            </ButtonLink>
        ),
    },
);

function Item({ item }: { item: FormationRowData }) {
    const href = route.edit(item.id);
    return (
        <TableRow className="group">
            <TableCell className="text-muted-foreground">{item.id}</TableCell>
            <TableCell>
                <Link href={href}>{item.title}</Link>
            </TableCell>
            <TableCell>
                <div className="flex flex-wrap gap-1">
                    {item.technologies.map((tech, k) => (
                        <Badge key={k} variant="secondary">
                            {tech.name}
                        </Badge>
                    ))}
                </div>
            </TableCell>
            <TableCell>
                {item.online ? (
                    <CheckCircle2Icon className="fill-success text-card size-4" />
                ) : (
                    <CircleXIcon className="fill-ring text-card size-4" />
                )}
            </TableCell>
            <TableCell className="text-right">
                <div className="flex justify-end">
                    <ButtonGroup className="opacity-0 group-hover:opacity-100">
                        {!item.online && (
                            <ButtonLink
                                variant="destructive"
                                href={route.destroy(item.id)}
                            >
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
