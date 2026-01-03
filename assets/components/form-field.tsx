import type { ReactNode } from "react";
import { Field, FieldError, FieldLabel } from "@/components/ui/field.tsx";
import { useError } from "@/components/form.tsx";
import { useRender } from "@base-ui/react/use-render";
import { mergeProps } from "@base-ui/react/merge-props";

type Props = useRender.ComponentProps<"input"> & {
  label: string;
  name: string;
  children?: ReactNode;
};

export function FormField(props: Props) {
  const { render, label, ...otherProps } = props;
  const error = useError(props.name);
  const children =
    props.children ??
    useRender({
      defaultTagName: "input",
      render,
      props: mergeProps<"input">({ id: props.name, name: props.name }, otherProps),
    });

  return (
    <Field>
      <FieldLabel htmlFor={props.name}>{label}</FieldLabel>
      {children}
      {error && <FieldError>{error}</FieldError>}
    </Field>
  );
}
