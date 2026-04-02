import { Handle, Position } from "@xyflow/react"
import { clsx } from "clsx"
import { memo } from "react"
import type { Node } from "./types.ts"

type Props = {
  data: Node["data"]
  selected: boolean
}

export const FlowNode = memo(({ data, selected }: Props) => {
  const completed = data.completed === true

  return (
    <div
      className={clsx(
        "relative size-px group-data-[hide-title=true]:opacity-20 group/node",
        selected && "is-selected opacity-100!",
        completed && "is-completed",
      )}
    >
      <SupportIcon selected={selected} completed={completed} />
      {data.icon && (
        <img
          alt=""
          width={50}
          height={50}
          className="absolute bottom-2 left-0 size-12 -translate-x-1/2 transition-all group-hover/node:-translate-y-1"
          src={`/uploads/icons/${data.icon}.svg`}
        />
      )}
      <div
        className={clsx(
          "title absolute -bottom-9 z-3 w-max -translate-x-1/2 text-center font-bold text-md",
          selected && "text-primary opacity-100!",
          completed && "text-success",
        )}
      >
        {data.title}
      </div>
      {/* @ts-expect-error We are loose, Handle accepts no type */}
      <Handle position={Position.Top} className="react-flow__handle" />
    </div>
  )
})

function SupportIcon(props: { selected: boolean; completed: boolean }) {
  return (
    <svg
      xmlns="http://www.w3.org/2000/svg"
      fill="none"
      className="-translate-1/2 absolute top-0 left-0 w-10"
      viewBox="0 0 259 174"
    >
      <path
        className={clsx(
          "fill-edge",
          props.selected && "fill-primary",
          props.completed && "fill-success",
        )}
        d="M220.6 151.45c-50.46 29.13-132.27 29.13-182.73 0C-5.5 126.41 2.06 112.29 0 75.1c50.46-29.13 208.04-27.63 258.5 1.5 0 45.65-.5 53.25-37.9 74.85Z"
      />
      <path
        className={clsx(
          "fill-card stroke-edge",
          props.selected && "fill-primary-light stroke-primary",
          props.completed && "fill-success-bg stroke-success",
        )}
        strokeWidth="10"
        d="M129.23 5c32.42 0 64.58 7.16 88.87 21.18 24.32 14.04 35.34 31.74 35.34 48.42 0 16.68-11.02 34.38-35.34 48.42-24.3 14.02-56.45 21.18-88.87 21.18s-64.57-7.16-88.86-21.18C16.05 108.98 5.02 91.28 5.02 74.6c0-16.68 11.03-34.38 35.35-48.42C64.66 12.16 96.8 5 129.23 5Z"
      />
      {props.completed && (
        <>
          <path
            fill="var(--success-text"
            d="M185.87 47.91c5.29-2.76 17.38-5.92 22.48-2.97 0 6.22 1.33 11.2-3.93 14.25l-90.38 52.17c-5.26 3.04-13.79 3.04-19.05 0L53.91 87.65c-4.28-2.47-3.92-10.4-3.92-14.66 5.26-3.04 17.71.62 22.97 3.66l31.55 18.21 80.86-46.67.5-.28Z"
          />
          <path
            fill="var(--green)"
            d="M185.87 38.91c5.29-2.76 13.45-2.67 18.55.28 5.26 3.03 5.26 7.96 0 11l-90.38 52.17c-5.26 3.04-13.79 3.04-19.05 0L53.91 78.65c-5.26-3.04-5.26-7.97 0-11 5.26-3.04 13.8-3.04 19.05 0l31.55 18.21 80.86-46.67.5-.28Z"
          />
        </>
      )}
    </svg>
  )
}

type CircleProps = {
  fill: string
  transform?: string
  stroke: string
}

/**
 * Draw an isometric circle (used for support)
 */
export function Circle({ stroke, fill, transform }: CircleProps) {
  return (
    <g transform={`${transform ?? ""} scale(2)`}>
      <path
        d="M-0.123473 5.49772C5.39937 5.49772 9.87653 3.03529 9.87653 -0.00228071C9.87653 -3.03985 5.39937 -5.50228 -0.123473 -5.50228C-5.64632 -5.50228 -10.1235 -3.03985 -10.1235 -0.00228071C-10.1235 3.03529 -5.64632 5.49772 -0.123473 5.49772Z"
        stroke={stroke}
        fill={fill}
        strokeWidth={0.5}
      />
    </g>
  )
}
