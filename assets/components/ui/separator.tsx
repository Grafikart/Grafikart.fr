import { Separator as SeparatorPrimitive } from "@base-ui/react/separator";

import { cn } from "@/lib/utils";

function Separator({ className, orientation = "horizontal", ...props }: SeparatorPrimitive.Props) {
  return (
    <SeparatorPrimitive
      orientation={orientation}
      className={cn(
        "bg-border shrink-0",
        orientation === "horizontal" && "h-px w-full",
        orientation === "vertical" && "w-px h-full",
        className,
      )}
      {...props}
    />
  );
}

export { Separator };
