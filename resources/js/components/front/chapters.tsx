import { BookOpenTextIcon } from 'lucide-react';

import { Card } from '@/components/ui/card.tsx';
import { formatDuration } from '@/lib/date.ts';
import { cn } from '@/lib/utils.ts';
import type { FormationChapterData, FormationCourseData } from '@/types';

type ChaptersProps = {
    chapters: FormationChapterData[];
};

export function Chapters({ chapters }: ChaptersProps) {
    return (
        <div className="space-y-4 rounded-2xl">
            <h2 className="text-foreground-title mb-4 flex items-center gap-2 text-2xl font-bold">
                <BookOpenTextIcon className="size-5" />
                Chapitres
            </h2>
            {chapters.map((chapter, k) => (
                <div key={chapter.title} className="space-y-3">
                    <h3 className="text-foreground-title text-lg font-semibold">
                        {k + 1}. {chapter.title}
                    </h3>
                    <Card className="gap-0 p-0 text-sm">
                        {chapter.courses.map((course) => (
                            <Course key={course.id} course={course} />
                        ))}
                    </Card>
                </div>
            ))}
        </div>
    );
}

function Course({ course }: { course: FormationCourseData }) {
    return (
        <a
            href={course.url}
            className={cn(
                'border-l-3 hover:bg-background flex w-full items-center justify-between gap-4 rounded-sm border-b border-x-transparent px-4 py-3 text-start first:border-t',
            )}
        >
            <p>{course.title}</p>
            <div className="text-muted whitespace-nowrap text-sm">
                {formatDuration(course.duration)}
            </div>
        </a>
    );
}
