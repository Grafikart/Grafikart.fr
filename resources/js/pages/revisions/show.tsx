import { BanIcon, CheckIcon } from "lucide-react"
import ReactDiffViewer from "react-diff-viewer-continued"
import { update, index } from "@/actions/App/Http/Cms/RevisionController.ts"
import { Form } from "@/components/form.tsx"
import { FormField } from "@/components/form-field.tsx"
import { withLayout } from "@/components/layout.tsx"
import { PageTitle } from "@/components/page-title.tsx"
import { Button } from "@/components/ui/button.tsx"
import type { RevisionShowData } from "@/types"

type Props = {
  revision: RevisionShowData
}

export default withLayout<Props>(
  ({ revision }) => {
    const isPending = revision.state === 0

    return (
      <div className="space-y-6">
        <PageTitle>{`Révision #${revision.id}`}</PageTitle>

        {isPending && (
          <Form
            {...update.post(revision.id)}
            className="flex gap-2 items-center"
          >
            <FormField
              name="comment"
              className="w-full"
              placeholder="Raison du refus"
            />
            <Button type="submit" value="-1" name="state" variant="destructive">
              <BanIcon />
              Rejeter
            </Button>
            <Button type="submit" value="1" name="state">
              <CheckIcon />
              Accepter
            </Button>
          </Form>
        )}

        <ReactDiffViewer
          oldValue={revision.currentContent}
          newValue={revision.content}
          splitView
          leftTitle="Contenu actuel"
          rightTitle="Proposition"
        />
      </div>
    )
  },
  {
    breadcrumb: (props) => [
      { label: "Révisions", href: index() },
      { label: `Révision #${props.revision.id}` },
    ],
  },
)
