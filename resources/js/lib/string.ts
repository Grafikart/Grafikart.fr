export function normalizeLineEnding(s?: string): string {
  return s?.replace(/\r\n/g, "\n") ?? ""
}

export function slugify(text: string): string {
  return text
    .toLowerCase()
    .normalize("NFD")
    .replace(/[\u0300-\u036f]/g, "")
    .replace(/[^a-z0-9]+/g, "-")
}
