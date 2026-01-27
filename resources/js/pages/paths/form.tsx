import { ReactFlowProvider } from '@xyflow/react';
import { SaveIcon } from 'lucide-react';

import route from '@/actions/App/Http/Cms/PathController';
import { FormField } from '@/components/form-field.tsx';
import { Form } from '@/components/form.tsx';
import { withLayout } from '@/components/layout.tsx';
import { PageTitle } from '@/components/page-title.tsx';
import { Button } from '@/components/ui/button.tsx';
import { NodesInput } from '@/components/ui/form/nodes-input/nodes-input.tsx';
import type { PathFormData } from '@/types';

type Props = {
    item: PathFormData;
};

export default withLayout<Props>(
    ({ item }) => {
        const formAction = item.id
            ? route.update.form(item.id)
            : route.store.form();

        return (
            <Form className="space-y-4" id="form" {...formAction}>
                <PageTitle>{item.title || 'Nouveau parcours'}</PageTitle>
                <FormField
                    label="Titre"
                    name="title"
                    defaultValue={item.title}
                />
                <FormField
                    label="Description"
                    name="description"
                    type="textarea"
                    defaultValue={item.description}
                />
                <ReactFlowProvider>
                    <NodesInput defaultValue={item.nodes} />
                </ReactFlowProvider>
            </Form>
        );
    },
    {
        breadcrumb: (props) => [
            { label: 'Parcours', href: route.index() },
            { label: props.item.title || 'Nouveau parcours' },
        ],
        top: (
            <Button form="form" type="submit">
                <SaveIcon /> Enregistrer
            </Button>
        ),
    },
);
