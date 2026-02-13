import { PlayCircleIcon, XIcon } from "lucide-react"
import type { Node } from "@/components/flow/types"
import { Chapters } from "@/components/front/chapters.tsx"
import { Button } from "@/components/ui/button.tsx"
import { Card } from "@/components/ui/card.tsx"
import { Spinner } from "@/components/ui/spinner.tsx"
import { useApiFetch } from "@/hooks/use-api-fetch.ts"
import { formatDuration } from "@/lib/date.ts"
import type { FormationViewData } from "@/types"

export function NodeDetail(props: { node: Node; onClose: () => void }) {
  const { data, isFetching } = useApiFetch<FormationViewData>(
    `/cursus/${props.node.data.id}`,
  )
  return (
    <div className="container absolute inset-0 z-5 grid grid-cols-[1fr_350px] gap-6 p-4 pt-23 text-lg">
      <div className="mx-auto flex max-w-200 flex-col">
        <div
          className="h-50 flex-none hover:text-primary"
          onClick={props.onClose}
        >
          <XIcon className="ml-auto size-6 cursor-pointer" />
        </div>
        <div className="grid starting:-translate-x-20 grid-cols-1 gap-4 starting:opacity-0 transition-all duration-600">
          <Card className="h-full p-4">
            {isFetching && <Spinner />}
            {data && (
              <div
                className="prose prose-lg"
                dangerouslySetInnerHTML={{
                  __html: data.content,
                }}
              />
            )}
          </Card>

          {data && (
            <div className="flex gap-4 *:grow">
              <Card className="gap-3 p-4">
                <h3 className="font-bold text-lg">Informations</h3>

                <ul className="list-inside list-disc leading-relaxed">
                  <li>{formatDuration(data.duration)} de vidéos</li>
                  <li>
                    {data.chapters.reduce(
                      (acc, c) => acc + c.courses.length,
                      0,
                    )}{" "}
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
          )}
          <Button size="lg" className="h-auto py-2 font-medium text-lg">
            <PlayCircleIcon className="size-5" />
            Commencer cette série
          </Button>
        </div>
      </div>

      <div className="max-h-screen starting:translate-x-20 overflow-auto starting:opacity-0 transition-all duration-600">
        {data?.chapters ? <Chapters chapters={data?.chapters} /> : <Spinner />}
      </div>
    </div>
  )
}
