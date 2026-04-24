import { Confetti } from "@/elements/con-fetti.ts";
import { CourseChapters } from "@/elements/course-chapters.ts";
import { CourseFilters } from "@/elements/course-filters.tsx";
import { CourseVideo } from "@/elements/course-video.ts";
import { LazyVideo } from "@/elements/lazy-video.ts";
import { NavTabs } from "@/elements/nav-tabs.ts";
import { PremiumButton } from "@/elements/premium-button.tsx";
import { SiteHeader } from "@/elements/site-header.ts";
import { SiteNotificationElement } from "@/elements/site-notification.ts";
import { SiteSearch } from "@/elements/site-search.tsx";
import { ThemeSwitcher } from "@/elements/theme-switcher.tsx";
import { TimeAgo } from "@/elements/time-ago.ts";
import { lazywc, r2wc } from "@/lib/custom-element.ts";
import "../css/front.css";
import { BurgerMenu } from "@/elements/burger-menu.tsx";
import { CodeInput } from "@/elements/code-input.ts";
import { DrawerToggle } from "@/elements/drawer-toggle.ts";
import { HasCompletedElement } from "@/elements/has-completed.ts";
// Temporary fix while waiting this mr (https://github.com/hotwired/turbo/pull/1505), remove idiomorph to when it's merged
import "../../node_modules/@hotwired/turbo/src/index.js";

r2wc("path-detail", () => import("@/elements/path-detail.tsx"), {
  path: "json",
  completednodeids: "json",
});
r2wc("evaluation-questions", () => import("@/elements/evaluation.tsx"), {
  course: "string",
});
r2wc("support-course", () => import("@/elements/support-course.tsx"), {
  course: "string",
});
r2wc("student-importer", () => import("@/elements/student-importer.tsx"), {
  action: "string",
  "example-url": "string",
  credits: "number",
  subject: "json",
  message: "json",
});
r2wc("course-filters", CourseFilters, {});
r2wc("site-search", SiteSearch, {});
r2wc("theme-switcher", ThemeSwitcher, {});
r2wc("burger-menu", BurgerMenu, {}, { append: true });

lazywc("code-block", () => import("@/elements/code-block.ts"));
lazywc("md-editor", () => import("@/elements/md-editor.ts"));

customElements.define("course-video", CourseVideo);
customElements.define("lazy-video", LazyVideo);
customElements.define("course-chapters", CourseChapters);
customElements.define("nav-tabs", NavTabs);
customElements.define("premium-button", PremiumButton);
customElements.define("con-fetti", Confetti);
customElements.define("site-header", SiteHeader);
customElements.define("site-notification", SiteNotificationElement);
customElements.define("time-ago", TimeAgo);
customElements.define("drawer-toggle", DrawerToggle);
customElements.define("has-completed", HasCompletedElement, { extends: "a" });
customElements.define("code-input", CodeInput);
