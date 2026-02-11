import { Confetti } from '@/elements/con-fetti.ts';
import { CourseChapters } from '@/elements/course-chapters.ts';
import { CourseFilters } from '@/elements/course-filters.tsx';
import { CourseVideo } from '@/elements/course-video.ts';
import { LazyVideo } from '@/elements/lazy-video.ts';
import { NavTabs } from '@/elements/nav-tabs.ts';
import { PremiumButton } from '@/elements/premium-button.tsx';
import { SiteHeader } from '@/elements/site-header.ts';
import { SiteSearch } from '@/elements/site-search.tsx';
import { ThemeSwitcher } from '@/elements/theme-switcher.tsx';
import { TimeAgo } from '@/elements/time-ago.ts';
import { lazywc, r2wc } from '@/lib/custom-element.ts';
import { start as startTurbo } from '@/lib/turbo/index.ts';
import '../css/front.css';

r2wc('path-preview', () => import('@/elements/path-preview.tsx'));
r2wc('path-detail', () => import('@/elements/path-detail.tsx'));
r2wc('course-filters', CourseFilters, {});
r2wc('site-search', SiteSearch, {});
r2wc('theme-switcher', ThemeSwitcher, {});

lazywc('code-block', () => import('@/elements/code-block.ts'));

customElements.define('course-video', CourseVideo);
customElements.define('lazy-video', LazyVideo);
customElements.define('course-chapters', CourseChapters);
customElements.define('nav-tabs', NavTabs);
customElements.define('premium-button', PremiumButton);
customElements.define('con-fetti', Confetti);
customElements.define('site-header', SiteHeader);
customElements.define('time-ago', TimeAgo);

startTurbo();
