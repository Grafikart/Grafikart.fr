import path from '@/actions/App/Http/Admin/BlogCategoryController.ts';
import { FormField } from '@/components/form-field.tsx';
import { Form } from '@/components/form.tsx';
import { withLayout } from '@/components/layout.tsx';
import { PageTitle } from '@/components/page-title.tsx';
import { Button } from '@/components/ui/button.tsx';
import type { BlogCategoryData } from '@/types';
import { SaveIcon } from 'lucide-react';

type Props = {
    item: BlogCategoryData;
};

export default withLayout<Props>(
    ({ item }) => {
        return (
            <Form
                id="form"
                className="space-y-4"
                {...(item.id ? path.update.form(item.id) : path.store.form())}
            >
                <PageTitle>{item.name || 'Nouvelle technologie'}</PageTitle>
                <FormField label="Nom" name="name" defaultValue={item.name} />
                <FormField label="Slug" name="slug" defaultValue={item.slug} />
            </Form>
        );
    },
    {
        breadcrumb: (props) => [
            { label: 'Catégories', href: path.index() },
            { label: props.item.name || 'Nouvelle technologie' },
        ],
        top: (
            <Button form="form" type="submit">
                <SaveIcon /> Enregistrer
            </Button>
        ),
    },
);
