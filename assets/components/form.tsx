import { type ComponentProps, createContext, type FormEventHandler, useContext, useState } from "react";
import type { Violation } from "@/types";
import { toast } from "sonner";

type Props = ComponentProps<"form">;

const FormContext = createContext({
  errors: [] as Violation[],
});

export function Form(props: Props) {
  const [errors, setErrors] = useState<Violation[]>([]);
  const [fetching, setFetching] = useState(false);

  const onSubmit: FormEventHandler<HTMLFormElement> = async (e) => {
    e.preventDefault();
    setFetching(true);
    try {
      const r = await fetch(formAction(props.action), {
        method: props.method ?? "post",
        body: new FormData(e.currentTarget),
        headers: {
          Accept: "application/json",
        },
      });
      // Validation errors
      if (r.status === 422) {
        const payload = (await r.json()) as { violations: Violation[] };
        setErrors(payload.violations);
        toast.error("Erreur de validation");
        return;
      }
      if (r.ok) {
        toast.success("Les données ont bien été enregistré");
      }
    } catch (e) {
    } finally {
      setFetching(false);
    }
  };
  return (
    <FormContext value={{ errors }}>
      <form {...props} onSubmit={onSubmit} encType="multipart/form-data" />
    </FormContext>
  );
}

export function useError(name: string): string | null {
  const errors = useContext(FormContext).errors;
  return errors.find((error) => error.propertyPath === name)?.title ?? null;
}

function formAction(action: unknown) {
  if (typeof action === "string") {
    return action;
  }
  return window.location.href;
}
