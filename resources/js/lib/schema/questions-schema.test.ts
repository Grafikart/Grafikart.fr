import Ajv from "ajv"
import { describe, expect, it } from "vitest"
import type { QuestionData } from "@/types"
import { importSchema } from "./questions-schema"

const ajv = new Ajv({ allErrors: true, discriminator: true })
const validate = ajv.compile<QuestionData[]>(importSchema)

describe("importSchema", () => {
  it.each([
    [
      "a valid choice question",
      [
        {
          question: "What is 1+1?",
          type: "choice",
          answer: { choices: ["1", "2", "3"], answer: 1 },
        },
      ],
    ],
    [
      "a valid text question",
      [
        {
          question: "What is the capital of France?",
          type: "text",
          answer: { answer: "Paris" },
        },
      ],
    ],
    [
      "mixed question types",
      [
        {
          question: "Pick one",
          type: "choice",
          answer: { choices: ["A", "B"], answer: 0 },
        },
        {
          question: "Explain",
          type: "text",
          answer: { answer: "something" },
        },
      ],
    ],
  ])("accepts %s", (_label, data) => {
    expect(validate(data)).toBe(true)
  })

  it.each([
    ["an empty array", []],
    [
      "a question with empty text",
      [{ question: "", type: "text", answer: { answer: "ok" } }],
    ],
    [
      "a choice question with less than 2 choices",
      [
        {
          question: "Pick one",
          type: "choice",
          answer: { choices: ["only one"], answer: 0 },
        },
      ],
    ],
    [
      "a choice question with a negative answer index",
      [
        {
          question: "Pick one",
          type: "choice",
          answer: { choices: ["A", "B"], answer: -1 },
        },
      ],
    ],
    [
      "a choice question with a string answer index",
      [
        {
          question: "Pick one",
          type: "choice",
          answer: { choices: ["A", "B"], answer: "0" },
        },
      ],
    ],
    [
      "a choice question missing choices",
      [{ question: "Pick one", type: "choice", answer: { answer: 0 } }],
    ],
    [
      "a text question with a numeric answer",
      [{ question: "Explain", type: "text", answer: { answer: 42 } }],
    ],
    [
      "an unknown type",
      [{ question: "Something", type: "unknown", answer: { answer: "ok" } }],
    ],
    ["a missing type field", [{ question: "No type" }]],
    ["a missing question field", [{ type: "text" }]],
    [
      "additional properties on the question",
      [
        {
          question: "Valid",
          type: "text",
          answer: { answer: "ok" },
          extra: true,
        },
      ],
    ],
    [
      "additional properties on the answer",
      [
        {
          question: "Valid",
          type: "text",
          answer: { answer: "ok", extra: true },
        },
      ],
    ],
  ])("rejects %s", (_label, data) => {
    expect(validate(data)).toBe(false)
  })
})
