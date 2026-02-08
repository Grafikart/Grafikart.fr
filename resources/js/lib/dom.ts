export function onPageLoad(cb: () => void) {
    cb();
}

export function siblings<T extends HTMLElement>(el: T): T[] {
    return Array.from(el.parentElement!.children).filter(
        (child) => child !== el,
    ) as T[];
}

export function $(selector: string) {
    return document.querySelector(selector);
}

export function $$(selector: string) {
    return Array.from(document.querySelectorAll(selector));
}

export function strToDom<T extends HTMLElement>(str: string): T {
    return document.createRange().createContextualFragment(str).firstChild as T;
}

type ViewTransitionCallback = () => void | Promise<void>;

export function withViewTransition(callback: ViewTransitionCallback) {
    if (!document.startViewTransition) {
        return callback();
    }

    return document.startViewTransition(callback);
}

/**
 * Add event listener to all elements matching the selector.
 */
export function onAll<T extends Element>(
    base: Element | Document,
    selector: string,
    eventName: Parameters<T['addEventListener']>[0],
    callback: (e: Event & { currentTarget: T }) => void,
) {
    base.querySelectorAll<T>(selector).forEach((el) => {
        // @ts-expect-error callback is typed with a stronger specification on currentTarget
        el.addEventListener(eventName, callback);
    });
}
