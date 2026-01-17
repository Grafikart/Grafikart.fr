import route from '@/actions/App/Http/Cms/PostController';
import { withLayout } from '@/components/layout.tsx';
import { PageTitle } from '@/components/page-title.tsx';
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
import type { PaginatedData, PostRowData } from '@/types';
import { Link } from '@inertiajs/react';
import {
    CheckCircle2Icon,
    CircleXIcon,
    EditIcon,
    NewspaperIcon,
    PlusCircleIcon,
    TrashIcon,
} from 'lucide-react';

type Props = {
    pagination: PaginatedData<PostRowData>;
};

export default withLayout<Props>(
    (props) => {
        return (
            <div className="space-y-4">
                <PageTitle>Articles</PageTitle>
                <h1 className="flex items-center gap-2 text-xl font-semibold">
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
        breadcrumb: [{ label: 'Articles', href: route.index() }],
        top: (
            <ButtonLink href={route.create()}>
                <PlusCircleIcon />
                Créer un article
            </ButtonLink>
        ),
    },
);

function Item({ item }: { item: PostRowData }) {
    const href = route.edit(item.id);
    return (
        <TableRow className="group">
            <TableCell className="text-muted-foreground">{item.id}</TableCell>
            <TableCell>
                <Link href={href}>{item.title}</Link>
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
                            <ButtonLink
                                variant="destructive"
                                method="delete"
                                href={href}
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
