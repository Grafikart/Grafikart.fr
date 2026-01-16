export type AttachmentFileData = {
id: number;
createdAt: number;
name: string;
size: number;
url: any;
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
export type CourseAttachmentData = {
id: number;
url: string;
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
level: number;
source: boolean;
technologies: Array<any>;
image: CourseAttachmentData | null;
youtubeThumbnail: CourseAttachmentData | null;
};
export type CourseListItemData = {
id: number;
title: string;
url: string;
createdAt: string;
online: boolean;
technologies: Array<TechnologyListItemData>;
};
export type DailyData = {
date: string;
value: number;
};
export type FolderData = {
path: string;
count: number;
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
category: number | null;
online: boolean;
image: AttachmentUrlData | null;
content: string;
categories: Array<OptionItemData>;
};
export type PostRowData = {
id: number;
title: string;
online: boolean;
};
export type TechnologyFormData = {
id: number | null;
name: string;
slug: string;
content: string;
image: string | null;
requirements: Array<OptionItemData>;
};
export type TechnologyListItemData = {
name: string;
url: string;
};
export type TechnologyRowData = {
id: number;
name: string;
image: string | null;
tutorialCount: number;
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
