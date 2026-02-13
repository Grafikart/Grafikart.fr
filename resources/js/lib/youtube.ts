declare global {
  interface Window {
    onYouTubeIframeAPIReady?: () => void
  }
}

export async function loadYoutubeApi(): Promise<typeof YT> {
  return new Promise((resolve) => {
    if (window.YT) {
      resolve(window.YT)
      return
    }
    const tag = document.createElement("script")
    tag.src = "https://www.youtube.com/iframe_api"
    const firstScriptTag = document.getElementsByTagName("script")[0]
    firstScriptTag.parentNode!.insertBefore(tag, firstScriptTag)
    window.onYouTubeIframeAPIReady = () => {
      window.onYouTubeIframeAPIReady = undefined
      resolve(window.YT)
    }
  })
}
