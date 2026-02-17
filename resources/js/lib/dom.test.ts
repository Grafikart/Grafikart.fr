import { describe, expect, it, vi } from "vitest"
import { formToObject } from "./dom"

function mockForm(fields: [string, string][]): HTMLFormElement {
  vi.stubGlobal(
    "FormData",
    class {
      entries() {
        return fields[Symbol.iterator]()
      }
    },
  )
  return {} as HTMLFormElement
}

describe("formToJson", () => {
  it("converts flat fields to a plain object", () => {
    const form = mockForm([
      ["name", "John"],
      ["age", "30"],
    ])
    expect(formToObject(form)).toEqual({ name: "John", age: "30" })
  })

  it("handles dot notation for nested keys", () => {
    const form = mockForm([
      ["address.city", "Paris"],
      ["address.zip", "75000"],
    ])
    expect(formToObject(form)).toEqual({
      address: { city: "Paris", zip: "75000" },
    })
  })

  it("handles deeply nested dot notation", () => {
    const form = mockForm([["a.b.c", "deep"]])
    expect(formToObject(form)).toEqual({ a: { b: { c: "deep" } } })
  })

  it("mixes flat and nested keys", () => {
    const form = mockForm([
      ["title", "Hello"],
      ["meta.description", "World"],
    ])
    expect(formToObject(form)).toEqual({
      title: "Hello",
      meta: { description: "World" },
    })
  })

  it("mixes handles array correctly", () => {
    const form = mockForm([
      ["title", "Hello"],
      ["items.0", "Mot1"],
      ["items.1", "Mot2"],
    ])
    expect(formToObject(form)).toEqual({
      title: "Hello",
      items: ["Mot1", "Mot2"],
    })
  })

  it("mixes handles array with properties", () => {
    const form = mockForm([
      ["title", "Hello"],
      ["items.0.label", "Mot1"],
      ["items.1.label", "Mot2"],
    ])
    expect(formToObject(form)).toEqual({
      title: "Hello",
      items: [{ label: "Mot1" }, { label: "Mot2" }],
    })
  })

  it("returns an empty object for an empty form", () => {
    const form = mockForm([])
    expect(formToObject(form)).toEqual({})
  })

  it("overwrites earlier value when duplicate keys exist", () => {
    const form = mockForm([
      ["name", "first"],
      ["name", "second"],
    ])
    expect(formToObject(form)).toEqual({ name: "second" })
  })
})
