import { type ComponentProps, createContext, type FormEventHandler, useContext, useMemo, useState } from "react";
import type { Violation } from "@/types";
import { toast } from "sonner";
import { Form as InertiaForm, router } from "@inertiajs/react";
import { ValidationErrors } from "@/components/ui/form/validation-error.tsx";
import { useMutation } from "@tanstack/react-query";
import { useApiMutation } from "@/hooks/use-api-fetch.ts";

type Props = Omit<ComponentProps<"form">, "action"> & { action?: string };

const FormContext = createContext({
  errors: [] as Violation[],
  fetching: false,
});

export function Form(props: Props) {
  const { mutate, errors, isPending } = useApiMutation(props.action ?? window.location.href, {
    method: props.method ?? "post",
  });

  const onSubmit: FormEventHandler<HTMLFormElement> = async (e) => {
    e.preventDefault();
    mutate(new FormData(e.currentTarget), {
      onSuccess: () => toast.success("Les données ont bien été enregistré"),
    });
  };

  const contextValue = useMemo(
    () => ({
      errors: errors ?? [],
      fetching: isPending,
    }),
    [errors, isPending],
  );

  return (
    <FormContext value={contextValue}>
      <ValidationErrors />
      <form {...props} onSubmit={onSubmit} encType="multipart/form-data" />
    </FormContext>
  );
}

export function useFormError(name: string): string | null {
  const errors = useContext(FormContext).errors;
  return errors.find((error) => error.propertyPath === name)?.title ?? null;
}

export function useFormErrors() {
  return useContext(FormContext).errors;
}

export function useFormFetching(): boolean {
  return useContext(FormContext).fetching;
}
