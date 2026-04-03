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
} from "media-chrome/react"
import {
  MediaPlaybackRateMenu,
  MediaPlaybackRateMenuButton,
} from "media-chrome/react/menu"
import {
  type ReactEventHandler,
  type Ref,
  useEffect,
  useRef,
  useState,
} from "react"
import type CustomVideoElement from "youtube-video-element"
import YoutubeVideo from "youtube-video-element/react"

type Props = {
  id: string
  poster?: string | null
  chapters?: string
  start?: number
  onProgress?: (n: number) => void
  onTimeChange?: (time: number) => void
}

const videoCls = `aspect-video w-full h-auto`
const PROGRESSION_THRESHOLD = 10

/**
 * Player for courses for Youtube & HTML5 videos
 */
export function VideoPlayer(props: Props) {
  const isYoutube = !props.id.startsWith("/")
  const previousTime = useRef(props.start ?? 0)
  const [startTime] = useState(props.start ?? 0) // We want a fixed start time

  const onTimeUpdate: ReactEventHandler<{
    duration: number
    currentTime: number
  }> = (e) => {
    const currentTime = e.currentTarget.currentTime
    props.onTimeChange?.(currentTime)
    if (currentTime - previousTime.current > PROGRESSION_THRESHOLD) {
      previousTime.current = currentTime
      props.onProgress?.(currentTime / e.currentTarget.duration)
    }
  }

  const onEnded = () => {
    props.onProgress?.(1)
  }

  const video = useRef<{ currentTime: number }>(null)

  // Seek the video when the timer changes
  useEffect(() => {
    if (!props.start || !video.current) {
      return
    }
    video.current.currentTime = props.start
  }, [props.start])

  if (isYoutube) {
    return (
      <YoutubeVideo
        ref={video as Ref<CustomVideoElement>}
        key="video"
        onTimeUpdate={onTimeUpdate}
        onEnded={onEnded}
        controls
        className={videoCls}
        src={`https://www.youtube.com/watch?v=${props.id}`}
        autoplay
        config={{
          start: startTime,
        }}
      />
    )
  }

  return (
    <MediaController className={videoCls} key="controller">
      <video
        ref={video as Ref<HTMLVideoElement>}
        onTimeUpdate={onTimeUpdate}
        onEnded={onEnded}
        slot="media"
        src={`${props.id}#t=${startTime ?? 0}`}
        autoPlay
        poster={props.poster ?? undefined}
      >
        {props.chapters && (
          <track default kind="chapters" src={props.chapters} />
        )}
      </video>
      <MediaPlaybackRateMenu rates={[1, 1.2, 1.5, 2]} hidden anchor="auto" />
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
  )
}
