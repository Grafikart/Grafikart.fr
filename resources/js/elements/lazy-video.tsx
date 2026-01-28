import {
    type MouseEventHandler,
    type Ref,
    useEffect,
    useMemo,
    useRef,
    useState,
} from 'react';

/**
 * Lazy YouTube player that respond to hash "#t40" to update it's starting time
 *
 * @param {string} videoid
 */
function LazyVideo({ videoid }: { videoid: string }) {
    const baseUrl = `https://www.youtube-nocookie.com/embed/${videoid}?autoplay=1`;
    const [autoplay, setAutoplay] = useState(false);
    const [time, setTime] = useState(0);
    const iframe = useRef<HTMLElement>(null);

    const url = useMemo(() => {
        const url = new URL(baseUrl);
        if (time) {
            url.searchParams.set('start', time.toString());
        }
        return url.toString();
    }, [time, baseUrl]);

    useEffect(() => {
        const onHashChange = () => {
            if (!window.location.hash.startsWith('#t')) {
                return;
            }
            setTime(parseInt(window.location.hash.replace('#t', '')));
            setAutoplay(true);
            iframe.current?.scrollIntoView({
                behavior: 'smooth',
                block: 'center',
                inline: 'center',
            });
        };
        addEventListener('hashchange', onHashChange);
        return () => {
            removeEventListener('hashchange', onHashChange);
        };
    }, []);

    const play: MouseEventHandler<HTMLAnchorElement> = (e) => {
        e.preventDefault();
        setAutoplay(true);
    };

    if (!autoplay) {
        return (
            <a
                ref={iframe as Ref<HTMLAnchorElement>}
                className="grid place-items-center overflow-hidden"
                onClick={play}
                href={baseUrl}
            >
                <img
                    alt=""
                    className="aspect-video w-full object-cover [grid-area:1/1]"
                    src={`https://img.youtube.com/vi/${videoid}/hqdefault.jpg`}
                />
                <svg
                    className="h-12 [grid-area:1/1]"
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 1024 721"
                >
                    <path fill="#FFF" d="m407 493 276-143-276-144v287z" />
                    <path
                        fill="#420000"
                        d="m407 206 242 161.6 34-17.6-276-144z"
                        opacity=".12"
                    />
                    <linearGradient
                        id="a"
                        x1="512.5"
                        x2="512.5"
                        y1="719.7"
                        y2="1.2"
                        gradientTransform="matrix(1 0 0 -1 0 721)"
                        gradientUnits="userSpaceOnUse"
                    >
                        <stop offset="0" stop-color="#e52d27" />
                        <stop offset="1" stop-color="#bf171d" />
                    </linearGradient>
                    <path
                        fill="url(#a)"
                        d="M1013 156.3s-10-70.4-40.6-101.4C933.6 14.2 890 14 870.1 11.6 727.1 1.3 512.7 1.3 512.7 1.3h-.4s-214.4 0-357.4 10.3C135 14 91.4 14.2 52.6 54.9 22 85.9 12 156.3 12 156.3S1.8 238.9 1.8 321.6v77.5C1.8 481.8 12 564.4 12 564.4s10 70.4 40.6 101.4c38.9 40.7 89.9 39.4 112.6 43.7 81.7 7.8 347.3 10.3 347.3 10.3s214.6-.3 357.6-10.7c20-2.4 63.5-2.6 102.3-43.3 30.6-31 40.6-101.4 40.6-101.4s10.2-82.7 10.2-165.3v-77.5c0-82.7-10.2-165.3-10.2-165.3zM407 493V206l276 144-276 143z"
                    />
                </svg>
            </a>
        );
    }

    return (
        <iframe
            ref={iframe as Ref<HTMLIFrameElement>}
            className="aspect-video w-full"
            src={url}
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
            referrerPolicy="strict-origin-when-cross-origin"
            allowFullScreen
        />
    );
}

export default {
    component: LazyVideo,
    props: { videoid: 'string' },
};
