import {
  CheckCircle2Icon,
  CircleXIcon,
  GraduationCapIcon,
  SaveIcon,
} from "lucide-react"
import route from "@/actions/App/Http/Cms/SchoolController"
import { Form } from "@/components/form.tsx"
import { FormField } from "@/components/form-field.tsx"
import { withLayout } from "@/components/layout.tsx"
import { PageTitle } from "@/components/page-title.tsx"
import { Button } from "@/components/ui/button.tsx"
import {
  Card,
  CardContent,
  CardHeader,
  CardTitle,
} from "@/components/ui/card.tsx"
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
import { formatDate } from "@/lib/date.ts"
import type { SchoolFormData } from "@/types"
import { UserSelector } from "@/components/ui/form/user-selector.tsx"

type Props = {
  item: SchoolFormData
}

export default withLayout<Props>(
  ({ item }) => {
    const formAction = item.id ? route.update.form(item.id) : route.store.form()
    const title = item.id ? "Éditer l'école" : "Nouvelle école"

    return (
      <div className="space-y-6">
        <PageTitle>{title}</PageTitle>
        <h1 className="flex items-center gap-2 font-semibold text-xl">
          <GraduationCapIcon className="text-primary" />
          {title}
        </h1>

        <Form id="form" className="space-y-6" {...formAction}>
          <div className="grid gap-4 xl:grid-cols-4">
            <FormField label="Name" name="name" defaultValue={item.name} />
            <FormField label="Owner" name="userId">
              <UserSelector
                name="userId"
                defaultValue={item.owner?.id}
                defaultName={item.owner?.name}
                className="w-full"
              >
                <NativeSelectOption value="">Sélectionner</NativeSelectOption>
              </UserSelector>
            </FormField>
            <FormField
              label="Coupon prefix"
              name="couponPrefix"
              defaultValue={item.couponPrefix}
            />
            <FormField
              label="Credits"
              name="credits"
              type="number"
              defaultValue={item.credits}
            />
          </div>

          <FormField
            label="Email subject"
            name="emailSubject"
            defaultValue={item.emailSubject}
          />

          <div className="space-y-2">
            <FormField
              label="Email message"
              name="emailMessage"
              type="textarea"
              defaultValue={item.emailMessage}
              className="min-h-40"
            />
            <p className="text-muted-foreground text-sm">
              Ce message apparaîtra au dessus dans l'email envoyé aux élèves
              lors de leur inscription sur le site.
            </p>
          </div>

          <h1 className="flex items-center gap-2 font-semibold text-xl">
            <GraduationCapIcon className="text-primary" />
            Étudiants
          </h1>

          {item.id && (
            <div>
              {item.students.length > 0 ? (
                <Table>
                  <TableHeader>
                    <TableRow>
                      <TableHead className="w-24">ID</TableHead>
                      <TableHead>Pseudo</TableHead>
                      <TableHead>Email</TableHead>
                      <TableHead>Inscription</TableHead>
                    </TableRow>
                  </TableHeader>
                  <TableBody>
                    {item.students.map((student) => (
                      <TableRow key={student.id}>
                        <TableCell className="text-muted-foreground">
                          {student.id}
                        </TableCell>
                        <TableCell>{student.username}</TableCell>
                        <TableCell>{student.email}</TableCell>
                        <TableCell>{formatDate(student.createdAt)}</TableCell>
                      </TableRow>
                    ))}
                  </TableBody>
                </Table>
              ) : (
                <p className="text-muted-foreground text-sm">
                  Aucun étudiant n'est encore rattaché à cette école.
                </p>
              )}
            </div>
          )}
        </Form>
      </div>
    )
  },
  {
    breadcrumb: (props) => [
      { label: "Écoles", href: route.index() },
      { label: props.item.id ? "Éditer l'école" : "Nouvelle école" },
    ],
    top: (
      <Button form="form" type="submit">
        <SaveIcon /> Enregistrer
      </Button>
    ),
  },
)
