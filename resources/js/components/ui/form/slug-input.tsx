import { Button } from '@/components/ui/button.tsx';
import { slugify } from '@/lib/string.ts';
import { LinkIcon } from 'lucide-react';
import { type ChangeEvent } from 'react';

type SlugInputProps = {
    defaultValue: string;
    prefix: string;
    url?: string;
};

export function SlugInput({ defaultValue, prefix, url }: SlugInputProps) {
    const handleChange = (e: ChangeEvent<HTMLInputElement>) => {
        e.target.value = slugify(e.target.value);
    };

    return (
        <div className="flex text-sm text-muted-foreground mb-3 items-center">
            <span className="opacity-50">{prefix}</span>
            <input
                type="text"
                name="slug"
                placeholder="slug"
                defaultValue={defaultValue}
                onChange={handleChange}
                className="outline-none field-sizing-content min-w-10"
            />
            {url && (
                <Button
                    nativeButton={false}
                    variant="ghost"
                    render={<a target="_blank" href={url} />}
                    size="icon-xs"
                >
                    <LinkIcon />
                </Button>
            )}
        </div>
    );
}
