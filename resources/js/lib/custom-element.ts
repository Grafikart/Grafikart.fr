import { createElement, type FC } from 'react';
import { createRoot, type Root } from 'react-dom/client';

function parseProps(props: Record<string, string>, el: HTMLElement) {
    return Object.fromEntries(
        Object.entries(props).map(([key, type]) => [
            key,
            convertAttribute(el.getAttribute(key), type),
        ]),
    );
}

function convertAttribute(value: string | null, type: string) {
    if (!value) {
        return null;
    }
    if (type === 'json') {
        return JSON.parse(value);
    }
    if (type === 'number') {
        return parseFloat(value);
    }
    return value;
}

export function r2wc(
    tagName: string,
    cb: () => Promise<{
        default: {
            // eslint-disable-next-line @typescript-eslint/no-explicit-any
            component: FC<any>;
            props: Record<string, string>;
        };
    }>,
) {
    customElements.define(
        tagName,
        class A extends HTMLElement {
            root: Root | null = null;

            connectedCallback() {
                cb().then((module) => {
                    this.root = createRoot(this);
                    const element = createElement(
                        module.default.component,
                        parseProps(module.default.props, this),
                    );
                    this.root.render(element);
                });
            }

            disconnectedCallback() {
                this.root?.unmount();
            }
        },
    );
}

export abstract class LazyComponent {
    constructor(protected el: HTMLElement) {}
    abstract onMount(): void | Promise<void>;
    onUnmount() {}
}

/**
 * Register custom element lazily
 */
export function lazywc(
    tagName: string,
    cb: () => Promise<{
        default: { new (el: HTMLElement): LazyComponent };
    }>,
) {
    customElements.define(
        tagName,
        class A extends HTMLElement {
            innerElement: LazyComponent | null = null;

            connectedCallback() {
                cb().then((module) => {
                    this.innerElement = new module.default(this);
                    this.innerElement.onMount();
                });
            }

            disconnectedCallback() {
                this.innerElement?.onUnmount();
            }
        },
    );
}
