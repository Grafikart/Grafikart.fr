import {
  CheckIcon,
  EyeIcon,
  MessageSquareIcon,
  TrashIcon,
  XIcon,
} from "lucide-react"
import route from "@/actions/App/Http/Cms/CommentController.ts"
import { Form } from "@/components/form.tsx"
import { FormField } from "@/components/form-field.tsx"
import { withLayout } from "@/components/layout.tsx"
import { Nl2br } from "@/components/nl2br.tsx"
import { PageTitle } from "@/components/page-title.tsx"
import { Button } from "@/components/ui/button.tsx"
import { ButtonLink } from "@/components/ui/button-link.tsx"
import { Pagination } from "@/components/ui/pagination.tsx"
import { useToggle } from "@/hooks/use-toggle.ts"
import { formatRelative } from "@/lib/date.ts"
import type { CommentRowData, PaginatedData } from "@/types"

type Props = {
  pagination: PaginatedData<CommentRowData>
  suspicious: boolean
}

export default withLayout<Props>(
  (props) => {
    return (
      <div className="space-y-4">
        <PageTitle>Commentaires</PageTitle>
        <div className="flex justify-between">
          <h1 className="flex items-center gap-2 font-semibold text-xl">
            {props.suspicious ? (
              <>
                <EyeIcon className="text-primary" />
                Commentaires suspects
              </>
            ) : (
              <>
                <MessageSquareIcon className="text-primary" />
                Commentaires
              </>
            )}
          </h1>
          {!props.suspicious && (
            <ButtonLink
              variant="secondary"
              href={route.index({ query: { suspicious: 1 } })}
            >
              <EyeIcon />
              Suspects
            </ButtonLink>
          )}
        </div>
        <div className="grid gap-6 divide-y *:pb-6">
          {props.pagination.data.map((item) => (
            <Item item={item} key={item.id} />
          ))}
        </div>
        <Pagination pagination={props.pagination} />
      </div>
    )
  },
  {
    breadcrumb: [{ label: "Commentaires", href: route.index() }],
  },
)

function Item({ item }: { item: CommentRowData }) {
  const [editing, toggle] = useToggle()

  if (editing) {
    return <RowForm item={item} onCancel={toggle} />
  }

  return (
    <article>
      <div className="flex items-center gap-1">
        <div className="font-medium">{item.username},</div>

        <div className="text-muted-foreground text-sm">
          {formatRelative(item.createdAt)}
        </div>
        <button
          onClick={toggle}
          className="text-muted-foreground text-sm underline"
        >
          Editer
        </button>
        <div className="ml-auto text-muted-foreground text-sm">{item.ip}</div>
        <ButtonLink
          variant="ghost"
          size="icon"
          href={route.destroy(item.id)}
          confirm="Voulez vous vraiment supprimer ce commentaire ?"
        >
          <TrashIcon />
        </ButtonLink>
      </div>
      <div className="-mt-1 mb-1 text-muted-foreground text-sm">
        {item.email}
      </div>
      <p className="text-sm">
        <Nl2br text={item.content} />
      </p>
    </article>
  )
}

function RowForm({
  item,
  onCancel,
}: {
  item: CommentRowData
  onCancel: () => void
}) {
  return (
    <Form
      {...route.update.form(item.id)}
      className="flex flex-col gap-2"
      onSuccess={onCancel}
    >
      <FormField
        autoFocus
        defaultValue={item.content}
        name="content"
        placeholder="Contenu du commentaire"
        label="Contenu"
        type="textarea"
      />
      <div className="flex justify-end gap-1">
        <Button size="icon" type="submit" variant="default">
          <CheckIcon />
        </Button>
        <Button
          onClick={onCancel}
          size="icon"
          type="button"
          variant="secondary"
        >
          <XIcon />
        </Button>
      </div>
    </Form>
  )
}
