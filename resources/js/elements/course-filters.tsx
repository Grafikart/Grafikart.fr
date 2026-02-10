import { QueryClientProvider } from '@tanstack/react-query';
import {
    ListVideoIcon,
    SearchIcon,
    SquarePlayIcon,
    StarIcon,
} from 'lucide-react';
import {
    createContext,
    type MouseEventHandler,
    type ReactNode,
    useContext,
    useState,
} from 'react';

import { queryClient, useApiFetch } from '@/hooks/use-api-fetch.ts';
import { cn } from '@/lib/utils.ts';
import type { CourseFiltersResponse } from '@/types';

const SearchParamsContext = createContext(
    {} as {
        params: URLSearchParams;
        setUrl: (url: string) => void;
        pathname: string;
    },
);

export function CourseFilters() {
    const [url, setUrl] = useState(() => new URL(window.location.toString()));

    const setUrlStr = (s: string) => setUrl(new URL(s));

    const params = url.searchParams;
    const pathname = url.pathname;

    return (
        <QueryClientProvider client={queryClient}>
            <SearchParamsContext
                value={{ params, setUrl: setUrlStr, pathname }}
            >
                <CourseFiltersInner />
            </SearchParamsContext>
        </QueryClientProvider>
    );
}

function CourseFiltersInner() {
    const { data, isLoading } = useApiFetch<CourseFiltersResponse>(
        '/api/courses/filters',
        {
            staleTime: 60_000,
        },
    );
    const [expanded, setExpanded] = useState(false);
    const { pathname } = useContext(SearchParamsContext);

    if (isLoading || !data) {
        return (
            <div>
                {[...Array(4)].map((_, i) => (
                    <div key={i} className="mb-6">
                        <div className="bg-border/40 mx-2 mb-3 h-3 w-16 animate-pulse rounded" />
                        <SkeletonLine />
                        <SkeletonLine />
                        <SkeletonLine />
                    </div>
                ))}
            </div>
        );
    }

    const isFormation = pathname.startsWith('/formation');
    const technologies = data.technologies.slice(0, 8);
    const hiddenTechnologies = data.technologies.slice(8);

    return (
        <div className="space-y-6">
            <div className="relative border-b pt-px">
                <input
                    className="focus:border-primary peer w-full border-2 border-transparent py-2 pl-8 text-sm outline-none"
                    placeholder="Rechercher un contenu"
                />
                <SearchIcon className="text-muted peer-focus:text-primary absolute left-2 top-3 size-4" />
            </div>
            <div>
                <FilterLink
                    root
                    count={data.types.course}
                    filterKey="type"
                    value="course"
                >
                    <SquarePlayIcon />
                    Tutoriels
                </FilterLink>
                <FilterLink
                    root
                    count={data.types.formation}
                    filterKey="type"
                    value="formation"
                >
                    <ListVideoIcon />
                    Formations
                </FilterLink>
            </div>

            <FilterSection title="Technologie">
                {technologies.map((t) => (
                    <FilterLink
                        key={t.value}
                        children={t.label}
                        count={
                            isFormation ? t.formations_count : t.courses_count
                        }
                        filterKey="technology"
                        value={t.value}
                    />
                ))}
                {hiddenTechnologies.length > 0 && (
                    <>
                        {expanded &&
                            hiddenTechnologies.map((t) => (
                                <FilterLink
                                    key={t.value}
                                    children={t.label}
                                    count={
                                        isFormation
                                            ? t.formations_count
                                            : t.courses_count
                                    }
                                    filterKey="technology"
                                    value={t.value}
                                />
                            ))}
                        <button
                            type="button"
                            onClick={() => setExpanded(!expanded)}
                            className="text-muted hover:text-foreground w-full cursor-pointer px-2 py-1.5 text-start text-sm"
                        >
                            {expanded
                                ? 'Voir moins'
                                : `Voir plus (${hiddenTechnologies.length})`}
                        </button>
                    </>
                )}
            </FilterSection>

            {!isFormation && (
                <>
                    <FilterSection title="Niveau">
                        {data.levels.map((l) => (
                            <FilterLink
                                key={l.value}
                                children={l.label}
                                filterKey="level"
                                value={l.value}
                            />
                        ))}
                    </FilterSection>

                    <FilterSection title="Durée">
                        <FilterLink
                            children="< 10 min"
                            filterKey="duration"
                            value="10"
                        />
                        <FilterLink
                            children="< 20 min"
                            filterKey="duration"
                            value="20"
                        />
                        <FilterLink
                            children="< 1 heure"
                            filterKey="duration"
                            value="60"
                        />
                    </FilterSection>

                    <FilterSection title="Premium">
                        <FilterLink
                            root
                            className="text-yellow"
                            filterKey="premium"
                            value="1"
                        >
                            <StarIcon /> Vidéo premium
                        </FilterLink>
                    </FilterSection>
                </>
            )}
        </div>
    );
}

