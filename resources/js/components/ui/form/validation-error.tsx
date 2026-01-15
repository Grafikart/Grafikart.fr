import { useFormError, useFormErrors } from "@/components/form.tsx";
import { FieldError } from "@/components/ui/field.tsx";
import { useEffect, useRef } from "react";
import { AlertCircleIcon } from "lucide-react";
import { Alert, AlertDescription, AlertTitle } from "@/components/ui/alert.tsx";

type Props = {
  name: string;
};

export function ValidationError(props: Props) {
  const error = useFormError(props.name);
  if (!error) {
    return null;
  }
  return <FieldError>{error}</FieldError>;
}

export function ValidationErrors() {
  const errors = useFormErrors();
  const alertRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    alertRef.current?.scrollIntoView({ behavior: "smooth" });
  }, [errors]);

  if (errors.length === 0) {
    return null;
  }

  return (
    <Alert variant="destructive" ref={alertRef} className="mb-6">
      <AlertCircleIcon />
      <AlertTitle>Certaines données ne sont pas valides</AlertTitle>
      <AlertDescription>
        <ul className="list-inside list-disc text-sm">
          {errors.map((error, index) => (
            <li key={index}>
              <strong>{error.propertyPath}</strong> : {error.title}
            </li>
          ))}
        </ul>
      </AlertDescription>
    </Alert>
  );
}
