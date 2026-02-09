import {
    MediaControlBar,
    MediaController,
    MediaFullscreenButton,
    MediaMuteButton,
    MediaPipButton,
    MediaPlayButton,
    MediaTimeDisplay,
    MediaTimeRange,
    MediaVolumeRange,
} from 'media-chrome/react';
import {
    MediaPlaybackRateMenu,
    MediaPlaybackRateMenuButton,
} from 'media-chrome/react/menu';
import { type ReactEventHandler, useRef } from 'react';
import YoutubeVideo from 'youtube-video-element/react';

type Props = {
    id: string;
    poster?: string | null;
    chapters?: string;
    start?: number;
    onProgress?: (n: number) => void;
};

const videoCls = `aspect-video w-full h-auto`;
const PROGRESSION_THRESHOLD = 10;

export function VideoPlayer(props: Props) {
    const isYoutube = !props.id.startsWith('/');
    const previousTime = useRef(props.start ?? 0);

    const onTimeUpdate: ReactEventHandler<{
        duration: number;
        currentTime: number;
    }> = (e) => {
        const currentTime = e.currentTarget.currentTime;
        if (currentTime - previousTime.current > PROGRESSION_THRESHOLD) {
            previousTime.current = currentTime;
            props.onProgress?.(currentTime / e.currentTarget.duration);
        }
    };

    const onEnded = () => {
        props.onProgress?.(1);
    };

    if (isYoutube) {
        return (
            <YoutubeVideo
                onTimeUpdate={onTimeUpdate}
                onEnded={onEnded}
                controls
                className={videoCls}
                src={`https://www.youtube.com/watch?v=${props.id}`}
                autoplay
                config={{
                    start: props.start,
                }}
            />
        );
    }

    return (
        <MediaController className={videoCls}>
            <video
                onTimeUpdate={onTimeUpdate}
                onEnded={onEnded}
                slot="media"
                src={`${props.id}#t=${props.start ?? 0}`}
                autoPlay
                poster={props.poster ?? undefined}
            >
                {props.chapters && (
                    <track default kind="chapters" src={props.chapters} />
                )}
            </video>
            <MediaPlaybackRateMenu
                rates={[1, 1.2, 1.5, 2]}
                hidden
                anchor="auto"
            />
            <MediaControlBar>
                <MediaPlayButton />
                <MediaTimeRange />
                <MediaTimeDisplay />
                <MediaMuteButton />
                <MediaVolumeRange />
                <MediaPlaybackRateMenuButton />
                <MediaFullscreenButton />
                <MediaPipButton />
            </MediaControlBar>
        </MediaController>
    );
}
