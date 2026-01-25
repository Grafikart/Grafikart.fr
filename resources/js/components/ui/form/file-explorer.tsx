import { FolderIcon, FolderOpenIcon, SearchIcon, TrashIcon } from "lucide-react";
import { useCallback, useState } from "react";
import { useDropzone } from "react-dropzone";
import { toast } from "sonner";

import route from '@/actions/App/Http/Cms/AttachmentController'
import { Badge } from "@/components/ui/badge.tsx";
import { Button } from "@/components/ui/button.tsx";
import { InputGroup, InputGroupAddon, InputGroupInput } from "@/components/ui/input-group.tsx";
import { Input } from "@/components/ui/input.tsx";
import { Separator } from "@/components/ui/separator.tsx";
import { Spinner } from "@/components/ui/spinner.tsx";
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table.tsx";
import { useApiFetch, useApiMutation } from "@/hooks/use-api-fetch.ts";
import { humanSize } from "@/lib/file.ts";
import { cn } from "@/lib/utils.ts";
import type { AttachmentFileData, FolderData } from "@/types";

type Props = {
  onSelect: (file: AttachmentFileData) => void;
  attachableType?: string;
  attachableId?: number;
};

export function FileExplorer(props: Props) {
  const { data: folders } = useApiFetch<FolderData[]>(route.folders().url);
  const [folder, setFolder] = useState("");

  const { data, setData } = useApiFetch<AttachmentFileData[]>(route.index({
    query: { path: folder },
  }).url);
  const { mutate, isPending } = useApiMutation<AttachmentFileData>(route.store().url);

  const onDrop = useCallback((files: File[]) => {
    for (const file of files) {
      const data = new FormData();
      data.set("file", file);
      if (props.attachableType) {
        data.set("attachableType", props.attachableType);
      }
      if (props.attachableId) {
        data.set("attachableId", props.attachableId.toString());
      }
      mutate(data, {
        onSuccess: (newFile) => {
          setData((files) => [newFile, ...files]);
        },
      });
    }
  }, [mutate, setData, props.attachableType, props.attachableId]);
  const { getRootProps, isDragActive, getInputProps } = useDropzone({ onDrop, noClick: true });

  const files = data ?? [];

  return (
    <div className="grid grid-cols-[300px_1fr] relative" {...getRootProps()}>
      <div
        className={cn(
          "absolute inset-0 border-2 rounded-lg border-primary bg-primary/10 z-20 place-items-center text-2xl font-semibold text-primary border-dashed hidden",
          isDragActive && "grid",
        )}
      >
        Déposer ici
      </div>
      <aside className="space-y-4 overflow-auto p-4">
        <InputGroup>
          <InputGroupInput placeholder="Rechercher..." />
          <InputGroupAddon>
            <SearchIcon />
          </InputGroupAddon>
        </InputGroup>

        <Separator orientation="horizontal" />

        {folders && <Folders folders={folders} onSelect={setFolder} />}
      </aside>

      <main className="overflow-auto">
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead className="w-70">Image</TableHead>
              <TableHead>Nom</TableHead>
              <TableHead className="w-10">Taille</TableHead>
              <TableHead className="w-20">Actions</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            <TableRow>
              <TableCell colSpan={4}>
                <Input {...getInputProps()} type="file" style={{}} disabled={isPending} />
              </TableCell>
            </TableRow>
            {isPending && (
              <TableRow>
                <TableCell colSpan={4} className="text-center">
                  <Spinner className="inline-block" />
                </TableCell>
              </TableRow>
            )}
            {files.map((file) => (
              <FileRow file={file} onSelect={props.onSelect} key={file.id} />
            ))}
          </TableBody>
        </Table>
      </main>
    </div>
  );
}

function FileRow({ file, onSelect }: { file: AttachmentFileData; onSelect: Props["onSelect"] }) {
  const { mutate, isSuccess, isPending } = useApiMutation(
      route.destroy(file.id).url,
    {
      method: "DELETE",
    },
    {
      onError: (error) => toast.error(error.toString()),
      onSuccess: () => toast.success("Fichier supprimé avec succès"),
    },
  );

  if (isSuccess) {
    return null;
  }

  return (
    <TableRow key={file.id}>
      <TableCell>
        <button onClick={() => onSelect(file)} type="button">
          <img className="rounded-lg shadow" src={file.thumbnail} alt="" width={250} height={100} />
        </button>
      </TableCell>
      <TableCell>{file.name}</TableCell>
      <TableCell>{humanSize(file.size)}</TableCell>
      <TableCell>
        <Button variant="destructive" disabled={isPending} type="button" onClick={() => mutate()}>
          <TrashIcon />
        </Button>
      </TableCell>
    </TableRow>
  );
}

const defaultFolder = `${new Date().getFullYear()}/${(new Date().getMonth() + 1).toString().padStart(2, "0")}`;

function Folders(props: { folders: FolderData[]; onSelect: (path: string) => void }) {
  const years = Array.from(
    props.folders.reduce((acc, folder) => {
      acc.add(folder.path.split("/")[0]);
      return acc;
    }, new Set<string>()),
  );
  const [selected, setSelected] = useState(defaultFolder);
  const selectedYear = selected.split("/")[0];

  const folderForYear = (y: string) => {
    return props.folders.filter((f) => f.path.startsWith(y));
  };

  const onSelectFolder = (path: string) => {
    setSelected(path);
    props.onSelect(path);
  };

  return (
    <div>
      {years.map((year) => {
        const folders = folderForYear(year);
        const count = folders.reduce((acc, f) => acc + f.count, 0);
        return (
          <div key={year}>
            <Button
              onClick={() => setSelected(year)}
              variant="ghost"
              className={cn("w-full justify-start", selectedYear === year && "text-primary")}
            >
              {selectedYear === year ? <FolderOpenIcon size={16} /> : <FolderIcon size={16} />}
              {year}
              <Badge variant="secondary" className="ml-2">
                {count}
              </Badge>
            </Button>
            {selectedYear === year && (
              <div className="pl-4">
                {folders.map((folder) => {
                  return (
                    <Button
                      onClick={() => onSelectFolder(folder.path)}
                      key={folder.path}
                      variant="ghost"
                      className={cn("w-full justify-start", selected === folder.path && "text-primary")}
                    >
                      {folder.path === selected ? <FolderOpenIcon size={16} /> : <FolderIcon size={16} />}
                      {folder.path.replace(`${year}/`, "")}
                      <Badge variant="secondary" className="ml-2">
                        {folder.count}
                      </Badge>
                    </Button>
                  );
                })}
              </div>
            )}
          </div>
        );
      })}
    </div>
  );
}
