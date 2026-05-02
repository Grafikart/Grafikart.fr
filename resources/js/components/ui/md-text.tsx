import ReactMarkdown from "react-markdown"

const INLINE_ELEMENTS = ["strong", "em", "code", "a", "del", "s", "pre"]

type Props = {
  inline?: boolean
  text?: string
}

export function MDText({ text, inline }: Props) {
  return (
    <ReactMarkdown
      allowedElements={inline ? INLINE_ELEMENTS : undefined}
      children={text}
      unwrapDisallowed
    />
  )
}
