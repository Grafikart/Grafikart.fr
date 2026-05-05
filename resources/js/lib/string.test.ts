import { describe, expect, it } from "vitest"
import { trimCharacter } from "./string"

describe("trimCharacter", () => {
  it.each([
    ["/foo/bar/", "/", "foo/bar"],
    ["///foo///", "/", "foo"],
    ["foo", "/", "foo"],
    ["/", "/", ""],
    ["", "/", ""],
    ["...hello...", ".", "hello"],
    ["aXXXa", "a", "XXX"],
  ])("trims %j with char %j → %j", (str, c, expected) => {
    expect(trimCharacter(str, c)).toBe(expected)
  })
})
