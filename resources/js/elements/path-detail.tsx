import { QueryClientProvider } from "@tanstack/react-query"
import {
  ConnectionMode,
  type FitViewOptions,
  type NodeMouseHandler,
  ReactFlow,
  type Viewport,
} from "@xyflow/react"
import { useCallback, useEffect, useEffectEvent, useRef, useState } from "react"
import { edgeTypes, nodeTypes } from "@/components/flow/flow-config.ts"
import { IsoBackground } from "@/components/flow/iso-background.tsx"
import type { GraphInstance, Node } from "@/components/flow/types"
import { NodeDetail } from "@/components/front/node-detail.tsx"
import { useGraph } from "@/components/ui/form/nodes-input/use-graph.ts"
import { queryClient } from "@/hooks/use-api-fetch.ts"
import { snapGrid } from "@/lib/2d.ts"
import type { PathViewData } from "@/types"

type Props = {
  element: HTMLElement
  path: PathViewData
  completednodeids?: number[] | null
}

const fitViewOptions = { padding: "25px" } satisfies FitViewOptions
const duration = 800
const emptyArray = [] as number[]

export default function PathDetail({ element, path, completednodeids }: Props) {
  const { nodes, edges, updateNode } = useGraph(
    path.nodes,
    completednodeids ?? emptyArray,
  )
  const flow = useRef<GraphInstance>(null)
  const viewport = useRef<Viewport>(null)
  const onInit = useCallback((instance: GraphInstance) => {
    flow.current = instance
  }, [])
  const [selectedNode, setSelectedNode] = useState<Node | null>(null)
  const completeCourse = useEffectEvent((event: Event) => {
    const node = nodes.find(
      (n) =>
        n.data.contentType === "course" &&
        n.data.contentId ===
          (event as CustomEvent<{ courseId: number }>).detail.courseId,
    )
    if (!node) {
      return
    }
    const newNode = {
      ...node,
      data: {
        ...node.data,
        completed: true,
      },
    }
    updateNode(newNode)
    setSelectedNode(newNode)
  })

  useEffect(() => {
    element.addEventListener("course:completed", completeCourse)

    return () => {
      element.removeEventListener("course:completed", completeCourse)
    }
  }, [element])

  const onNodeClick: NodeMouseHandler<Node> = (_e, node) => {
    const isMobile = window.innerWidth < 769
    const offset = node.type === "formation" ? 170 : 0
    const zoom = 1.5
    viewport.current = flow.current?.getViewport() ?? null
    flow.current?.setCenter(
      node.position.x + (isMobile ? 0 : offset / zoom),
      node.position.y + (window.innerHeight / 2 - 200) / zoom,
      {
        duration,
        zoom: zoom,
      },
    )
    updateNode({
      ...node,
      selected: true,
    })
    setSelectedNode(node)
  }
  const handleDismiss = () => {
    if (!selectedNode) {
      return
    }
    setSelectedNode(null)
    updateNode({
      ...selectedNode,
      selected: false,
    })
    if (viewport.current) {
      flow.current?.setViewport(viewport.current, { duration })
    }
  }
  return (
    <QueryClientProvider client={queryClient}>
      <ReactFlow
        connectionMode={ConnectionMode.Loose}
        maxZoom={2}
        minZoom={0.8}
        onNodeClick={onNodeClick}
        nodes={nodes}
        edges={edges.map((edge) => ({
          ...edge,
          selectable: false,
          focusable: false,
        }))}
        nodeTypes={nodeTypes}
        edgeTypes={edgeTypes}
        fitView
        fitViewOptions={fitViewOptions}
        onInit={onInit}
        className="group"
        data-focus={Boolean(selectedNode)}
        onPaneClick={handleDismiss}
      >
        <IsoBackground id="2" gap={snapGrid} />
      </ReactFlow>
      {selectedNode && (
        <NodeDetail node={selectedNode} onClose={handleDismiss} />
      )}
    </QueryClientProvider>
  )
}
