const ANIMATION_DURATION = 300;
const SHOW_DELAY = 500;

let styleElement: HTMLStyleElement | null = null;
let progressElement: HTMLDivElement | null = null;
let trickleInterval: ReturnType<typeof setInterval> | null = null;
let showTimeout: ReturnType<typeof setTimeout> | null = null;
let value = 0;
let visible = false;
let hiding = false;

function ensureElements(): void {
    if (!styleElement) {
        styleElement = document.createElement('style');
        styleElement.textContent = `
            .turbo-progress-bar {
                position: fixed;
                top: 0;
                left: 0;
                height: 3px;
                background: #0076ff;
                z-index: 2147483647;
                transition:
                    width ${ANIMATION_DURATION}ms ease-out,
                    opacity ${ANIMATION_DURATION / 2}ms ${ANIMATION_DURATION / 2}ms ease-in;
                transform: translate3d(0, 0, 0);
            }
        `;
        document.head.insertBefore(styleElement, document.head.firstChild);
    }
    if (!progressElement) {
        progressElement = document.createElement('div');
        progressElement.className = 'turbo-progress-bar';
    }
}

function show(): void {
    if (visible) {
        return;
    }
    visible = true;
    ensureElements();
    progressElement!.style.width = '0';
    progressElement!.style.opacity = '1';
    document.documentElement.insertBefore(progressElement!, document.body);
    setValue(0);
    startTrickling();
}

function hide(): void {
    if (!visible || hiding) {
        return;
    }
    hiding = true;
    progressElement!.style.opacity = '0';
    setTimeout(() => {
        progressElement?.remove();
        stopTrickling();
        visible = false;
        hiding = false;
    }, ANIMATION_DURATION * 1.5);
}

function setValue(v: number): void {
    value = v;
    requestAnimationFrame(() => {
        if (progressElement) {
            progressElement.style.width = `${10 + value * 90}%`;
        }
    });
}

function startTrickling(): void {
    if (!trickleInterval) {
        trickleInterval = setInterval(() => {
            setValue(value + Math.random() / 100);
        }, ANIMATION_DURATION);
    }
}

function stopTrickling(): void {
    if (trickleInterval) {
        clearInterval(trickleInterval);
        trickleInterval = null;
    }
}

export function showProgressBarAfterDelay(): void {
    hideProgressBar();
    showTimeout = setTimeout(show, SHOW_DELAY);
}

export function hideProgressBar(): void {
    if (showTimeout) {
        clearTimeout(showTimeout);
        showTimeout = null;
    }
    if (visible) {
        setValue(1);
        hide();
    }
}
