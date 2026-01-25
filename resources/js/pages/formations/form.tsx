import { SaveIcon } from 'lucide-react';

import route from '@/actions/App/Http/Cms/FormationController';
import { FormField } from '@/components/form-field.tsx';
import { Form } from '@/components/form.tsx';
import { withLayout } from '@/components/layout.tsx';
import { PageTitle } from '@/components/page-title.tsx';
import { Button } from '@/components/ui/button.tsx';
import {
    Card,
    CardContent,
    CardHeader,
    CardTitle,
} from '@/components/ui/card.tsx';
import { AttachmentSelector } from '@/components/ui/form/attachment-selector.tsx';
import { ChaptersInput } from '@/components/ui/form/chapters-input.tsx';
import { DatetimePicker } from '@/components/ui/form/datetime-picker.tsx';
import { LevelSelector } from '@/components/ui/form/level-selector.tsx';
import { MDEditor } from '@/components/ui/form/mdeditor.tsx';
import { SlugInput } from '@/components/ui/form/slug-input.tsx';
import { TechnologySelector } from '@/components/ui/form/technology-selector.tsx';
import { Label } from '@/components/ui/label.tsx';
import { Switch } from '@/components/ui/switch.tsx';
import { Textarea } from '@/components/ui/textarea.tsx';
import type { FormationFormData } from '@/types';

import '@mdxeditor/editor/style.css';

type Props = {
    item: FormationFormData;
};

export default withLayout<Props>(
    ({ item }) => {
        const url = item.id ? `/formations/${item.slug}` : undefined;
        const formAction = item.id
            ? route.update.form(item.id)
            : route.store.form();

        return (
            <Form
                className="grid grid-cols-[1fr_300px] gap-6"
                id="form"
                {...formAction}
            >
                <PageTitle>{item.title || 'Nouvelle formation'}</PageTitle>
                <main>
                    <input
                        name="title"
                        defaultValue={item.title}
                        className="mb-1 block w-full text-2xl font-semibold outline-none"
                        placeholder="Titre"
                    />
                    <SlugInput
                        defaultValue={item.slug}
                        prefix="grafikart.fr/formations/"
                        url={url}
                    />
                    <MDEditor defaultValue={item.content} name="content" />

                    <h2 className="mt-4 mb-1 block w-full text-2xl font-semibold">
                        Chapitres
                    </h2>
                    <ChaptersInput defaultValue={item.chapters} />
                </main>
                <aside className="space-y-6">
                    <div className="flex justify-end gap-4">
                        <div className="flex items-center space-x-2">
                            <Switch
                                id="online"
                                name="online"
                                defaultChecked={item.online}
                            />
                            <Label htmlFor="online">En ligne</Label>
                        </div>
                    </div>

                    <Card className="overflow-hidden pt-0">
                        <AttachmentSelector
                            name="image"
                            className="aspect-video"
                            defaultValue={item.attachment?.id}
                            attachableId={item.id}
                            attachableType="Formation"
                            preview={item.attachment?.url}
                        />
                        <CardContent className="space-y-4">
                            <FormField
                                label="Publié le"
                                name="createdAt"
                                defaultValue={item.createdAt}
                                render={<DatetimePicker />}
                            />
                            <FormField label="Résumé" name="short">
                                <Textarea
                                    name="short"
                                    defaultValue={item.short ?? ''}
                                    rows={3}
                                />
                            </FormField>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardContent>
                            <FormField
                                label="Outils & Langages"
                                name="technologies"
                                render={
                                    <TechnologySelector
                                        defaultValue={item.technologies ?? []}
                                    />
                                }
                            />
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader>
                            <CardTitle>Informations</CardTitle>
                        </CardHeader>
                        <CardContent className="space-y-4">
                            <FormField
                                label="Difficulté"
                                name="level"
                                defaultValue={item.level}
                                render={<LevelSelector />}
                            />
                            <FormField
                                label="Playlist Youtube"
                                name="youtubePlaylist"
                                defaultValue={item.youtubePlaylist ?? ''}
                            />
                            <FormField
                                label="Liens"
                                type="textarea"
                                name="links"
                                defaultValue={item.links ?? ''}
                            />
                            <div className="flex items-end gap-2">
                                <FormField
                                    label="Dépréciée par"
                                    name="deprecatedBy"
                                    defaultValue={item.deprecatedBy ?? ''}
                                />
                                <div className="pb-1">
                                    <Switch
                                        name="forceRedirect"
                                        defaultChecked={item.forceRedirect}
                                    />
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </aside>
            </Form>
        );
    },
    {
        breadcrumb: (props) => [
            { label: 'Formations', href: route.index() },
            { label: props.item.title || 'Nouveau' },
        ],
        top: (
            <Button form="form" type="submit">
                <SaveIcon /> Enregistrer
            </Button>
        ),
    },
);
