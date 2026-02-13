import { BookOpenTextIcon } from "lucide-react"
import { Card } from "@/components/ui/card.tsx"
import { formatDuration } from "@/lib/date.ts"
import { cn } from "@/lib/utils.ts"
import type { FormationChapterData, FormationCourseData } from "@/types"

type ChaptersProps = {
  chapters: FormationChapterData[]
}

export function Chapters({ chapters }: ChaptersProps) {
  return (
    <div className="space-y-4 rounded-2xl">
      <h2 className="mb-4 flex items-center gap-2 font-bold text-2xl text-foreground-title">
        <BookOpenTextIcon className="size-5" />
        Chapitres
      </h2>
      {chapters.map((chapter, k) => (
        <div key={chapter.title} className="space-y-3">
          <h3 className="font-semibold text-foreground-title text-lg">
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
  )
}

function Course({ course }: { course: FormationCourseData }) {
  return (
    <a
      href={course.url}
      className={cn(
        "flex w-full items-center justify-between gap-4 rounded-sm border-x-transparent border-b border-l-3 px-4 py-3 text-start first:border-t hover:bg-background",
      )}
    >
      <p>{course.title}</p>
      <div className="whitespace-nowrap text-muted text-sm">
        {formatDuration(course.duration)}
      </div>
    </a>
  )
}
