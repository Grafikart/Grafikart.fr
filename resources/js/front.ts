import { CourseChapters } from '@/elements/course-chapters.ts';
import { SiteSearch } from '@/elements/site-search.tsx';
import { ThemeSwitcher } from '@/elements/theme-switcher.tsx';
import { lazywc, r2wc } from '@/lib/custom-element.ts';
import '@hotwired/turbo';
import '../css/front.css';

r2wc('lazy-video', () => import('@/elements/lazy-video.tsx'));
r2wc('path-preview', () => import('@/elements/path-preview.tsx'));
r2wc('path-detail', () => import('@/elements/path-detail.tsx'));
r2wc('site-search', SiteSearch, {});
lazywc('code-block', () => import('@/elements/code-block.ts'));
r2wc('theme-switcher', ThemeSwitcher, {});
customElements.define('course-chapters', CourseChapters);
