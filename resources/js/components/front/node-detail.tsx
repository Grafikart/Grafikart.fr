import { XIcon } from "lucide-react"
import type { Node } from "@/components/flow/types"
import { Card } from "@/components/ui/card.tsx"
import { Spinner } from "@/components/ui/spinner.tsx"
import { useApiFetch } from "@/hooks/use-api-fetch.ts"
import type { CourseViewData, FormationViewData } from "@/types"
import { FormationDetail } from "@/components/front/formation-detail.tsx"
import { CourseDetail } from "@/components/front/course-detail.tsx"
import type { MouseEventHandler } from "react"

export function NodeDetail(props: { node: Node; onClose: () => void }) {
  const isFork = props.node.type === "fork"
  const video = props.node.data.meta?.video
  const { data, isFetching } = useApiFetch<FormationViewData | CourseViewData>(
    `/cursus/${props.node.data.id}`,
    {
      enabled: !isFork,
    },
  )

  const handleOverlayClick: MouseEventHandler<HTMLDivElement> = (e) => {
    if (e.target === e.currentTarget) {
      props.onClose()
    }
  }
  return (
    <div className="absolute inset-0 z-5 grid gap-6 p-4 pt-23 text-lg overflow-auto">
      <div className="container mx-auto" onClick={handleOverlayClick}>
        {/* Space that let the clicked node appears under */}
        <div
          className="h-80 flex-none flex items-end justify-end hover:text-primary"
          onClick={props.onClose}
        />
        {isFetching && !isFork && (
          <div className="flex justify-center absolute">
            <Spinner className="size-8 text-muted" />
          </div>
        )}
        {isFork && <ForkDetail video={video} onClose={props.onClose} />}
        {data?.type === "formation" && (
          <FormationDetail
            data={data as FormationViewData}
            onClose={props.onClose}
          />
        )}
        {data?.type === "course" && (
          <CourseDetail data={data as CourseViewData} onClose={props.onClose} />
        )}
      </div>
    </div>
  )
}

function ForkDetail({
  video,
  onClose,
}: {
  video: string | null | undefined
  onClose: () => void
}) {
  return (
    <div className="max-w-250 mx-auto starting:translate-y-20 starting:opacity-0 duration-600">
      <div className="flex justify-end -mt-10">
        <CloseButton onClick={onClose} />
      </div>
      {video ? (
        <iframe
          className="aspect-video w-full"
          src={`https://www.youtube-nocookie.com/embed/${video}?autoplay=1`}
          allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
          referrerPolicy="strict-origin-when-cross-origin"
          allowFullScreen
        />
      ) : (
        <div className="p-8 text-muted-foreground">
          Aucune vidéo n'est configurée pour ce rond point.
        </div>
      )}
    </div>
  )
}

export function CloseButton({ onClick }: { onClick: () => void }) {
  return (
    <button onClick={onClick} className="hover:text-primary">
      <XIcon className="mb-4 size-6 cursor-pointer hover:text-primary" />
    </button>
  )
}
