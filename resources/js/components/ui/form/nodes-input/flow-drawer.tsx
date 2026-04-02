import { Card, CardContent } from "@/components/ui/card.tsx"
import { Input } from "@/components/ui/input.tsx"
import { NativeSelect } from "@/components/ui/native-select.tsx"
import type { Node } from "../../../flow/types.ts"

const contentTypes = [
  { value: "course", label: "Cours" },
  { value: "formation", label: "Formation" },
  { value: "gate", label: "Portail" },
  { value: "fork", label: "Rond point" },
] as const

const typesWithoutContent = ["gate", "fork"]

type Props = {
  node: Node
  onChange: (n: { id: string } & Partial<Node>) => void
}

/**
 * Drawer to edit a node in the flow chart
 */
export function FlowDrawer(props: Props) {
  const changeHandler = (key: string) => (value: string | null) => {
    props.onChange({
      id: props.node.id,
      type: key === "contentType" && value ? value : props.node.type,
      data: {
        ...props.node.data,
        [key]: value,
      },
    })
  }

  const changeMetaHandler = (key: string) => (value: string | null) => {
    props.onChange({
      id: props.node.id,
      data: {
        ...props.node.data,
        // @ts-expect-error too dynamic
        meta: {
          ...props.node.data.meta,
          [key]: value?.trim() ? value : null,
        },
      },
    })
  }

  const hasContent = !typesWithoutContent.includes(props.node.data.contentType)
  const isFork = props.node.data.contentType === "fork"

  return (
    <Card className="absolute right-2 z-3 top-2">
      <CardContent className="flex flex-col gap-2">
        <div className="flex gap-2">
          <Input
            type="text"
            placeholder="Titre"
            value={props.node.data.title?.toString()}
            onValueChange={changeHandler("title")}
          />
          {hasContent && (
            <Input
              type="text"
              className="w-15 flex-none"
              placeholder="ID du contenu"
              value={props.node.data.contentId?.toString()}
              onValueChange={changeHandler("contentId")}
            />
          )}
          <NativeSelect
            className="w-30 flex-none"
            value={props.node.data.contentType}
            onValueChange={changeHandler("contentType")}
          >
            {contentTypes.map((ct) => (
              <option key={ct.value} value={ct.value}>
                {ct.label}
              </option>
            ))}
          </NativeSelect>
          <Input
            type="text"
            placeholder="Icône"
            className="w-30 flex-none"
            value={props.node.data.icon?.toString()}
            onValueChange={changeHandler("icon")}
          />
        </div>
        {isFork && (
          <Input
            type="text"
            placeholder="Vidéo"
            className="w-full flex-none"
            value={props.node.data.meta?.video?.toString() ?? ""}
            onValueChange={changeMetaHandler("video")}
          />
        )}
      </CardContent>
    </Card>
  )
}
