import { useReactFlow, useStore } from "@xyflow/react"
import { type PointerEvent, useRef } from "react"
import { getSvgPathFromStroke } from "@/lib/svg.ts"

type Point = [number, number]

/**
 * Freehand lasso selection tool rendered as a canvas overlay on top of the flow.
 *
 * Drawing: pointer events are captured and converted to canvas-relative coordinates,
 * then rendered as a filled SVG stroke path on a <canvas>.
 *
 * Selection: on each pointer move, every node's four corners (in flow-space) are
 * converted to canvas-space and tested against the drawn path using `isPointInPath`.
 * - partial=true  → a node is selected if ANY corner falls inside the lasso
 * - partial=false → a node is selected only if ALL corners fall inside the lasso
 */
export function Lasso({ partial }: { partial: boolean }) {
  const { flowToScreenPosition, setNodes } = useReactFlow()
  const { width, height, nodeLookup } = useStore((state) => ({
    width: state.width,
    height: state.height,
    nodeLookup: state.nodeLookup,
  }))

  const canvasRef = useRef<HTMLCanvasElement>(null)
  const ctxRef = useRef<CanvasRenderingContext2D | null>(null)
  const canvasRect = useRef<DOMRect | null>(null)
  const lassoPoints = useRef<Point[]>([])
  // Snapshot of each node's 4 corners (in flow-space), captured once on pointer down
  const nodeCorners = useRef<Record<string, Point[]>>({})

  /** Convert a pointer event to canvas-relative coordinates */
  function toCanvasPoint(e: PointerEvent): Point {
    const rect = canvasRect.current!
    return [e.clientX - rect.left, e.clientY - rect.top]
  }

  /** Convert a flow-space position to canvas-relative coordinates */
  function toCanvasPosition(pos: { x: number; y: number }): {
    x: number
    y: number
  } {
    const screen = flowToScreenPosition(pos)
    const rect = canvasRect.current!
    return { x: screen.x - rect.left, y: screen.y - rect.top }
  }

  /** Check if a flow-space point falls inside a canvas Path2D */
  function isInsideLasso(
    ctx: CanvasRenderingContext2D,
    path: Path2D,
    point: Point,
  ): boolean {
    const { x, y } = toCanvasPosition({ x: point[0], y: point[1] })
    return ctx.isPointInPath(path, x, y)
  }

  /** Determine which nodes are inside the lasso based on their corners */
  function getSelectedNodeIds(
    ctx: CanvasRenderingContext2D,
    path: Path2D,
  ): Set<string> {
    const selected = new Set<string>()
    for (const [nodeId, corners] of Object.entries(nodeCorners.current)) {
      const isInside = partial
        ? corners.some((p) => isInsideLasso(ctx, path, p))
        : corners.every((p) => isInsideLasso(ctx, path, p))
      if (isInside) {
        selected.add(nodeId)
      }
    }
    return selected
  }

  function handlePointerDown(e: PointerEvent) {
    ;(e.target as HTMLCanvasElement).setPointerCapture(e.pointerId)
    canvasRect.current = canvasRef.current!.getBoundingClientRect()
    lassoPoints.current = [toCanvasPoint(e)]

    // Snapshot every node's 4 corners in flow-space
    nodeCorners.current = {}
    for (const node of nodeLookup.values()) {
      const { x, y } = node.internals.positionAbsolute
      const { width = 0, height = 0 } = node.measured
      nodeCorners.current[node.id] = [
        [x, y],
        [x + width, y],
        [x + width, y + height],
        [x, y + height],
      ]
    }

    ctxRef.current = canvasRef.current?.getContext("2d") ?? null
    if (!ctxRef.current) return
    ctxRef.current.lineWidth = 1
    ctxRef.current.fillStyle = "rgba(0, 89, 220, 0.08)"
    ctxRef.current.strokeStyle = "rgba(0, 89, 220, 0.8)"
  }

  function handlePointerMove(e: PointerEvent) {
    if (e.buttons !== 1 || !ctxRef.current) return

    lassoPoints.current.push(toCanvasPoint(e))
    const path = new Path2D(getSvgPathFromStroke(lassoPoints.current))

    // Redraw the lasso stroke
    ctxRef.current.clearRect(0, 0, width, height)
    ctxRef.current.fill(path)
    ctxRef.current.stroke(path)

    // Update node selection based on the lasso shape
    const selectedIds = getSelectedNodeIds(ctxRef.current, path)
    setNodes((nodes) =>
      nodes.map((node) => ({
        ...node,
        selected: selectedIds.has(node.id),
      })),
    )
  }

  function handlePointerUp(e: PointerEvent) {
    ;(e.target as HTMLCanvasElement).releasePointerCapture(e.pointerId)
    lassoPoints.current = []
    ctxRef.current?.clearRect(0, 0, width, height)
  }

  return (
    <canvas
      ref={canvasRef}
      width={width}
      height={height}
      className="tool-overlay"
      onPointerDown={handlePointerDown}
      onPointerMove={handlePointerMove}
      onPointerUp={handlePointerUp}
    />
  )
}
