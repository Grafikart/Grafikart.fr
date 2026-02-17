import { mergeProps } from "@base-ui/react/merge-props"
import { useRender } from "@base-ui/react/use-render"
import type { ReactNode } from "react"
import { useFormError } from "@/components/form.tsx"
import { Field, FieldLabel } from "@/components/ui/field.tsx"
import { ValidationError } from "@/components/ui/form/validation-error.tsx"
import { Input } from "@/components/ui/input.tsx"
import { Textarea } from "@/components/ui/textarea.tsx"

type Props = useRender.ComponentProps<"input"> & {
  label?: string
  name: string
  children?: ReactNode
  right?: ReactNode
  onValueChange?: (s: string) => void
}

export function FormField(props: Props) {
  const { render, label, ...otherProps } = props
  const error = useFormError(props.name)
  const children =
    props.children ??
    // biome-ignore lint/correctness/useHookAtTopLevel: intentional conditional hook usage with base-ui
    useRender({
      render: render ?? fieldFor(props.type),
      props: mergeProps<"input">(
        {
          id: props.name,
          name: props.name,
          "aria-invalid": Boolean(error),
        },
        otherProps,
      ),
    })

  return (
    <Field className="group/field">
      {label && (
        <div className="flex items-center justify-between">
          <FieldLabel htmlFor={props.name}>{label}</FieldLabel>
          {props.right}
        </div>
      )}
      {children}
      <ValidationError name={props.name} />
    </Field>
  )
}

function fieldFor(s?: string) {
  if (s === "textarea") {
    return <Textarea />
  }
  return <Input />
}
