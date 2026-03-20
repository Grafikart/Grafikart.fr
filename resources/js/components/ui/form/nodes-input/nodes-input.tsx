import { ConnectionMode, ReactFlow, useReactFlow } from "@xyflow/react"
import { ExpandIcon, ShrinkIcon } from "lucide-react"
import { type MouseEventHandler, useMemo, useRef, useState } from "react"
import { edgeTypes, nodeTypes } from "@/components/flow/flow-config.ts"
import { IsoBackground } from "@/components/flow/iso-background.tsx"
import { Lasso } from "@/components/flow/lasso.tsx"
import { FlowAdd } from "@/components/ui/form/nodes-input/flow-add.tsx"
import { FlowDrawer } from "@/components/ui/form/nodes-input/flow-drawer.tsx"
import { useKeyDown } from "@/hooks/use-key-down.ts"
import { useToggle } from "@/hooks/use-toggle.ts"
import { type Position, snapGrid } from "@/lib/2d.ts"
import type { PathFormData } from "@/types"
import { FlowEdgeMenu } from "./flow-edge-menu.tsx"
import { useContextMenu } from "./use-context-menu.ts"
import { GraphContext, useGraph } from "./use-graph.ts"

type Props = {
  defaultValue: PathFormData["nodes"]
}

export const NodesInput = (props: Props) => {
  const { screenToFlowPosition } = useReactFlow()
  const spacePressed = useKeyDown(" ")
  const [isExpanded, toggleExpanded] = useToggle()
  const {
    updateNode,
    nodes,
    edges,
    onNodesChange,
    onEdgesChange,
    addNode,
    updateEdge,
    onConnect,
    value,
    setValue,
  } = useGraph(props.defaultValue)

  const containerRef = useRef<HTMLDivElement>(null)
  const { edge, reset, position, open } = useContextMenu(nodes, edges)
  const [selectedNodeId, setSelectedNodeId] = useState<string | null>(null)
  const selectedNode = nodes.find((node) => node.id === selectedNodeId)
  const [addPosition, setAddPosition] = useState<Position | null>(null)

  const onDblClick: MouseEventHandler = (e) => {
    if ((e.target as HTMLElement).tagName === "INPUT") {
      return
    }
    e.preventDefault()
    e.stopPropagation()
    setAddPosition(
      screenToFlowPosition({
        x: e.clientX,
        y: e.clientY,
      }),
    )
  }

  const onAdd = (item: {
    name: string
    type: string
    icon?: string
    id: number | null
  }) => {
    if (!addPosition) {
      return
    }
    const node = addNode({
      position: addPosition,
      type: item.type,
      data: {
        title: item.name,
        contentId: item.id,
        contentType: item.type,
        description: null,
        parents: [],
        icon: item.icon ?? "",
      },
    })
    setAddPosition(null)
    setSelectedNodeId(node.id)
  }

  return (
    <div className={isExpanded ? "fixed inset-0 z-50 bg-background" : ""}>
      <div
        ref={containerRef}
        className="w-full relative border"
        style={{
          height: isExpanded ? "100vh" : "max(200px, calc(100vh - 290px))",
        }}
      >
        {addPosition && (
          <FlowAdd onSelect={onAdd} onClose={() => setAddPosition(null)} />
        )}
        {selectedNode && (
          <FlowDrawer node={selectedNode} onChange={updateNode} />
        )}
        <button
          type="button"
          onClick={toggleExpanded}
          className="absolute bottom-2 right-2 z-3 rounded-md bg-background border p-1.5 text-muted-foreground hover:text-foreground"
        >
          {isExpanded ? <ShrinkIcon size={16} /> : <ExpandIcon size={16} />}
        </button>
        <GraphContext value={useMemo(() => ({ updateNode }), [updateNode])}>
          <ReactFlow
            maxZoom={2}
            minZoom={0.8}
            snapGrid={snapGrid}
            connectionMode={ConnectionMode.Loose}
            snapToGrid
            nodes={nodes}
            edges={edges}
            onNodesChange={onNodesChange}
            onEdgesChange={onEdgesChange}
            onConnect={onConnect}
            nodeTypes={nodeTypes}
            edgeTypes={edgeTypes}
            onNodeClick={(_e, node) => {
              setSelectedNodeId(node.id)
            }}
            onEdgeContextMenu={(event, edge) => {
              event.preventDefault()
              const rect = containerRef.current!.getBoundingClientRect()
              open(edge.id, {
                x: event.clientX - rect.left,
                y: event.clientY - rect.top,
              })
            }}
            onNodeContextMenu={(event, node) => {
              event.preventDefault()
              const rect = containerRef.current!.getBoundingClientRect()
              open(node.id, {
                x: event.clientX - rect.left,
                y: event.clientY - rect.top,
              })
            }}
            onPaneClick={() => {
              reset()
            }}
            deleteKeyCode={"Delete"}
            fitView
            onDoubleClickCapture={onDblClick}
          >
            <IsoBackground id="2" gap={snapGrid} color="var(--border)" />
            {spacePressed && <Lasso partial />}
          </ReactFlow>
          {edge && (
            <FlowEdgeMenu
              edge={edge}
              position={position}
              onEdgeChange={(e) => {
                updateEdge(e)
                reset()
              }}
            />
          )}
        </GraphContext>
      </div>
      <textarea
        name="nodes"
        className="w-full min-h-100 border p-2"
        onChange={(e) => setValue(JSON.parse(e.currentTarget.value))}
        value={JSON.stringify(value, null, 2)}
      />
    </div>
  )
}
