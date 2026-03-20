import { ConstructionIcon, GitForkIcon } from "lucide-react"
import type { MouseEventHandler } from "react"
import { SearchCommand } from "@/components/search.tsx"
import { Button } from "@/components/ui/button.tsx"

type Props = {
  onSelect: (v: {
    name: string
    type: string
    icon?: string
    id: number | null
  }) => void
  onClose: () => void
}

/**
 * Dialog to add a new course / formation to the graph
 */
export function FlowAdd(props: Props) {
  // Dismiss the dialog on the overlay click
  const onOverlayClick: MouseEventHandler<HTMLDivElement> = (e) => {
    if (e.target === e.currentTarget) {
      props.onClose()
    }
  }
  const addCustomType = (type: string) => {
    props.onSelect({
      name: "",
      type: type,
      icon: type,
      id: null,
    })
  }
  return (
    <div
      className="absolute inset-0 bg-background/40 z-10 grid justify-center items-start"
      onClick={onOverlayClick}
    >
      <div className="flex gap-2">
        <SearchCommand onSelect={props.onSelect} />
        <Button
          onClick={() => addCustomType("fork")}
          variant="outline"
          size="icon"
          className="mt-1"
        >
          <GitForkIcon />
        </Button>
        <Button
          variant="outline"
          size="icon"
          className="mt-1"
          onClick={() => addCustomType("gate")}
        >
          <ConstructionIcon />
        </Button>
      </div>
    </div>
  )
}
