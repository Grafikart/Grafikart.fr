import {
  addEdge,
  type OnConnect,
  useEdgesState,
  useNodesState,
} from "@xyflow/react"
import { createContext, useCallback, useMemo } from "react"
import type { Edge, NewNode, Node } from "@/components/flow/types.ts"
import type { PathFormData, PathNodeData } from "@/types"

const valueToFlow = (
  nodes: PathFormData["nodes"],
  completedIds: number[] = [],
): { edges: Edge[]; nodes: Node[] } => {
  const completedIdsSet = new Set(completedIds)
  const edges = [] as Edge[]
  for (const node of nodes) {
    for (const parent of node.parents) {
      edges.push({
        id: `${parent.id.toString()}-${node.id?.toString()}`,
        source: parent.id.toString(),
        target: node.id!.toString(),
        type: parent.primary ? "primary" : "secondary",
      })
    }
  }

  const flowNodes = nodes.map(
    (node) =>
      ({
        id: node.id?.toString() ?? "",
        type: node.contentType,
        position: { x: node.x, y: node.y },
        data: {
          ...node,
          completed: completedIdsSet.has(node.id),
        },
      }) satisfies Node,
  )

  return {
    edges: edges,
    nodes: flowNodes,
  }
}

const flowToValue = (nodes: Node[], edges: Edge[]): PathNodeData[] => {
  const paths = new Map(
    nodes.map((node) => {
      const { completed: _completed, ...data } = node.data

      return [
        node.id,
        {
          ...data,
          ...node.position,
          parents: [] as { id: number; primary: boolean }[],
        } satisfies PathNodeData,
      ]
    }),
  )

  for (const edge of edges) {
    paths.get(edge.target)?.parents?.push({
      id: parseInt(edge.source, 10),
      primary: edge.type === "primary",
    })
  }

  return Array.from(paths.values())
}

const empty = [] as number[]

/**
 * Custom hook that manages the state of a graph, including nodes, edges, and interactions between them.
 */
export const useGraph = (
  initial: PathNodeData[],
  completedIds: number[] = empty,
) => {
  const initialFlow = useMemo(
    () => valueToFlow(initial, completedIds),
    [completedIds, initial],
  )
  const [nodes, setNodes, onNodesChange] = useNodesState(initialFlow.nodes)
  const [edges, setEdges, onEdgesChange] = useEdgesState(initialFlow.edges)

  // Update nodes when the data changes
  const setValue = useCallback(
    (v: PathNodeData[]) => {
      const flow = valueToFlow(v, completedIds)
      setNodes(flow.nodes)
      setEdges(flow.edges)
    },
    [completedIds, setNodes, setEdges],
  )

  const onConnect: OnConnect = useCallback(
    (params) =>
      setEdges((edges) => addEdge({ ...params, type: "primary" }, edges)),
    [setEdges],
  )
  const addNode = useCallback(
    (node: NewNode): Node => {
      const id = Date.now() * -1
      const newNode = {
        ...node,
        id: id.toString(),
        origin: [0.5, 0.5],
        data: { ...node.data, id },
      }
      setNodes((nodes) => [...nodes, newNode])
      return newNode
    },
    [setNodes],
  )
  const updateNode = useCallback(
    (node: { id: string } & Partial<Node>) => {
      setNodes((nodes) =>
        nodes.map((n) =>
          n.id === node.id
            ? {
                ...n,
                ...node,
              }
            : n,
        ),
      )
    },
    [setNodes],
  )
  const updateEdge = useCallback(
    (edge: { id: string } & Partial<Edge>) => {
      setEdges((edges) => {
        return edges.map((e) =>
          e.id === edge.id
            ? {
                ...e,
                ...edge,
              }
            : e,
        )
      })
    },
    [setEdges],
  )

  return {
    nodes,
    edges,
    onNodesChange,
    onEdgesChange,
    onConnect,
    updateNode,
    updateEdge,
    addNode,
    value: flowToValue(nodes, edges),
    setValue,
  }
}

export const GraphContext = createContext<
  Pick<ReturnType<typeof useGraph>, "updateNode">
>({ updateNode: () => {} })
