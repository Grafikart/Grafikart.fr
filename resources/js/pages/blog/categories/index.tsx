import path from '@/actions/App/Http/Cms/BlogCategoryController.ts';
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
import type { BlogCategoryData, PaginatedData } from '@/types';
import { Link } from '@inertiajs/react';
import { EditIcon, PlusCircleIcon, TagsIcon, TrashIcon } from 'lucide-react';

type Props = {
    pagination: PaginatedData<BlogCategoryData>;
};

export default withLayout<Props>(
    (props) => {
        return (
            <div className="space-y-4">
                <PageTitle>Technologies</PageTitle>
                <h1 className="flex items-center gap-2 text-xl font-semibold">
                    <TagsIcon className="text-primary" />
                    Catégorie
                </h1>
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead className="w-10">ID</TableHead>
                            <TableHead>Nom</TableHead>
                            <TableHead className="w-32">slug</TableHead>
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
        breadcrumb: [{ label: 'Catégories', href: path.index() }],
        top: (
            <ButtonLink href={path.create()}>
                <PlusCircleIcon />
                Ajouter une catégorie
            </ButtonLink>
        ),
    },
);

function Item({ item }: { item: BlogCategoryData }) {
    if (!item.id) {
        return null;
    }
    return (
        <TableRow className="group">
            <TableCell className="text-muted-foreground">{item.id}</TableCell>
            <TableCell>
                <Link href={path.edit(item.id)}>{item.name}</Link>
            </TableCell>
            <TableCell className="text-muted-foreground">{item.slug}</TableCell>
            <TableCell className="text-right">
                <div className="flex justify-end">
                    <ButtonGroup className="opacity-0 group-hover:opacity-100">
                        <ButtonLink
                            variant="destructive"
                            confirm="Voulez vous vraiment supprimer cette catégorie ?"
                            href={path.destroy(item.id)}
                        >
                            <TrashIcon />
                        </ButtonLink>
                    </ButtonGroup>
                    <ButtonLink href={path.edit(item.id)} variant="secondary">
                        <EditIcon />
                    </ButtonLink>
                </div>
            </TableCell>
        </TableRow>
    );
}
