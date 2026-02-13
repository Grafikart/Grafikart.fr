import type { ReactFlowInstance } from "@xyflow/react"
import type { PathNodeData } from "@/types"

export type Node = {
  id: string
  type: string
  selected?: boolean
  data: Omit<PathNodeData, "x" | "y">
  position: {
    x: number
    y: number
  }
}

export type NewNode = {
  type: string
  data: Omit<PathNodeData, "x" | "y" | "id">
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
