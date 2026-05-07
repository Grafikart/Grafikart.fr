import { TriangleAlertIcon } from "lucide-react"
import LogController from "@/actions/App/Http/Cms/LogController.ts"
import { withLayout } from "@/components/layout.tsx"
import { PageTitle } from "@/components/page-title.tsx"

type Props = {
  output: string
}

export default withLayout<Props>(
  ({ output }) => {
    const groups = output
      ? [
          ...output
            .split("\n--\n")
            .map((g) => g.trim())
            .filter(Boolean),
        ].reverse()
      : []

    return (
      <div className="space-y-4">
        <PageTitle>Logs</PageTitle>
        <h1 className="flex items-center gap-2 font-semibold text-xl">
          <TriangleAlertIcon className="text-primary" />
          Erreurs récentes
        </h1>
        {groups.length === 0 ? (
          <p className="text-center text-muted-foreground py-12">
            Aucune erreur trouvée.
          </p>
        ) : (
          <div className="space-y-2">
            {groups.map((group, i) => {
              const [firstLine, ...rest] = group.split("\n")
              return (
                <details
                  key={i}
                  className="rounded-lg border bg-card overflow-hidden"
                >
                  <summary className="cursor-pointer select-none px-4 py-2 text-xs font-mono hover:bg-muted/50 transition-colors list-none">
                    {firstLine}
                  </summary>
                  {rest.length > 0 && (
                    <pre className="border-t bg-muted/50 px-4 py-3 text-xs font-mono leading-relaxed whitespace-pre-wrap overflow-x-auto">
                      {rest.join("\n")}
                    </pre>
                  )}
                </details>
              )
            })}
          </div>
        )}
      </div>
    )
  },
  {
    breadcrumb: [{ label: "Logs", href: LogController.index() }],
  },
)
