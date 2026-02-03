import { createElement, type FC } from 'react';
import { createRoot, type Root } from 'react-dom/client';

function parseProps(
    props: Record<string, string>,
    el: HTMLElement,
): { element: HTMLElement; [k: string]: unknown } {
    return {
        ...Object.fromEntries(
            Object.entries(props).map(([key, type]) => [
                key,
                convertAttribute(el.getAttribute(key), type),
            ]),
        ),
        element: el,
    };
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

type LazyImport = () => Promise<{
    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    default: { component: FC<any>; props: Record<string, string> };
}>;

export function r2wc(tagName: string, lazyImport: LazyImport): void;
export function r2wc(
    tagName: string,
    component: FC<{ element: HTMLElement; [k: string]: unknown }>,
    props: Record<string, string>,
): void;
export function r2wc(
    tagName: string,
    componentOrImport:
        | FC<{ element: HTMLElement; [k: string]: unknown }>
        | LazyImport,
    props?: Record<string, string>,
): void {
    customElements.define(
        tagName,
        class A extends HTMLElement {
            root: Root | null = null;

            connectedCallback() {
                // Direct component passed
                if (props !== undefined) {
                    this.root = createRoot(this);
                    const element = createElement(
                        componentOrImport as FC<{
                            element: HTMLElement;
                            [k: string]: unknown;
                        }>,
                        parseProps(props, this),
                    );
                    this.root.render(element);
                    return;
                }

                // Lazy import passed
                (componentOrImport as LazyImport)().then((module) => {
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
