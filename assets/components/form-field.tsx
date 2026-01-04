import type { ReactNode } from "react";
import { Field, FieldLabel } from "@/components/ui/field.tsx";
import { useFormError } from "@/components/form.tsx";
import { useRender } from "@base-ui/react/use-render";
import { mergeProps } from "@base-ui/react/merge-props";
import { Input } from "@/components/ui/input.tsx";
import { ValidationError } from "@/components/ui/form/validation-error.tsx";

type Props = useRender.ComponentProps<"input"> & {
  label: string;
  name: string;
  children?: ReactNode;
  right?: ReactNode;
};

export function FormField(props: Props) {
  const { render, label, ...otherProps } = props;
  const error = useFormError(props.name);
  const children =
    props.children ??
    useRender({
      render: render ?? <Input />,
      props: mergeProps<"input">({ id: props.name, name: props.name, "aria-invalid": Boolean(error) }, otherProps),
    });

  return (
    <Field className="group/field">
      <div className="flex justify-between items-center">
        <FieldLabel htmlFor={props.name}>{label}</FieldLabel>
        {props.right}
      </div>
      {children}
      <ValidationError name={props.name} />
    </Field>
  );
}
