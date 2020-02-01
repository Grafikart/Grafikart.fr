export default class RecapLiveElement extends HTMLElement {

    private isPlaying = false
    private videoContainer?: HTMLDivElement
    private iframe?: HTMLIFrameElement
    private currentLive?: HTMLDivElement
    private liveList?: HTMLDivElement

    connectedCallback() {
        this.videoContainer = <HTMLDivElement>this.querySelector('.js-video')
        this.liveList = <HTMLDivElement>this.querySelector('.js-videos')
        const lives: NodeListOf<HTMLDivElement> = this.querySelectorAll('.live')
        lives.forEach((live) => {
            live.addEventListener('click', this.play.bind(this))
        })
    }

    /**
     * Lance la lecture d'une vidéo
     */
    play (e: MouseEvent) {
        const live = <HTMLDivElement>e.currentTarget
        const id = <string>live.dataset.youtube
        if (this.videoContainer === undefined || this.liveList === undefined) {
            return
        }
        if (this.iframe) {
            this.iframe.setAttribute('src', this.youtubeURL(id))
        } else {
            this.videoContainer.innerHTML = `<iframe 
            src="${this.youtubeURL(id)}"
            allowfullscreen></iframe>`
            this.iframe = <HTMLIFrameElement>this.videoContainer.querySelector('iframe')
        }
        this.classList.add('is-playing')
        this.classList.add('card')
        if (this.currentLive) {
            this.currentLive.classList.remove('is-playing')
        }
        this.currentLive = live
        this.isPlaying = true
        live.classList.add('is-playing')
        this.liveList.scrollTo({
            top: 100,
            left: 100,
            behavior: 'smooth'
        });
    }

    /**
     * Génère l'URL pour l'embed
     */
    private youtubeURL (id: string) {
        return `https://www.youtube-nocookie.com/embed/${id}?controls=1&autoplay=1&loop=0&showinfo=0&rel=0&hd=1`
    }
}
