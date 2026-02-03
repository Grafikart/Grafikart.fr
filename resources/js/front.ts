import { lazywc, r2wc } from '@/lib/custom-element.ts';
import '../css/front.css';

r2wc('lazy-video', () => import('@/elements/lazy-video.tsx'));
r2wc('path-preview', () => import('@/elements/path-preview.tsx'));
r2wc('path-detail', () => import('@/elements/path-detail.tsx'));
lazywc('code-block', () => import('@/elements/code-block.ts'));
