import { CheckIcon, EditIcon, PlusIcon, StarIcon, XIcon } from 'lucide-react';

import route from '@/actions/App/Http/Cms/PlanController.ts';
import { FormField } from '@/components/form-field.tsx';
import { Form } from '@/components/form.tsx';
import { withLayout } from '@/components/layout.tsx';
import { PageTitle } from '@/components/page-title.tsx';
import { Button } from '@/components/ui/button.tsx';
import { Pagination } from '@/components/ui/pagination.tsx';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table.tsx';
import { useToggle } from '@/hooks/use-toggle.ts';
import type { PaginatedData, PlanData } from '@/types';

type Props = {
    pagination: PaginatedData<PlanData>;
};

export default withLayout<Props>(
    (props) => {
        return (
            <div className="space-y-4">
                <PageTitle>Plans</PageTitle>
                <h1 className="flex items-center gap-2 text-xl font-semibold">
                    <StarIcon className="text-primary" />
                    Formules
                </h1>
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Nom de la formule</TableHead>
                            <TableHead className="w-25">Prix</TableHead>
                            <TableHead className="w-25">Durée</TableHead>
                            <TableHead className="w-20 text-right">
                                Actions
                            </TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        {props.pagination.data.map((item) => (
                            <Item item={item} key={item.id} />
                        ))}
                        <AddRow />
                    </TableBody>
                </Table>
                <Pagination pagination={props.pagination} />
            </div>
        );
    },
    {
        breadcrumb: [{ label: 'Formules', href: route.index() }],
    },
);

function Item({ item }: { item: PlanData }) {
    const [editing, toggle] = useToggle();

    if (editing) {
        return (
            <TableRow>
                <TableCell colSpan={4}>
                    <RowForm item={item} onCancel={toggle} />
                </TableCell>
            </TableRow>
        );
    }

    return (
        <TableRow className="group">
            <TableCell onClick={toggle}>{item.name}</TableCell>
            <TableCell>{item.price} €</TableCell>
            <TableCell>{item.duration} mois</TableCell>
            <TableCell className="text-right">
                <Button onClick={toggle} size="icon" variant="secondary">
                    <EditIcon />
                </Button>
            </TableCell>
        </TableRow>
    );
}

const emptyItem = {
    id: null,
    name: '',
    price: 5,
    duration: 1,
    stripeId: '',
} satisfies PlanData;

function AddRow() {
    const [creating, toggle] = useToggle();

    return (
        <TableRow>
            <TableCell colSpan={4}>
                {creating ? (
                    <RowForm item={emptyItem} onCancel={toggle} />
                ) : (
                    <Button
                        onClick={toggle}
                        variant="ghost"
                        className="text-muted-foreground w-full"
                    >
                        <PlusIcon />
                        Ajouter une formule
                    </Button>
                )}
            </TableCell>
        </TableRow>
    );
}

function RowForm({ item, onCancel }: { item: PlanData; onCancel: () => void }) {
    return (
        <Form
            {...(item.id ? route.update.form(item.id) : route.store.form())}
            className="flex items-end gap-2"
        >
            <FormField
                autoFocus
                defaultValue={item.name}
                name="name"
                placeholder="Nom"
                label="Nom"
            />
            <FormField
                defaultValue={item.price}
                name="price"
                placeholder="Prix"
                type="number"
                label="Prix"
            />
            <FormField
                defaultValue={item.duration}
                name="duration"
                placeholder="Durée"
                type="number"
                label="Durée"
            />
            <FormField
                defaultValue={item.stripeId ?? ''}
                name="stripeId"
                placeholder="ID Stripe"
                label="ID Stripe"
            />
            <div className="flex justify-end gap-1 pb-1">
                <Button size="icon" type="submit" variant="default">
                    <CheckIcon />
                </Button>
                <Button
                    onClick={onCancel}
                    size="icon"
                    type="button"
                    variant="secondary"
                >
                    <XIcon />
                </Button>
            </div>
        </Form>
    );
}
