import { r2wc } from '@/lib/custom-element.ts';
import { onPageLoad } from '@/lib/dom.ts';
import { bindSyntaxHighlighting } from '@/modules/highlighter.ts';
import '../css/front.css';

onPageLoad(() => {
    bindSyntaxHighlighting();
});

r2wc('lazy-video', () => import('@/elements/lazy-video.tsx'));
r2wc('path-preview', () => import('@/elements/path-preview.tsx'));
r2wc('path-detail', () => import('@/elements/path-detail.tsx'));
