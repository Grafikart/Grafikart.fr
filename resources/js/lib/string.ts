export function normalizeLineEnding(s?: string): string {
  return s?.replace(/\r\n/g, "\n") ?? ""
}

export function trimCharacter(str: string, c: string): string {
  let start = 0
  let end = str.length
  while (start < end && str[start] === c) start++
  while (end > start && str[end - 1] === c) end--
  return str.slice(start, end)
}

export function slugify(text: string): string {
  return text
    .toLowerCase()
    .normalize("NFD")
    .replace(/[\u0300-\u036f]/g, "")
    .replace(/[^a-z0-9]+/g, "-")
}
