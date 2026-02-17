import { CheckIcon, CopyIcon } from "lucide-react"
import { type ComponentProps, useCallback, useEffect, useState } from "react"
import { Button } from "@/components/ui/button.tsx"

type Props = Omit<ComponentProps<typeof Button>, "onClick" | "children"> & {
  text: string | (() => string)
}

export function CopyButton({ text, ...props }: Props) {
  const [copied, setCopied] = useState(false)

  const handleClick = useCallback(() => {
    const value = typeof text === "function" ? text() : text
    navigator.clipboard.writeText(value)
    setCopied(true)
  }, [text])

  useEffect(() => {
    if (!copied) {
      return
    }
    const timer = setTimeout(() => setCopied(false), 2000)
    return () => clearTimeout(timer)
  }, [copied])

  return (
    <Button
      type="button"
      variant="ghost"
      size="icon"
      {...props}
      onClick={handleClick}
    >
      {copied ? <CheckIcon className="text-success" /> : <CopyIcon />}
    </Button>
  )
}
