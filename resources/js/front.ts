import r2wc from '@r2wc/react-to-web-component';

import { LazyVideo } from '@/elements/lazy-video.tsx';
import { onPageLoad } from '@/lib/dom.ts';
import { bindSyntaxHighlighting } from '@/modules/highlighter.ts';
import '../css/front.css';

onPageLoad(() => {
    bindSyntaxHighlighting();
});

customElements.define(
    'lazy-video',
    r2wc(LazyVideo, {
        props: {
            videoid: 'string',
        },
    }),
);
