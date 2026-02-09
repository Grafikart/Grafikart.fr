import { hideProgressBar, showProgressBarAfterDelay } from './progress.ts';
import { renderPage } from './renderer.ts';

export type VisitAction = 'advance' | 'replace' | 'restore';

let currentRequest: AbortController | null = null;

export async function performVisit(
    url: URL,
    action: VisitAction,
): Promise<void> {
    const beforeVisit = dispatch('turbo:before-visit', { url: url.href }, true);
    if (beforeVisit.defaultPrevented) {
        return;
    }

    // Cancel any in-flight request
    currentRequest?.abort();
    const controller = new AbortController();
    currentRequest = controller;

    dispatch('turbo:visit', { url: url.href, action });
    showProgressBarAfterDelay();

    let response: Response;
    try {
        response = await fetch(url.href, {
            signal: controller.signal,
            headers: { Accept: 'text/html' },
        });
    } catch (e) {
        if ((e as Error).name === 'AbortError') {
            return;
        }
        hideProgressBar();
        window.location.href = url.href;
        return;
    } finally {
        if (currentRequest === controller) {
            currentRequest = null;
        }
    }

    if (!response.ok) {
        hideProgressBar();
        window.location.href = url.href;
        return;
    }

    const html = await response.text();
    const newDoc = new DOMParser().parseFromString(html, 'text/html');

    const useTransition =
        prefersViewTransitions() && prefersViewTransitions(newDoc);

    const render = async () => {
        const beforeRender = dispatch(
            'turbo:before-render',
            { newBody: newDoc.body },
            true,
        );
        if (beforeRender.defaultPrevented) {
            return;
        }

        await renderPage(newDoc);
        dispatch('turbo:render', {});
    };

    if (useTransition && document.startViewTransition) {
        await document.startViewTransition(() => render()).finished;
    } else {
        await render();
    }

    if (action === 'advance') {
        history.pushState({ turbo: true }, '', url.href);
    } else if (action === 'replace') {
        history.replaceState({ turbo: true }, '', url.href);
    }

    const anchor = url.hash.slice(1);
    if (anchor) {
        document.getElementById(anchor)?.scrollIntoView();
    } else {
        window.scrollTo(0, 0);
    }

    hideProgressBar();
    dispatch('turbo:load', { url: url.href });
}

function prefersViewTransitions(doc: Document = document): boolean {
    const meta = doc.querySelector<HTMLMetaElement>(
        'meta[name="view-transition"]',
    );
    if (meta?.content !== 'same-origin') {
        return false;
    }
    return !window.matchMedia('(prefers-reduced-motion: reduce)').matches;
}

function dispatch(
    name: string,
    detail: Record<string, unknown>,
    cancelable = false,
): Event {
    const event = new CustomEvent(name, { bubbles: true, cancelable, detail });
    document.dispatchEvent(event);
    return event;
}
