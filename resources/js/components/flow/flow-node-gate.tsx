import { Handle, Position } from "@xyflow/react"
import { clsx } from "clsx"
import { memo } from "react"
import type { Node } from "./types.ts"

type Props = {
  data: Node["data"]
  selected: boolean
  type: string
}

export const FlowNodeGate = memo(({ data, selected }: Props) => {
  const titleStyle = clsx(
    "absolute -bottom-9 z-3 -translate-x-1/2 text-center font-bold text-md text-primary",
  )

  return (
    <div
      className={clsx(
        "relative size-px in-[.front]:pointer-events-none!",
        selected && "is-selected",
      )}
    >
      <img
        alt=""
        width={30}
        height={30}
        className="absolute -bottom-2 -left-6 size-18 -translate-x-1/2 translate-y-3 transition-all"
        src={`/images/flow/${data.icon || "gate"}.svg`}
      />
      <div>
        <div className={clsx(titleStyle, "isometric w-max")}>{data.title}</div>
      </div>
      {/* @ts-expect-error We are loose, Handle accepts no type */}
      <Handle position={Position.Top} className="react-flow__handle" />
    </div>
  )
})
