import { describe, expect, it, vi } from "vitest"
import { SimpleCachedValue } from "./cache"

describe("SimpleCachedValue", () => {
  it("returns cached value while key stays same", () => {
    const key = vi.fn(() => "alpha")
    const value = vi.fn(() => ({ label: "cached" }))
    const cache = new SimpleCachedValue(key, value)

    const firstValue = cache.getValue()
    const secondValue = cache.getValue()

    expect(firstValue).toBe(secondValue)
    expect(key).toHaveBeenCalledTimes(2)
    expect(value).toHaveBeenCalledTimes(1)
  })

  it("recomputes value when key changes", () => {
    const keys = ["alpha", "beta"]
    const key = vi.fn(() => keys.shift() ?? "beta")
    const value = vi.fn(() => ({ id: value.mock.calls.length + 1 }))
    const cache = new SimpleCachedValue(key, value)

    const firstValue = cache.getValue()
    const secondValue = cache.getValue()

    expect(firstValue).not.toBe(secondValue)
    expect(value).toHaveBeenCalledTimes(2)
  })

  it("keeps falsy cached values while key stays same", () => {
    const key = vi.fn(() => "alpha")
    const value = vi.fn(() => 0)
    const cache = new SimpleCachedValue(key, value)

    expect(cache.getValue()).toBe(0)
    expect(cache.getValue()).toBe(0)
    expect(value).toHaveBeenCalledTimes(1)
  })
})
