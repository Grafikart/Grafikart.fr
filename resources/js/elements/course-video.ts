import { createElement } from 'react';
import { createRoot, type Root } from 'react-dom/client';

import { Spinner } from '@/components/ui/spinner.tsx';
import { apiFetch } from '@/hooks/use-api-fetch.ts';
import { isAuthenticated } from '@/lib/auth.ts';
import { withViewTransition } from '@/lib/dom.ts';
import { loadYoutubeApi } from '@/lib/youtube.ts';

type State = 'poster' | 'loading' | 'playing' | 'paused' | 'ended';

const stickyClasses = [
    'fixed',
    'bottom-4',
    'right-4',
    'z-50',
    'w-[25vw]',
    'rounded-md',
    'shadow-md',
];

/**
 * Player for the video on the courses
 */
export class CourseVideo extends HTMLElement {
    private state: State = 'poster';
    private root: Root | null = null;
    private player: YT.Player | null = null;
    private progress: ProgressTracker | null = null;
    private observer: IntersectionObserver | null = null;
    private isSticky = false;
    private placeholder: HTMLElement | null = null;

    connectedCallback() {
        this.addEventListener('click', this.play, { once: true });
        window.addEventListener('hashchange', this.onHashChange);
        this.observer = new IntersectionObserver(
            ([entry]) => {
                if (
                    !entry.isIntersecting &&
                    this.state === 'playing' &&
                    !this.isSticky
                ) {
                    this.enableSticky();
                } else if (entry.isIntersecting && this.isSticky) {
                    this.disableSticky();
                }
            },
            { threshold: 0.5 },
        );
        this.observer.observe(this);
    }

    disconnectedCallback() {
        window.removeEventListener('hashchange', this.onHashChange);
        this.root?.unmount();
        this.progress?.stop(true);
        this.observer?.disconnect();
        this.placeholder?.remove();
    }

    onHashChange = async () => {
        if (!window.location.hash.startsWith('#t')) {
            return;
        }
        const time = parseInt(window.location.hash.replace('#t', ''));
        this.scrollIntoView({
            behavior: 'smooth',
            inline: 'center',
            block: 'center',
        });
        if (this.state === 'poster') {
            await this.play(time);
        } else {
            this.player?.seekTo(time, true);
            this.player?.playVideo();
        }
    };

    play = async (time: number | MouseEvent) => {
        if (this.state !== 'poster') {
            return;
        }
        this.removeEventListener('click', this.play);
        this.state = 'loading';
        this.root = createRoot(this);
        this.root.render(
            createElement(Spinner, { className: 'text-white size-10' }),
        );

        const ytApi = await loadYoutubeApi();
        const videoId = this.getAttribute('video')!;
        const fragment = document.createElement('div');
        this.innerHTML = '';
        this.appendChild(fragment);
        return new Promise<void>((resolve) => {
            this.player = new ytApi.Player(fragment, {
                videoId: videoId,
                host: 'https://www.youtube-nocookie.com',
                playerVars: {
                    autoplay: 1,
                    loop: 0,
                    modestbranding: 1,
                    controls: 1,
                    showinfo: 0,
                    rel: 0,
                    start: typeof time === 'number' ? time : 0,
                },
                events: {
                    onStateChange: isAuthenticated()
                        ? this.onYoutubePlayerStateChange
                        : undefined,
                    onReady: () => {
                        this.onYoutubePlayerReady();
                        resolve();
                    },
                },
            });
        });
    };

    public onYoutubePlayerStateChange = (event: YT.OnStateChangeEvent) => {
        switch (event.data) {
            case YT.PlayerState.PLAYING:
                this.progress?.start();
                this.state = 'playing';
                break;
            case YT.PlayerState.ENDED:
                this.progress?.stop();
                this.state = 'ended';
                break;
            case YT.PlayerState.PAUSED:
                this.progress?.stop();
                this.state = 'paused';
                break;
        }
    };

    public onYoutubePlayerReady = () => {
        const courseId = this.getAttribute('course');
        if (!courseId) {
            throw new Error(
                '<course-video> need a course attribute to track progress',
            );
        }
        this.progress = new ProgressTracker(courseId, () => {
            const duration = this.player?.getDuration();
            const currentTime = this.player?.getCurrentTime();
            if (!duration || !currentTime) {
                return 0;
            }
            return currentTime / duration;
        });
    };

    private enableSticky() {
        this.isSticky = true;
        const iframe = this.querySelector('iframe');
        if (iframe) {
            iframe.style.setProperty('view-transition-name', 'player');
            withViewTransition(() => {
                iframe.style.setProperty(
                    'max-width',
                    (window.innerWidth - 950) / 2 + 'px',
                );
                iframe.classList.add(...stickyClasses);
            });
        }
    }

    private disableSticky() {
        this.isSticky = false;
        const iframe = this.querySelector('iframe');
        if (iframe) {
            withViewTransition(() => {
                iframe.style.removeProperty('max-width');
                iframe.classList.remove(...stickyClasses);
            });
        }
    }
}

/**
 * Handle sending progress to the server
 */
class ProgressTracker {
    private timer: ReturnType<typeof setInterval> | null = null;

    constructor(
        private id: string,
        private getProgress: () => number,
    ) {}

    public start() {
        this.timer = setInterval(this.ping, 10_000);
    }

    public stop(quiet = false) {
        if (!quiet) {
            this.ping();
        }
        if (this.timer) {
            clearInterval(this.timer);
        }
    }

    private ping = async () => {
        const progress = Math.round(this.getProgress() * 1000);
        await apiFetch(`/api/progress/${this.id}`, {
            method: 'POST',
            body: JSON.stringify({
                progress,
            }),
        });
    };
}
