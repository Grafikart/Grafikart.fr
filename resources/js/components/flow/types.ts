import type { ReactFlowInstance } from "@xyflow/react"
import type { PathNodeData } from "@/types"

export type FlowNodeData = Omit<PathNodeData, "x" | "y"> & {
  completed?: boolean
}

export type Node = {
  id: string
  type: string
  selected?: boolean
  data: FlowNodeData
  position: {
    x: number
    y: number
  }
}

export type NewNode = {
  type: string
  data: Omit<FlowNodeData, "id">
  position: {
    x: number
    y: number
  }
}

export type Edge = {
  id: string
  source: string
  target: string
  type?: string
}

export type Graph = {
  nodes: Node[]
  edges: Edge[]
}

export type GraphInstance = ReactFlowInstance<Node, Edge>
