import type { ComponentProps } from "react"
import type { Edge } from "@/components/flow/types.ts"
import { Card } from "@/components/ui/card.tsx"
import type { Position } from "@/lib/2d.ts"

type Props = {
  edge: Edge
  position: Position
  onEdgeChange: (edge: Edge) => void
}

export function FlowEdgeMenu(props: Props) {
  const onTypeChange = (type: string) => {
    props.onEdgeChange({
      ...props.edge,
      type,
    })
  }

  return (
    <Card
      className="absolute rounded-sm shadow min-w-30 p-0 gap-0"
      style={{
        left: props.position.x,
        top: props.position.y,
      }}
    >
      <Button onClick={() => onTypeChange("primary")}>Principal</Button>
      <Button onClick={() => onTypeChange("secondary")}>Secondaire</Button>
    </Card>
  )
}

function Button(props: ComponentProps<"button">) {
  return (
    <button
      {...props}
      type="button"
      className="p-2 px-4 cursor-pointer hover:text-secondary-hover w-full text-left hover:bg-muted"
    />
  )
}
