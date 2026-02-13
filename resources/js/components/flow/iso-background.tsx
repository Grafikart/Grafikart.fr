import { type ReactFlowState, useStore } from "@xyflow/react"
import { memo, useRef } from "react"
import { shallow } from "zustand/shallow"

const selector = (s: ReactFlowState) => ({
  transform: s.transform,
  patternId: `pattern-${s.rfId}`,
})

type Props = {
  id: string
  gap: [number, number]
  color: string
}

function BackgroundComponent({ id, gap, color }: Props) {
  const ref = useRef<SVGSVGElement>(null)
  const { transform, patternId } = useStore(selector, shallow)

  const scaledOffset: [number, number] = [
    transform[2] || 1 + gap[0],
    transform[2] || 1 + gap[1],
  ]

  const _patternId = `${patternId}${id ? id : ""}`
  const scaledGap: [number, number] = [
    2 * gap[0] * transform[2] || 1,
    2 * gap[1] * transform[2] || 1,
  ]

  return (
    <svg
      className="react-flow__background absolute inset-0 size-full"
      ref={ref}
      data-testid="rf__background"
    >
      <pattern
        id={_patternId}
        x={transform[0] % scaledGap[0]}
        y={transform[1] % scaledGap[1]}
        width={scaledGap[0]}
        height={scaledGap[1]}
        patternUnits="userSpaceOnUse"
        patternTransform={`translate(-${scaledOffset[0]},-${scaledOffset[1]})`}
      >
        <path
          d={`M 0 ${scaledGap[1]} L ${scaledGap[0]} 0`}
          stroke={color}
          strokeWidth={1}
        />
        <path
          d={`M 0 0 L ${scaledGap[0]} ${scaledGap[1]}`}
          stroke={color}
          strokeWidth={0.5}
        />
      </pattern>
      <rect
        x="0"
        y="0"
        width="100%"
        height="100%"
        fill={`url(#${_patternId})`}
      />
    </svg>
  )
}

BackgroundComponent.displayName = "Background"

export const IsoBackground = memo(BackgroundComponent)
