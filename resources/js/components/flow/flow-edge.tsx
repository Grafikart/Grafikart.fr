import { BaseEdge, getStraightPath, useStore } from "@xyflow/react"
import { clsx } from "clsx"

type Props = {
  id: string
  source: string
  target: string
  sourceX: number
  sourceY: number
  targetX: number
  targetY: number
  type: string
}

export function FlowEdge({
  id,
  source,
  target,
  sourceX,
  sourceY,
  targetX,
  targetY,
  type,
}: Props) {
  const completed = useStore(
    (state) =>
      state.nodeLookup.get(source)?.data.completed ||
      state.nodeLookup.get(target)?.data.completed,
  )
  // Offset the position of the edge to center it
  const [edgePath] = getStraightPath({
    sourceX,
    sourceY: sourceY + 10,
    targetX,
    targetY: targetY + 10,
  })

  return (
    <BaseEdge
      id={id}
      path={edgePath}
      style={{ strokeDasharray: "5" }}
      className={clsx(
        " stroke-2!",
        completed
          ? "stroke-success! [stroke-dasharray:none]!"
          : "stroke-edge! animate-[dashdraw_0.5s_linear_infinite] [stroke-dasharray:5]",
        type === "secondary" && "opacity-30",
      )}
    />
  )
}
