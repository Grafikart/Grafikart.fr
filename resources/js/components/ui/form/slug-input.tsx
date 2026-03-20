import { LinkIcon } from "lucide-react"
import type { ChangeEvent } from "react"
import { Button } from "@/components/ui/button.tsx"
import { slugify } from "@/lib/string.ts"
import { cn } from "@/lib/utils.ts"

type SlugInputProps = {
  defaultValue: string
  prefix: string
  url?: string
  className?: string
}

export function SlugInput({
  defaultValue,
  prefix,
  url,
  className,
}: SlugInputProps) {
  const handleChange = (e: ChangeEvent<HTMLInputElement>) => {
    e.target.value = slugify(e.target.value)
  }

  return (
    <div
      className={cn(
        "flex text-sm text-muted-foreground items-center",
        className,
      )}
    >
      <span className="opacity-50">{prefix}</span>
      <input
        type="text"
        name="slug"
        placeholder="slug"
        defaultValue={defaultValue}
        onChange={handleChange}
        className="outline-none field-sizing-content min-w-10"
      />
      {url && (
        <Button
          nativeButton={false}
          variant="ghost"
          render={<a target="_blank" href={url} />}
          size="icon-xs"
        >
          <LinkIcon />
        </Button>
      )}
    </div>
  )
}
