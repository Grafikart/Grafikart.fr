import { CourseChapters } from '@/elements/course-chapters.ts';
import { LazyVideo } from '@/elements/lazy-video.ts';
import { NavTabs } from '@/elements/nav-tabs.ts';
import { SiteSearch } from '@/elements/site-search.tsx';
import { ThemeSwitcher } from '@/elements/theme-switcher.tsx';
import { lazywc, r2wc } from '@/lib/custom-element.ts';
import '@hotwired/turbo';
import '../css/front.css';

r2wc('path-preview', () => import('@/elements/path-preview.tsx'));
r2wc('path-detail', () => import('@/elements/path-detail.tsx'));
r2wc('site-search', SiteSearch, {});
r2wc('theme-switcher', ThemeSwitcher, {});

lazywc('code-block', () => import('@/elements/code-block.ts'));

customElements.define('lazy-video', LazyVideo);
customElements.define('course-chapters', CourseChapters);
customElements.define('nav-tabs', NavTabs);
