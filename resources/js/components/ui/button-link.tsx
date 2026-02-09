import type {UrlMethodPair} from "@inertiajs/core";
import {Link} from "@inertiajs/react";
import type {ComponentProps} from "react";

import {Button} from "@/components/ui/button.tsx";

function ButtonLink({
  href,
  confirm,
  ...props
}: { href: string | UrlMethodPair; confirm?: string } & ComponentProps<typeof Button>) {
  return (
    <Button
      {...props}
      render={<Link href={href} onBefore={confirm ? () => window.confirm(confirm) : undefined} />}
      nativeButton={false}
    />
  );
}

export {ButtonLink};
