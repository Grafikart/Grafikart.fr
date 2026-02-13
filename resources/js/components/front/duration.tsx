import { formatDuration } from "@/lib/date"
import { cn } from "@/lib/utils"

type DurationProps = {
  duration: number
  className?: string
}

export function Duration({ duration, className }: DurationProps) {
  return <span className={cn(className)}>{formatDuration(duration)}</span>
}
