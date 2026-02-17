export function times<T>(n: number, cb: (k: number) => T): T[] {
  return Array.from({ length: n }).map((_, k) => cb(k))
}
