import { useCallback, useMemo, useState } from "react"
import type { Position } from "@/lib/2d.ts"
import type { Edge, Node } from "../../../flow/types.ts"

export function useContextMenu(nodes: Node[], edges: Edge[]) {
  const [selectedId, setId] = useState("")
  const [position, setPosition] = useState({ x: 0, y: 0 })
  return {
    node: useMemo(
      () => nodes.find((n) => n.id === selectedId),
      [selectedId, nodes],
    ),
    edge: useMemo(
      () => edges.find((e) => e.id === selectedId),
      [selectedId, edges],
    ),
    open: useCallback((id: string, p: Position) => {
      setId(id)
      setPosition(p)
    }, []),
    position,
    reset: () => setId(""),
  }
}
