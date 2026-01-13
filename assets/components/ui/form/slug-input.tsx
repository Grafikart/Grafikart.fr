import { Button } from "@/components/ui/button.tsx";
import { LinkIcon } from "lucide-react";

type SlugInputProps = {
  defaultValue: string;
  prefix: string;
  url?: string;
};

export function SlugInput({ defaultValue, prefix, url }: SlugInputProps) {
  return (
    <div className="flex text-sm text-muted-foreground mb-3 items-center">
      <span className="opacity-50">{prefix}</span>
      <input
        type="text"
        name="slug"
        placeholder="slug"
        defaultValue={defaultValue}
        className="outline-none field-sizing-content min-w-10"
      />
      {url && (
        <Button nativeButton={false} variant="ghost" render={<a target="_blank" href={url} />} size="icon-xs">
          <LinkIcon />
        </Button>
      )}
    </div>
  );
}
