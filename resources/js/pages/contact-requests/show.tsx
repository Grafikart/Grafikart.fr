import { MailIcon } from "lucide-react"
import { index } from "@/actions/App/Http/Cms/ContactRequestController"
import { withLayout } from "@/components/layout.tsx"
import { PageTitle } from "@/components/page-title.tsx"
import {
  Card,
  CardContent,
  CardHeader,
  CardTitle,
} from "@/components/ui/card.tsx"
import { formatDate } from "@/lib/date.ts"
import type { ContactRequestRowData } from "@/types"

type Props = {
  item: ContactRequestRowData
}

export default withLayout<Props>(
  ({ item }) => {
    return (
      <div className="space-y-6">
        <PageTitle>{`Contact de ${item.name}`}</PageTitle>
        <h1 className="flex items-center gap-2 font-semibold text-2xl">
          <MailIcon className="text-primary" />
          {item.name}
        </h1>

        <div className="grid gap-6 lg:grid-cols-[minmax(0,1fr)_280px]">
          <p className="whitespace-pre-wrap text-sm">{item.message}</p>

          <aside className="space-y-4">
            <Card>
              <CardHeader>
                <CardTitle>Informations</CardTitle>
              </CardHeader>
              <CardContent className="space-y-3 text-sm">
                <div>
                  <div className="text-muted-foreground text-xs uppercase tracking-wide">
                    Nom
                  </div>
                  <div className="font-medium">{item.name}</div>
                </div>
                <div>
                  <div className="text-muted-foreground text-xs uppercase tracking-wide">
                    Email
                  </div>
                  <a
                    href={`mailto:${item.email}`}
                    className="font-medium hover:underline"
                  >
                    {item.email}
                  </a>
                </div>
                <div>
                  <div className="text-muted-foreground text-xs uppercase tracking-wide">
                    Date
                  </div>
                  <div>{formatDate(item.createdAt)}</div>
                </div>
                {item.ip && (
                  <div>
                    <div className="text-muted-foreground text-xs uppercase tracking-wide">
                      IP
                    </div>
                    <div className="font-mono text-xs">{item.ip}</div>
                  </div>
                )}
              </CardContent>
            </Card>
          </aside>
        </div>
      </div>
    )
  },
  {
    breadcrumb: (props) => [
      { label: "Demandes de contact", href: index() },
      { label: props.item.name },
    ],
  },
)
