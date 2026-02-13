import { ConnectionMode, type FitViewOptions, ReactFlow } from "@xyflow/react"
import { edgeTypes, nodeTypes } from "@/components/flow/flow-config.ts"
import { IsoBackground } from "@/components/flow/iso-background.tsx"
import { useGraph } from "@/components/ui/form/nodes-input/use-graph.ts"
import { snapGrid } from "@/lib/2d.ts"
import type { PathViewData } from "@/types"

type Props = {
  path: PathViewData
}

const fitViewOptions = { padding: "100px" } satisfies FitViewOptions

export function PathPreview({ path }: Props) {
  const { nodes, edges } = useGraph(path.nodes)
  return (
    <ReactFlow
      connectionMode={ConnectionMode.Loose}
      maxZoom={2}
      minZoom={0.1}
      nodes={nodes}
      edges={edges}
      nodeTypes={nodeTypes}
      edgeTypes={edgeTypes}
      fitView
      fitViewOptions={fitViewOptions}
    >
      <IsoBackground id="2" gap={snapGrid} color="var(--color-border)" />
    </ReactFlow>
  )
}

export default {
  component: PathPreview,
  props: { path: "json" },
}
