import { Button } from "@base-ui/react";
import type { ComponentProps } from "react";

import { useFormFetching } from "@/components/form.tsx";

type Props = ComponentProps<typeof Button>;

/**
 * Un bouton automatiquement désactivé quand le formulaire est en train d'être soumis
 */
export function FormButton(props: Props) {
  const fetching = useFormFetching();
  return <Button disabled={fetching} {...props} />;
}
