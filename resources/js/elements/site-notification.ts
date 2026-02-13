import { createElement } from 'react';
import { createRoot, type Root } from 'react-dom/client';

import { NotificationsMenu } from '@/components/front/notifications-menu.tsx';

export class SiteNotificationElement extends HTMLElement {
    private root: Root | null = null;
    private openMenu: (() => void) | null = null;

    connectedCallback() {
        const div = document.createElement('div');
        div.classList.add('contents');
        this.insertAdjacentElement('beforeend', div);
        this.root = createRoot(div);
        this.root.render(
            createElement(NotificationsMenu, {
                element: this,
                readAt: parseInt(this.getAttribute('read-at')!, 10),
            }),
        );
    }

    disconnectedCallback() {
        this.root?.unmount();
    }
}
