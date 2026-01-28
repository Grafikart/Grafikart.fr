export type AttachmentFileData = {
    id: number;
    createdAt: string;
    name: string;
    url: string;
    size: number;
    thumbnail: string;
};
export type AttachmentUrlData = {
    id: number;
    url: string;
};
export type BlogCategoryData = {
    id: number | null;
    name: string;
    slug: string;
};
export type Chapter = {
    title: string;
    ids: Array<number>;
};
export type ChapterData = {
    title: string;
    courses: Array<OptionItemData>;
};
export type CommentRowData = {
    id: number;
    username: string;
    email: string;
    content: string;
    ip: string;
    createdAt: string;
};
export type CourseFormData = {
    id: number | null;
    title: string;
    slug: string;
    createdAt: string;
    online: boolean;
    premium: boolean;
    forceRedirect: boolean;
    videoPath: string;
    demo: string;
    youtubeId: string;
    duration: number;
    deprecatedBy: number | null;
    content: string;
    level: DifficultyLevel;
    source: boolean;
    attachment: AttachmentUrlData | null;
    youtubeThumbnail: AttachmentUrlData | null;
    technologies: Array<TechnologyUsageData> | null;
};
export type CourseRowData = {
    id: number;
    title: string;
    online: boolean;
    createdAt: string;
    technologies: Array<TechnologyUsageData>;
};
export type DailyData = {
    date: string;
    value: number;
};
export type DifficultyLevel = 0 | 1 | 2;
export type FolderData = {
    path: string;
    count: number;
};
export type FormationChapterData = {
    title: string;
    courses: Array<FormationCourseData>;
};
export type FormationCourseData = {
    url: string;
    id: number;
    title: string;
    slug: string;
    duration: number;
};
export type FormationFormData = {
    id: number | null;
    title: string;
    slug: string;
    createdAt: string;
    online: boolean;
    forceRedirect: boolean;
    deprecatedBy: number | null;
    content: string;
    short: string | null;
    level: DifficultyLevel;
    attachment: AttachmentUrlData | null;
    youtubePlaylist: string | null;
    links: string | null;
    chapters: Array<ChapterData>;
    technologies: Array<TechnologyUsageData> | null;
};
export type FormationRowData = {
    id: number;
    title: string;
    online: boolean;
    createdAt: string;
    technologies: Array<TechnologyUsageData>;
};
export type FormationViewData = {
    type: string;
    content: string;
    links: string | null;
    chapters: Array<FormationChapterData>;
    duration: number;
};
export type MonthlyData = {
    month: number;
    year: number;
    value: number;
};
export type OptionItemData = {
    id: number;
    name: string;
};
export type PathFormData = {
    id: number | null;
    title: string;
    slug: string;
    description: string;
    nodes: Array<PathNodeData>;
};
export type PathNodeData = {
    id: number;
    icon: string | null;
    title: string | null;
    description: string | null;
    contentType: string;
    contentId: number | null;
    x: number;
    y: number;
    parents: Array<PathNodeEdgeData>;
};
export type PathNodeEdgeData = {
    id: number;
    primary: boolean;
};
export type PathRowData = {
    id: number;
    title: string;
    description: string;
};
export type PathViewData = {
    id: number | null;
    title: string;
    slug: string;
    description: string;
    nodes: Array<PathNodeData>;
};
export type PlanData = {
    id: number | null;
    name: string;
    price: number;
    duration: number;
    stripeId: string;
};
export type PostFormData = {
    id: number | null;
    title: string;
    slug: string;
    createdAt: string;
    categoryId: number | null;
    online: boolean;
    attachment: AttachmentUrlData | null;
    content: string;
};
export type PostRowData = {
    id: number;
    title: string;
    online: boolean;
};
export type SearchResultData = {
    id: number;
    name: string;
    type: string;
    url: string;
};
export type SettingsFormData = {
    liveAt: string;
    spamWords: string;
};
export type TechnologyFormData = {
    id: number | null;
    name: string;
    slug: string;
    content: string;
    image: string | null;
    requirements: Array<OptionItemData> | null;
};
export type TechnologyRowData = {
    id: number;
    name: string;
    image: string | null;
    count: number;
};
export type TechnologyUsageData = {
    id: number;
    name: string;
    version: string | null;
    primary: boolean;
};
export type TransactionRowData = {
    id: number;
    duration: number;
    price: number;
    tax: number;
    fee: number;
    method: string;
    refunded: boolean;
    createdAt: string;
    userId: number;
    username: string;
    email: string;
};
export type UserRowData = {
    id: number;
    username: string;
    email: string;
    createdAt: string;
    isPremium: boolean;
    isBanned: boolean;
    lastLoginIp: string | null;
};
