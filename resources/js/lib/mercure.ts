import type { NotificationCreatedEvent } from '@/types';

type Event<T extends string, D extends object> = { type: T } & D;

type Events = Event<'NotificationCreatedEvent', NotificationCreatedEvent>;

/**
 * Subscribe to mercure topics with additional typings for events
 */
export function subscribeToMercure(
    topics: string[],
    callback: (event: Events) => void,
) {
    const url = new URL(window.location.origin + '/.well-known/mercure');
    for (const topic of topics) {
        url.searchParams.append('topic', topic);
    }
    const es = new EventSource(url.toString());
    es.addEventListener('message', (event) => {
        const data = JSON.parse(event.data) as { event: string; data: unknown };
        callback({
            ...(data.data as Events),
            type: data.event.split('\\').at(-1),
        } as Events);
    });
}
