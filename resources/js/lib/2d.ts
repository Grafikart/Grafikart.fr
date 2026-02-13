export type Position = { x: number; y: number }

const size = 25
const angle = Math.PI / 6
const width = size * Math.cos(angle)
const height = size * Math.sin(angle)
export const snapGrid = [width, height] as [number, number]

export function toIsometric(p: Position): Position {
  // Convert 2D coordinates to isometric projection using 30-degree angle
  // Isometric projection rotates the coordinate system by 30 degrees
  // and compresses the depth dimension
  return {
    x: (p.x - p.y) * Math.cos(Math.PI / 6),
    y: (p.x + p.y) * Math.sin(Math.PI / 6),
  }
}

export function addVec2(p1: Position, p2: Position): Position {
  return {
    x: p1.x + p2.x,
    y: p1.y + p2.y,
  }
}
