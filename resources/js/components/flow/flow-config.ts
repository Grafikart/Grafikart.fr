import { FlowEdge } from "@/components/flow/flow-edge.tsx"
import { FlowNode } from "@/components/flow/flow-node.tsx"
import { FlowNodeFork } from "@/components/flow/flow-node-fork.tsx"
import { FlowNodeGate } from "@/components/flow/flow-node-gate.tsx"

export const nodeTypes = {
  default: FlowNode,
  course: FlowNode,
  formation: FlowNode,
  fork: FlowNodeFork,
  gate: FlowNodeGate,
}

export const edgeTypes = {
  default: FlowEdge,
  primary: FlowEdge,
  secondary: FlowEdge,
}
