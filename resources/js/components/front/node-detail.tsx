import { PlayCircleIcon, XIcon } from 'lucide-react';

import type { Node } from '@/components/flow/types';
import { Chapters } from '@/components/front/chapters.tsx';
import { Button } from '@/components/ui/button.tsx';
import { Card } from '@/components/ui/card.tsx';
import { Spinner } from '@/components/ui/spinner.tsx';
import { useApiFetch } from '@/hooks/use-api-fetch.ts';
import { formatDuration } from '@/lib/date.ts';
import type { FormationViewData } from '@/types';

export function NodeDetail(props: { node: Node; onClose: () => void }) {
    const { data, isFetching } = useApiFetch<FormationViewData>(
        `/cursus/${props.node.data.id}`,
    );
    return (
        <div className="pt-23 z-5 container absolute inset-0 grid grid-cols-[1fr_350px] gap-6 p-4 text-lg">
            <div className="max-w-200 mx-auto flex flex-col">
                <div
                    className="h-50 hover:text-primary flex-none"
                    onClick={props.onClose}
                >
                    <XIcon className="ml-auto size-6 cursor-pointer" />
                </div>
                <div className="starting:-translate-x-20 starting:opacity-0 duration-600 grid grid-cols-1 gap-4 transition-all">
                    <Card className="h-full p-4">
                        {isFetching && <Spinner />}
                        {data && (
                            <>
                                <div
                                    className="prose prose-lg"
                                    dangerouslySetInnerHTML={{
                                        __html: data.content,
                                    }}
                                />
                            </>
                        )}
                    </Card>

                    {data && (
                        <div className="flex gap-4 *:grow">
                            <Card className="gap-3 p-4">
                                <h3 className="text-lg font-bold">
                                    Informations
                                </h3>

                                <ul className="list-inside list-disc leading-relaxed">
                                    <li>
                                        {formatDuration(data.duration)} de
                                        vidéos
                                    </li>
                                    <li>
                                        {data.chapters.reduce(
                                            (acc, c) => acc + c.courses.length,
                                            0,
                                        )}{' '}
                                        chapitres
                                    </li>
                                </ul>
                            </Card>
                            {data.links && (
                                <Card className="gap-3 p-4">
                                    <h3 className="text-lg font-bold">
                                        Liens utiles
                                    </h3>

                                    <div
                                        className="leading-relaxed *:list-inside *:list-disc [&_a]:hover:underline"
                                        dangerouslySetInnerHTML={{
                                            __html: data.links ?? '',
                                        }}
                                    />
                                </Card>
                            )}
                        </div>
                    )}
                    <Button
                        size="lg"
                        className="h-auto py-2 text-lg font-medium"
                    >
                        <PlayCircleIcon className="size-5" />
                        Commencer cette série
                    </Button>
                </div>
            </div>

            <div className="starting:translate-x-20 starting:opacity-0 duration-600 max-h-screen overflow-auto transition-all">
                {data?.chapters ? (
                    <Chapters chapters={data?.chapters} />
                ) : (
                    <Spinner />
                )}
            </div>
        </div>
    );
}
