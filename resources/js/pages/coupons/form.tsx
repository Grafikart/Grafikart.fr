import { SaveIcon, TicketIcon } from "lucide-react"
import route from "@/actions/App/Http/Cms/CouponController"
import { Form } from "@/components/form.tsx"
import { FormField } from "@/components/form-field.tsx"
import { withLayout } from "@/components/layout.tsx"
import { PageTitle } from "@/components/page-title.tsx"
import { Button } from "@/components/ui/button.tsx"
import type { CouponFormData } from "@/types"

type Props = {
  item: CouponFormData
}

export default withLayout<Props>(
  ({ item }) => {
    const formAction = item.id ? route.update.form(item.id) : route.store.form()
    const title = item.id ? "Éditer le coupon" : "Nouveau coupon"

    return (
      <div className="space-y-6">
        <PageTitle>{title}</PageTitle>
        <h1 className="flex items-center gap-2 font-semibold text-xl">
          <TicketIcon className="text-primary" />
          {title}
        </h1>

        <Form id="form" {...formAction}>
          <div className="grid grid-cols-2 gap-4 max-w-100">
            {" "}
            <FormField label="Code" name="id" defaultValue={item.id} />
            <FormField
              label="Mois"
              name="months"
              type="number"
              defaultValue={item.months}
            />
          </div>{" "}
        </Form>
      </div>
    )
  },
  {
    breadcrumb: (props) => [
      { label: "Coupons", href: route.index() },
      { label: props.item.id || "Nouveau coupon" },
    ],
    top: (
      <Button form="form" type="submit">
        <SaveIcon /> Enregistrer
      </Button>
    ),
  },
)
