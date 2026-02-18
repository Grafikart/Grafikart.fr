export const importSchema = {
  type: "object",
  required: ["questions"],
  properties: {
    questions: {
      type: "array",
      items: {
        type: "object",
        required: ["question", "type", "answer"],
        discriminator: { propertyName: "type" },
        oneOf: [
          {
            properties: {
              question: { type: "string", minLength: 2 },
              type: { const: "choice" },
              answer: {
                type: "object",
                required: ["choices", "answer"],
                additionalProperties: false,
                properties: {
                  choices: {
                    type: "array",
                    items: { type: "string" },
                    minItems: 2,
                  },
                  answer: { type: "integer", minimum: 0 },
                },
              },
            },
            additionalProperties: false,
          },
          {
            properties: {
              question: { type: "string", minLength: 2 },
              type: { const: "text" },
              answer: {
                type: "object",
                required: ["answer"],
                additionalProperties: false,
                properties: {
                  answer: { type: "string" },
                },
              },
            },
            additionalProperties: false,
          },
        ],
      },
      minItems: 1,
    },
  },
} as const
