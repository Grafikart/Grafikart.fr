import type { CourseViewData } from "@/types"
import { Card } from "@/components/ui/card.tsx"
import { CloseButton } from "@/components/front/node-detail.tsx"

export const slideInClass =
  "starting:translate-y-20 starting:opacity-0 duration-600"

export function CourseDetail({
  data,
  onClose,
}: {
  data: CourseViewData
  onClose: () => void
}) {
  return (
    <div className={`max-w-250 mx-auto ${slideInClass}`}>
      <div className="flex justify-between">
        <h3 className="font-bold text-2xl mb-4">{data.title}</h3>
        <CloseButton onClick={onClose} />
      </div>
      <Card className="pt-0 gap-0">
        <course-video
          class="grid place-items-center overflow-hidden aspect-video group relative rounded-t-lg cursor-pointer bg-[#000] w-full h-auto [&_iframe]:w-full [&_iframe]:aspect-video [&_iframe]:h-auto"
          video="KaheIi944OA"
          course={data.id.toString()}
        >
          <img
            alt=""
            loading="lazy"
            className="aspect-video w-full object-cover [grid-area:1/1]"
            src={data.poster}
          />
          <div className="inset-0 absolute bg-linear-to-b from-transparent to-video group-hover:opacity-80 transition-opacity [grid-area:1/1]"></div>
          <svg
            fill="none"
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 46 46"
            className="size-20 [grid-area:1/1] fill-white group-hover:scale-110 transition-all"
            style={{ filter: "drop-shadow(0 1px 20px #121C4280)" }}
          >
            <path d="M23 0C10.32 0 0 10.32 0 23s10.32 23 23 23 23-10.32 23-23S35.68 0 23 0zm8.55 23.83l-12 8A1 1 0 0118 31V15a1 1 0 011.55-.83l12 8a1 1 0 010 1.66z"></path>
          </svg>
        </course-video>
        <div className="p-8">
          <div
            className="prose prose-lg"
            dangerouslySetInnerHTML={{
              __html: data.content,
            }}
          />
        </div>
      </Card>
    </div>
  )
}
