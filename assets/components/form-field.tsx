import type { ReactNode } from "react";
import { Field, FieldError, FieldLabel } from "@/components/ui/field.tsx";
import { useError } from "@/components/form.tsx";

type Props = {
  label: string;
  name: string;
  children: ReactNode;
};
export function FormField(props: Props) {
  const error = useError(props.name);
  return (
    <Field>
      <FieldLabel htmlFor={props.name}>{props.label}</FieldLabel>
      {props.children}
      {error && <FieldError>{error}</FieldError>}
    </Field>
  );
}
