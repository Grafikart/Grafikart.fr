import route from '@/actions/App/Http/Cms/CourseController';
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
import { formatDate } from '@/lib/date.ts';
import type { CourseRowData, PaginatedData } from '@/types';
import { Link } from '@inertiajs/react';
import {
    CheckCircle2Icon,
    CircleXIcon,
    CopyIcon,
    EditIcon,
    MonitorPlayIcon,
    PlusCircleIcon,
    TrashIcon,
} from 'lucide-react';

type Props = {
    pagination: PaginatedData<CourseRowData>;
};

export default withLayout<Props>(
    (props) => {
        return (
            <div className="space-y-4">
                <PageTitle>Tutoriels</PageTitle>
                <h1 className="flex items-center gap-2 text-xl font-semibold">
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
        breadcrumb: [{ label: 'Tutoriels', href: route.index() }],
        top: (
            <ButtonLink href={route.create()}>
                <PlusCircleIcon />
                Créer un cours
            </ButtonLink>
        ),
    },
);

function Item({ item }: { item: CourseRowData }) {
    const href = route.edit(item.id);
    return (
        <TableRow className="group">
            <TableCell className="text-muted-foreground">{item.id}</TableCell>
            <TableCell>
                <Link href={href}>{item.title}</Link>
            </TableCell>
            <TableCell>{formatDate(item.createdAt)}</TableCell>
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
                    <CheckCircle2Icon className="size-4 fill-success text-card" />
                ) : (
                    <CircleXIcon className="size-4 fill-ring text-card" />
                )}
            </TableCell>
            <TableCell className="text-right">
                <div className="flex justify-end">
                    <ButtonGroup className="opacity-0 group-hover:opacity-100">
                        {item.online && (
                            <ButtonLink variant="destructive" href={href}>
                                <TrashIcon />
                            </ButtonLink>
                        )}
                        <ButtonLink
                            href={route.create({ query: { clone: item.id } })}
                            variant="secondary"
                        >
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
