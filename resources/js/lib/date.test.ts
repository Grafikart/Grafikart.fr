import { describe, expect, it } from "vitest"
import { formatDuration } from "./date"

describe("formatDuration", () => {
  it.each([
    [0, "0 min"],
    [60, "1 min"],
    [180, "3 min"],
    [600, "10 min"],
    [65, "1 min"],
    [3600, "1h"],
    [7200, "2h"],
    [3660, "1h01"],
    [3665, "1h01"],
    [5400, "1h30"],
    [8100, "2h15"],
  ])("formats %i seconds as %s", (seconds, expected) => {
    expect(formatDuration(seconds)).toBe(expected)
  })
})
