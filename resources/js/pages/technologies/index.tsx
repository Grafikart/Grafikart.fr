import CourseController from '@/actions/App/Http/Cms/CourseController.ts';
import route from '@/actions/App/Http/Cms/TechnologyController';
import { withLayout } from '@/components/layout.tsx';
import { PageTitle } from '@/components/page-title.tsx';
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
import type { PaginatedData, TechnologyRowData } from '@/types';
import { Link } from '@inertiajs/react';
import { EditIcon, PlusCircleIcon, WrenchIcon } from 'lucide-react';

type Props = {
    pagination: PaginatedData<TechnologyRowData>;
};

export default withLayout<Props>(
    (props) => {
        return (
            <div className="space-y-4">
                <PageTitle>Technologies</PageTitle>
                <h1 className="flex items-center gap-2 text-xl font-semibold">
                    <WrenchIcon className="text-primary" />
                    Technologies
                </h1>
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead className="w-10">ID</TableHead>
                            <TableHead>Nom</TableHead>
                            <TableHead className="w-32">Tutoriels</TableHead>
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
        breadcrumb: [{ label: 'Technologies', href: route.index() }],
        top: (
            <ButtonLink href={route.create.url()}>
                <PlusCircleIcon />
                Créer une technologie
            </ButtonLink>
        ),
    },
);

function Item({ item }: { item: TechnologyRowData }) {
    const href = route.edit(item.id);
    return (
        <TableRow className="group">
            <TableCell className="text-muted-foreground">{item.id}</TableCell>
            <TableCell>
                <Link href={href} className="flex items-center gap-2">
                    {item.image && (
                        <img
                            src={item.image}
                            alt={item.name}
                            className="size-6 rounded-sm object-contain"
                        />
                    )}
                    {item.name}
                </Link>
            </TableCell>
            <TableCell>
                <Link
                    href={CourseController.index({
                        query: { technology: item.id },
                    })}
                >
                    {item.count}
                </Link>
            </TableCell>
            <TableCell className="text-right">
                <ButtonLink href={href} variant="secondary">
                    <EditIcon />
                </ButtonLink>
            </TableCell>
        </TableRow>
    );
}
