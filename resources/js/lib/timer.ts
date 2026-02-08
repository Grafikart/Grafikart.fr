/**
 * Debounce a callback
 */
export function debounce<T extends unknown[]>(
    func: (...args: T) => void,
    wait: number,
    immediate: boolean = false,
) {
    let timeout: ReturnType<typeof setTimeout> | null = null;
    return function (...args: T) {
        if (timeout) {
            clearTimeout(timeout);
        }
        timeout = setTimeout(() => {
            timeout = null;
            if (!immediate) {
                func(...args);
            }
        }, wait);
        if (immediate && !timeout) {
            func(...args);
        }
    };
}

/**
 * Throttle a callback
 */
export function throttle<T extends unknown[]>(
    callback: (...args: T) => void,
    delay: number,
) {
    let last: number | undefined;
    let timer: ReturnType<typeof setTimeout> | undefined;
    return function (...args: T) {
        const now = +new Date();
        if (last && now < last + delay) {
            // delay not elapsed, reset the timer
            clearTimeout(timer);
            timer = setTimeout(function () {
                last = now;
                callback(...args);
            }, delay);
        } else {
            last = now;
            callback(...args);
        }
    };
}

/**
 * Asynchronous version of setTimeout
 */
export function wait(duration: number): Promise<void> {
    return new Promise((resolve) => {
        window.setTimeout(resolve, duration);
    });
}
