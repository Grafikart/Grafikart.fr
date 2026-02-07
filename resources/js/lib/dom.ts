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
