import { UploadIcon } from "lucide-react";
import { type ComponentProps, useState } from "react";

import { Dialog, DialogContent, DialogTrigger } from "@/components/ui/dialog.tsx";
import { FileExplorer } from "@/components/ui/form/file-explorer.tsx";
import { cn } from "@/lib/utils";
import type { AttachmentFileData } from "@/types";

type Props = {
  defaultValue?: number;
  preview?: string;
  name: string;
  attachableType?: string;
  attachableId?: number | null;
} & ComponentProps<"div">;

export function AttachmentSelector({ className, defaultValue, preview, name, attachableType, attachableId, ...props }: Props) {
  const [attachmentId, setAttachmentId] = useState(defaultValue);
  const [previewUrl, setPreviewUrl] = useState(preview);
  const onFileSelect = (file: AttachmentFileData) => {
    setPreviewUrl(file.thumbnail);
    setAttachmentId(file.id);
    setOpen(false);
  };
  const [open, setOpen] = useState(false);

  return (
    <>
      <input type="hidden" name={name} value={attachmentId} />
      <Dialog open={open} onOpenChange={setOpen}>
        <DialogTrigger
          className={cn(
            className,
            "group bg-muted relative rounded-md flex items-center justify-center cursor-pointer",
            props["aria-invalid"] && "ring-destructive/20 dark:ring-destructive/40",
            "border-destructive",
          )}
        >
          {previewUrl && (
            <img
              src={previewUrl}
              alt=""
              className="absolute inset-0 w-full h-full group-hover:opacity-20 transition-opacity object-cover"
            />
          )}
          <UploadIcon size={16} className={cn(preview && "hidden group-hover:block relative z-5")} />
        </DialogTrigger>
        <DialogContent className="p-0 max-w-300 ">
          <FileExplorer onSelect={onFileSelect} attachableType={attachableType} attachableId={attachableId} />
        </DialogContent>
      </Dialog>
    </>
  );
}
