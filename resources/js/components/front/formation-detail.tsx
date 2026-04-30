import { FormationViewData } from "@/types"
import { Card } from "@/components/ui/card.tsx"
import { formatDuration } from "@/lib/date.ts"
import { Button } from "@/components/ui/button.tsx"
import { PlayCircleIcon } from "lucide-react"
import { Chapters } from "@/components/front/chapters.tsx"
import { CloseButton } from "@/components/front/node-detail.tsx"

export function FormationDetail({
  data,
  onClose,
}: {
  data: FormationViewData
  onClose: () => void
}) {
  const chapters = data.chapters
  return (
    <div className="grid gap-4 md:grid-cols-[1fr_350px] md:gap-8 items-start">
      {/* Content */}
      <div className="max-w-200 mx-auto flex flex-col gap-4 starting:-translate-x-20 starting:opacity-0 duration-600">
        <div className="-mt-10 flex justify-end">
          <CloseButton onClick={onClose} />
        </div>
        <Card className="p-4">
          <div
            className="prose prose-lg"
            dangerouslySetInnerHTML={{
              __html: data.content,
            }}
          />
        </Card>
        <div className="grid grid-cols-2 gap-4 *:grow">
          <Card className="gap-3 p-4">
            <h3 className="font-bold text-lg">Informations</h3>
            <ul className="list-inside list-disc leading-relaxed">
              <li>{formatDuration(data.duration)} de vidéos</li>
              <li>
                {data.chapters.reduce((acc, c) => acc + c.courses.length, 0)}{" "}
                chapitres
              </li>
            </ul>
          </Card>
          {data.links && (
            <Card className="gap-3 p-4">
              <h3 className="font-bold text-lg">Liens utiles</h3>

              <div
                className="leading-relaxed *:list-inside *:list-disc [&_a]:hover:underline"
                dangerouslySetInnerHTML={{
                  __html: data.links ?? "",
                }}
              />
            </Card>
          )}
        </div>
        <Button
          render={<a href={`${data?.url}/continue#autoplay`} />}
          size="lg"
          className="h-auto py-2 font-medium text-lg"
        >
          <PlayCircleIcon className="size-5" />
          Commencer cette série
        </Button>
      </div>
      {/* chapters */}
      <div className="starting:translate-x-20 mt-8 md:-mt-50 starting:opacity-0 transition-all duration-600">
        <Chapters chapters={chapters} />
      </div>
    </div>
  )
}