function buildUrl(params: URLSearchParams, key: string, value: string): string {
    const next = new URLSearchParams(params);
    next.delete('page');
    let path = '';

    if (next.get(key) === value) {
        next.delete(key);
    } else {
        next.set(key, value);
    }

    if (key === 'type') {
        path = next.get(key) === 'formation' ? '/formations' : '/tutoriels';
        next.delete('type');
        if (next.get(key) === 'formation') {
            next.delete('level');
            next.delete('duration');
            next.delete('premium');
        }
    }

    const qs = next.toString();
    return qs ? `${path}?${qs}` : window.location.pathname;
}

/**
 * Fetch the next page and replace the current content
 */
async function fetchAndReplaceMain(url: string): Promise<void> {
    const response = await fetch(url, {
        headers: { Accept: 'text/html' },
    });

    if (!response.ok) {
        alert('Impossible de récupérer ce filtre');
        return;
    }

    const html = await response.text();
    const doc = new DOMParser().parseFromString(html, 'text/html');
    const newMain = doc.querySelector('main');
    const currentMain = document.querySelector('main');

    if (!newMain || !currentMain) {
        alert('Impossible de placer le nouveau contenu');
        return;
    }
    const adopted = document.adoptNode(newMain);
    currentMain.replaceWith(adopted);
}

function FilterLink({
    children,
    count,
    filterKey,
    value,
    className,
    root,
}: {
    children: ReactNode;
    count?: number | null;
    filterKey: string;
    value: string;
    className?: string;
    root?: boolean;
}) {
    const { params, setUrl, pathname } = useContext(SearchParamsContext);
    const active = (() => {
        if (filterKey === 'type' && value === 'formation') {
            return pathname.startsWith('/formation');
        } else if (filterKey === 'type') {
            return pathname.startsWith('/tutoriels');
        }
        return params.get(filterKey) === value;
    })();
    const href = buildUrl(params, filterKey, value);

    const handleClick: MouseEventHandler<HTMLAnchorElement> = async (e) => {
        e.preventDefault();
        const url = e.currentTarget.getAttribute('href')!;
        window.history.replaceState({}, '', url);
        setUrl(window.location.toString());
        await fetchAndReplaceMain(url);
    };

    return (
        <a
            href={href}
            onClick={handleClick}
            aria-selected={active}
            className={cn(
                'flex items-center gap-2 px-2 py-1.5 text-sm [&_svg]:size-4',
                !root && 'border-l',
                'aria-selected:border-l-primary aria-selected:bg-primary/5 aria-selected:text-primary aria-selected:border-l-2',
                'hover:bg-border/20',
                className,
            )}
        >
            {children}
            {count != null && (
                <span className="text-muted ml-auto text-xs">{count}</span>
            )}
        </a>
    );
}

function FilterSection({
    title,
    children,
}: {
    title: string;
    children: ReactNode;
}) {
    return (
        <div className="px-2">
            <h3 className="text-muted mb-2 text-xs font-medium uppercase">
                {title}
            </h3>
            {children}
        </div>
    );
}

function SkeletonLine() {
    return (
        <div className="bg-border/40 mx-2 my-2.5 h-3.5 animate-pulse rounded" />
    );
}
