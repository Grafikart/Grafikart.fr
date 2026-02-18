import { type ComponentProps, useEffect, useId, useRef, useState } from "react"
import { cn } from "@/lib/utils.ts"

type WobblyDialogProps = {
  name: string
  content: string
} & ComponentProps<"div">

export function WobblyDialog({ name, content, ...props }: WobblyDialogProps) {
  const filterId = useId()
  const wrapperRef = useRef<HTMLDivElement>(null)
  const [displayedText, setDisplayedText] = useState("")

  useEffect(() => {
    let index = 0
    const interval = setInterval(() => {
      index++
      if (index > content.length) {
        clearInterval(interval)
        return
      }
      setDisplayedText(content.slice(0, index))
    }, 25)
    return () => clearInterval(interval)
  }, [content])

  return (
    <div
      {...props}
      className={cn("relative", props.className)}
      ref={wrapperRef}
    >
      <svg
        xmlns="http://www.w3.org/2000/svg"
        version="1.1"
        style={{ position: "absolute", width: 0, height: 0 }}
      >
        <defs>
          <filter id={filterId}>
            <feGaussianBlur
              in="SourceGraphic"
              stdDeviation="10"
              result="blur"
            />
            <feColorMatrix
              in="blur"
              mode="matrix"
              values="1 0 0 0 0  0 1 0 0 0  0 0 1 0 0  0 0 0 19 -9"
              result="goo"
            />
            <feComposite in="SourceGraphic" in2="goo" operator="atop" />
          </filter>
        </defs>
      </svg>

      <div
        className="wobbly-dialog-blobs"
        style={{ filter: `url(#${CSS.escape(filterId)})` }}
      >
        <div className="wobbly-dialog-blob-top" />
        <div className="wobbly-dialog-blob-bottom" />
        <div className="absolute w-full p-8 text-2xl text-foreground">
          {displayedText}
        </div>
      </div>

      <div className="wobbly-dialog-character-wrap">
        <div className="wobbly-dialog-character">{name}</div>
      </div>
    </div>
  )
}
