import { GripVerticalIcon, PlusIcon, XCircleIcon } from 'lucide-react';
import { type KeyboardEventHandler, useState } from 'react';

import CourseController from '@/actions/App/Http/Cms/CourseController.ts';
import { Button } from '@/components/ui/button.tsx';
import { Card } from '@/components/ui/card.tsx';
import { Sortable, SortableItem, SortableItemHandle } from '@/components/ui/sortable.tsx';
import { Spinner } from '@/components/ui/spinner.tsx';
import { apiFetch } from '@/hooks/use-api-fetch.ts';
import type { ChapterData, OptionItemData } from '@/types';


type Props = {
    defaultValue: ChapterData[];
    name?: string
};

export function ChaptersInput({ defaultValue, name = 'chapters' }: Props) {
    const [chapters, setChapters] = useState<ChapterData[]>(defaultValue)

    const addChapter = (title: string) => {
        setChapters((prev) => [...prev, { title, courses: [] }]);
    };

    const removeChapter = (index: number) => {
        setChapters((prev) => prev.filter((_, i) => i !== index));
    };

    const addCourse = (chapterIndex: number, course: OptionItemData) => {
        setChapters((prev) =>
            prev.map((chapter, i) =>
                i === chapterIndex
                    ? { ...chapter, courses: [...chapter.courses, course] }
                    : chapter
            )
        );
    };

    const removeCourse = (chapterIndex: number, courseIndex: number) => {
        setChapters((prev) =>
            prev.map((chapter, i) =>
                i === chapterIndex
                    ? {
                          ...chapter,
                          courses: chapter.courses.filter(
                              (_, ci) => ci !== courseIndex
                          ),
                      }
                    : chapter
            )
        );
    };

    const setCourses = (chapterIndex: number, courses: OptionItemData[]) => {
        setChapters((prev) =>
            prev.map((chapter, i) =>
                i === chapterIndex
                    ? { ...chapter, courses: courses }
                    : chapter
            )
        );
    };


    const handleAdd: KeyboardEventHandler<HTMLInputElement> = (e) => {
        if (e.key === 'Enter') {
            e.preventDefault()
            addChapter(e.currentTarget.value)
            e.currentTarget.value = '';
        }
    }

    return (
        <div className="flex flex-wrap items-start gap-4">
            {chapters.map((chapter, chapterIndex) => (
                <ChapterItem
                    key={chapterIndex}
                    chapter={chapter}
                    index={chapterIndex}
                    name={name}
                    onRemove={() => removeChapter(chapterIndex)}
                    onAddCourse={(course) => addCourse(chapterIndex, course)}
                    onRemoveCourse={(courseIndex) =>
                        removeCourse(chapterIndex, courseIndex)
                    }
                    onReorderCourse={(courses) =>
                        setCourses(chapterIndex, courses)
                    }
                />
            ))}
            <input placeholder="Ajouter un chapitre" onKeyDown={handleAdd} className="w-full"/>
        </div>
    );
}

type ChapterItemProps = {
    chapter: ChapterData;
    index: number;
    name: string;
    onRemove: () => void;
    onAddCourse: (course: OptionItemData) => void;
    onRemoveCourse: (courseIndex: number) => void;
    onReorderCourse: (courses: OptionItemData[]) => void;
};

function ChapterItem({
    chapter,
    index,
    name,
    onRemove,
    onAddCourse,
    onRemoveCourse,
    onReorderCourse,
}: ChapterItemProps) {
    const [fetching, setFetching] = useState(false)
    const handleAdd: KeyboardEventHandler<HTMLInputElement> = async (e) => {
        if (e.key === 'Enter') {
            e.preventDefault()
            setFetching(true)
            try {
                const input = e.currentTarget
                const item = await apiFetch<OptionItemData>(CourseController.show(input.valueAsNumber).url)
                input.value = '';
                onAddCourse(item)
            } catch (e) {
                console.error(e)
            } finally{
                setFetching(false)
            }
        }
    }
    return (
        <div className="grid grid-cols-1 gap-2 w-70">
            <div className="flex items-center">
                <input type="text" defaultValue={chapter.title} name={`${name}[${index}][title]`} className="w-full"/>
                <Button type="button" variant="ghost" size="icon-xs" onClick={onRemove}>
                    <XCircleIcon className="text-muted-foreground"/>
                </Button>
            </div>
            <div className="grid grid-cols-1 gap-2">
                <Sortable value={chapter.courses} getItemValue={c => c.id.toString()} onValueChange={onReorderCourse} className="grid grid-cols-1 gap-2">
                {chapter.courses.map((course, k) => (
                    <SortableItem key={course.id} value={course.id.toString()}>
                        <input type="hidden" value={course.id} name={`${name}[${index}][ids][${k}]`} />
                        <Card className="p-2 flex flex-row items-center w-full gap-1">
                            <SortableItemHandle asChild>
                                <Button type="button" variant="ghost" size="icon-xs" className="flex-none">
                                    <GripVerticalIcon/>
                                </Button>
                            </SortableItemHandle>
                            {course.name}
                            <Button type="button" variant="ghost" size="icon-xs" onClick={() => onRemoveCourse(k)} className="flex-none ml-auto">
                                <XCircleIcon className="text-muted-foreground"/>
                            </Button>
                        </Card>
                    </SortableItem>
                ))}
                </Sortable>
                <Card className="flex flex-row items-center w-full gap-1 p-2">
                    <Button type="button" variant="ghost" size="icon-xs" disabled className="flex-none">
                        {fetching ? <Spinner/> : <PlusIcon/>}
                    </Button>
                    <input type="number" placeholder="Ajouter un cours" className="w-full outline-none disabled:opacity-50" onKeyDown={handleAdd} disabled={fetching}/>
                </Card>
            </div>
        </div>
    )
}

