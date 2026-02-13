import { Handle, Position } from "@xyflow/react"
import { memo } from "react"
import type { Node } from "./types.ts"

type Props = {
  data: Node["data"]
}

const size = 50

export const FlowNodeFork = memo(({ data }: Props) => {
  return (
    <div className="relative size-px">
      <SupportIcon />
      <svg
        className="-translate-1/2 absolute top-0 left-0"
        width={size}
        height={size}
        viewBox={`${-size / 2} ${-size / 2} ${size} ${size}`}
        xmlns="http://www.w3.org/2000/svg"
      >
        <Circle fill="var(--color-background)" stroke="transparent" />
      </svg>
      <div className="skew-15 absolute -inset-5 -rotate-45">
        <svg
          xmlns="http://www.w3.org/2000/svg"
          viewBox="0 0 24 24"
          className="size-10 animate-spin fill-success duration-[2s]"
          style={{
            animationDuration: "10s",
            animationDirection: "reverse",
          }}
        >
          <path
            className="fill-edge"
            d="M4 3h1v2.15a9.76 9.76 0 0 1 14.33.37l-.52.91A8.77 8.77 0 0 0 5.58 6H8v1H4zm16.4 4.67-2 3.47.87.5 1.18-2.06a8.77 8.77 0 0 1-7.27 11.13l.54.93a9.75 9.75 0 0 0 7.73-12.21l1.91 1.1.5-.86zm-9.1 13.8L9.3 18l-.86.5 1.12 1.95A8.76 8.76 0 0 1 3.74 9H2.67a9.78 9.78 0 0 0 6.7 12.43L7.33 22.6l.5.86z"
          />
        </svg>
      </div>
      <div>
        <div className="absolute -bottom-9 z-3 w-max -translate-x-1/2 text-center font-bold text-md">
          {data.title}
        </div>
      </div>
      {/* @ts-expect-error We are loose, Handle accepts no type */}
      <Handle position={Position.Top} className="react-flow__handle" />
    </div>
  )
})

function SupportIcon() {
  return (
    <svg
      xmlns="http://www.w3.org/2000/svg"
      fill="none"
      className="-translate-1/2 absolute top-0 left-0 w-10"
      viewBox="0 0 259 174"
    >
      <path
        fill="var(--node-stroke)"
        d="M220.6 151.45c-50.46 29.13-132.27 29.13-182.73 0C-5.5 126.41 2.06 112.29 0 75.1c50.46-29.13 208.04-27.63 258.5 1.5 0 45.65-.5 53.25-37.9 74.85Z"
      />
      <path
        fill="var(--node-fill)"
        stroke="var(--node-stroke)"
        strokeWidth="10"
        d="M129.23 5c32.42 0 64.58 7.16 88.87 21.18 24.32 14.04 35.34 31.74 35.34 48.42 0 16.68-11.02 34.38-35.34 48.42-24.3 14.02-56.45 21.18-88.87 21.18s-64.57-7.16-88.86-21.18C16.05 108.98 5.02 91.28 5.02 74.6c0-16.68 11.03-34.38 35.35-48.42C64.66 12.16 96.8 5 129.23 5Z"
      />
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
