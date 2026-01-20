// eslint-disable-next-line import/order
import {
 type CodeBlockEditorDescriptor,
 codeBlockPlugin,
 diffSourcePlugin,
 headingsPlugin,
 imagePlugin,
 linkPlugin,
 listsPlugin,
 markdownShortcutPlugin,
 MDXEditor,
 type MDXEditorMethods,
 quotePlugin,
 thematicBreakPlugin,
 useCodeBlockEditorContext
} from "@mdxeditor/editor";

import "@mdxeditor/editor/style.css";
import { ImageIcon, SquareCodeIcon } from "lucide-react";
import { useRef, useState } from "react";
import { toast } from "sonner";

import { Button } from "@/components/ui/button.tsx";
import { Textarea } from "@/components/ui/textarea.tsx";

type Props = {
  defaultValue: string;
  name: string;
};
export function MDEditor(props: Props) {
  const [value, setValue] = useState(props.defaultValue);
  const editor = useRef<MDXEditorMethods>(null);
  const [mode, setMode] = useState<"rich-text" | "source">("rich-text");

  const toggleMode = () => {
    setMode((m) => (m === "rich-text" ? "source" : "rich-text"));
  };

  return (
    <div className="relative">
      <Button size="icon" className="absolute -left-10 top-0 z-30" variant="secondary" onClick={toggleMode}>
        {mode === "rich-text" && <SquareCodeIcon />}
        {mode === "source" && <ImageIcon />}
      </Button>
      <MDXEditor
        ref={editor}
        key={mode}
        className="prose"
        markdown={value}
        plugins={[
          headingsPlugin(),
          listsPlugin(),
          quotePlugin(),
          diffSourcePlugin({ diffMarkdown: props.defaultValue, viewMode: mode }),
          thematicBreakPlugin(),
          markdownShortcutPlugin(),
          linkPlugin(),
          imagePlugin(),
          codeBlockPlugin({ codeBlockEditorDescriptors: [PlainTextCodeEditorDescriptor] }),
        ]}
        onChange={setValue}
        onError={(e) => toast.error(e.error)}
      />
      <input type="hidden" value={value} name={props.name} />
    </div>
  );
}

const PlainTextCodeEditorDescriptor: CodeBlockEditorDescriptor = {
  match: () => true,
  priority: 0,
  Editor: (props) => {
    const cb = useCodeBlockEditorContext();
    // stops the propagation so that the parent lexical editor does not handle certain events.
    return (
      <div onKeyDown={(e) => e.nativeEvent.stopImmediatePropagation()}>
        <Textarea
          className="font-mono"
          rows={3}
          cols={20}
          defaultValue={props.code}
          onChange={(e) => cb.setCode(e.target.value)}
        />
      </div>
    );
  },
};
