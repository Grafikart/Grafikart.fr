import { AlertCircleIcon } from "lucide-react";
import { useEffect, useRef } from "react";

import { useFormError, useFormErrors } from "@/components/form.tsx";
import { Alert, AlertDescription, AlertTitle } from "@/components/ui/alert.tsx";
import { FieldError } from "@/components/ui/field.tsx";


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

  if (Object.keys(errors).length === 0) {
    return null;
  }

  return (
    <Alert variant="destructive" ref={alertRef} className="mb-6">
      <AlertCircleIcon />
      <AlertTitle>Certaines données ne sont pas valides</AlertTitle>
      <AlertDescription>
        <ul className="list-inside list-disc text-sm">
          {Object.keys(errors).map((key) => (
            <li key={key}>
              <strong>{key}</strong> : {errors[key]}
            </li>
          ))}
        </ul>
      </AlertDescription>
    </Alert>
  );
}
