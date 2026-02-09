import { type PropsWithChildren, useEffect, useMemo, useState } from 'react';
import { useWindowSize } from 'usehooks-ts';

import { withViewTransition } from '@/lib/dom.ts';
import { cn } from '@/lib/utils.ts';

type Props = PropsWithChildren<{
    parent: HTMLElement
}>

const minWidth = 250;

export function StickyVideo (props: Props) {
    const {width} = useWindowSize({
        debounceDelay: 300,
        initializeWithValue: true
    })
    const availableWidth = useMemo(() => {
        const w = (width - 900) / 2
        if (document.body.classList.contains('has-sidebar')) {
            return w + 120
        }
        return w
    }, [width])
    const [visible, setVisible] = useState(true)
    const sticky = !visible && availableWidth > minWidth


    useEffect(() => {
        const observer = new IntersectionObserver(function ([entry]) {
            withViewTransition(() => {
                setVisible(entry.isIntersecting)
            })
        }, {
            threshold: 0.3
        });
        observer.observe(props.parent)

        return () => {
            observer.disconnect()
        }
    }, [props.parent])


    return <div className={cn(
        "aspect-video w-full h-auto",
        sticky && "fixed bottom-4 right-4 rounded-md overflow-hidden shadow-md z-100"
    )} style={{width: sticky ? `${availableWidth}px` : undefined, viewTransitionName: 'videosticky'}}>
        {props.children}
    </div>
}
