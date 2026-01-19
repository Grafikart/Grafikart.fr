export type AttachmentFileData = {
id: number;
createdAt: any;
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
createdAt: any;
};
export type CourseFormData = {
id: number | null;
title: string;
slug: string;
createdAt: any;
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
technologies: Array<TechnologyUsageData> | Array<any> | null;
};
export type CourseRowData = {
id: number;
title: string;
online: boolean;
createdAt: string;
technologies: Array<TechnologyUsageData> | Array<any>;
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
export type FormationFormData = {
id: number | null;
title: string;
slug: string;
createdAt: any;
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
technologies: { [key: number]: TechnologyUsageData } | Array<any> | null;
};
export type FormationRowData = {
id: number;
title: string;
online: boolean;
createdAt: string;
technologies: { [key: number]: TechnologyUsageData } | Array<any>;
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
createdAt: any;
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
requirements: Array<OptionItemData> | Array<any> | null;
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
createdAt: any;
userId: number;
username: string;
email: string;
};
export type UserRowData = {
id: number;
username: string;
email: string;
createdAt: any;
isPremium: boolean;
isBanned: boolean;
lastLoginIp: string | null;
};
