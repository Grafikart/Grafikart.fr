import { router } from "@inertiajs/react"
import { HandCoinsIcon } from "lucide-react"
import route from "@/actions/App/Http/Cms/TransactionController.ts"
import TransactionController from "@/actions/App/Http/Cms/TransactionController.ts"
import { withLayout } from "@/components/layout.tsx"
import { PageTitle } from "@/components/page-title.tsx"
import { Badge } from "@/components/ui/badge.tsx"
import { ButtonLink } from "@/components/ui/button-link.tsx"
import {
  NativeSelect,
  NativeSelectOption,
} from "@/components/ui/native-select.tsx"
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@/components/ui/table.tsx"
import type { TransactionReportRowData } from "@/types"
import { useEffect } from "react"

const FIRST_YEAR = 2020

type Props = {
  items: TransactionReportRowData[]
  year: number
}

export default withLayout<Props>(
  (props) => {
    // Force all the CSS to media "screen" to make the page printable
    useEffect(() => {
      document
        .querySelectorAll('link[rel="stylesheet"], style')
        .forEach((link) => {
          if (!link.getAttribute("media")) {
            link.setAttribute("media", "screen")
          }
        })
    }, [])

    const currentYear = new Date().getFullYear()
    const years = Array.from(
      { length: currentYear - FIRST_YEAR + 1 },
      (_, i) => currentYear - i,
    )

    return (
      <div className="space-y-4">
        <PageTitle>Rapports financiers</PageTitle>
        <div className="flex justify-between">
          <h1 className="flex items-center gap-2 font-semibold text-xl">
            <HandCoinsIcon className="text-primary" />
            Rapports financiers
          </h1>
          <div className="flex items-center gap-2">
            <NativeSelect
              value={props.year}
              onValueChange={(value) =>
                router.get(TransactionController.report.url(), { year: value })
              }
            >
              {years.map((y) => (
                <NativeSelectOption key={y} value={y}>
                  {y}
                </NativeSelectOption>
              ))}
            </NativeSelect>
            <ButtonLink
              href={TransactionController.index.url()}
              variant="secondary"
            >
              Détails des transactions
            </ButtonLink>
          </div>
        </div>
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead>Mois</TableHead>
              <TableHead>Montant TTC</TableHead>
              <TableHead>TVA</TableHead>
              <TableHead>Frais</TableHead>
              <TableHead>Revenues - Frais</TableHead>
              <TableHead className="w-20">Méthode</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            {props.items.map((item) => (
              <Item item={item} key={item.month + item.method} />
            ))}
          </TableBody>
        </Table>
      </div>
    )
  },
  {
    breadcrumb: [{ label: "Transactions", href: route.index() }],
  },
)

function Item({ item }: { item: TransactionReportRowData }) {
  return (
    <TableRow className="group">
      <TableCell className="w-10">{item.month}</TableCell>
      <TableCell>{(item.price / 100).toFixed(2)} €</TableCell>
      <TableCell>{(item.tax / 100).toFixed(2)} €</TableCell>
      <TableCell>{(item.fee / 100).toFixed(2)} €</TableCell>
      <TableCell>{((item.price - item.fee) / 100).toFixed(2)} €</TableCell>
      <TableCell>
        <Badge variant={item.method === "stripe" ? "default" : "secondary"}>
          {item.method}
        </Badge>
      </TableCell>
    </TableRow>
  )
}
