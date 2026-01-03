import { cn } from "@/lib/utils";
import { UploadIcon } from "lucide-react";
import { type ChangeEventHandler, type ComponentProps, useState } from "react";

function ImageInput({ className, defaultValue, ...props }: ComponentProps<"input">) {
  const [preview, setPreview] = useState(defaultValue as string);
  const handleChange: ChangeEventHandler<HTMLInputElement> = (event) => {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files.length > 0) {
      const file = target.files[0];
      console.log(target, URL.createObjectURL(file));
      setPreview(URL.createObjectURL(file));
    }
  };

  return (
    <div
      className={cn(
        className,
        "group bg-muted relative rounded-md flex items-center justify-center cursor-pointer",
        props["aria-invalid"] && "ring-destructive/20 dark:ring-destructive/40",
        "border-destructive",
      )}
    >
      <input
        onChange={handleChange}
        type="file"
        className={cn("absolute inset-0 opacity-0 cursor-pointer z-10")}
        {...props}
      />
      {preview && (
        <img
          src={preview}
          alt=""
          className="absolute inset-0 w-full h-full object-cover group-hover:opacity-20 transition-opacity"
        />
      )}
      <UploadIcon size={16} className={cn(preview && "hidden group-hover:block relative z-5")} />
    </div>
  );
}

export { ImageInput };
