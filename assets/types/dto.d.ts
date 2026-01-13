export type AttachmentFileData = {
id: number;
createdAt: number;
name: string;
size: number;
url: any;
thumbnail: string;
};
export type ContentTechnologyDTO = {
id: number;
primary: boolean;
version: string | null;
name: string;
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
technologies: Array<ContentTechnologyDTO>;
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
export type OptionResource = {
id: number;
name: string;
};
export type PlanFormData = {
name: string;
price: number;
duration: number;
stripeId: string | null;
};
export type PlanItemData = {
id: number;
name: string;
price: number;
duration: number;
stripeId: string | null;
};
export type TechnologyFormData = {
id: number | null;
name: string;
slug: string;
content: string;
image: string | null;
requirements: Array<OptionResource>;
};
export type TechnologyItemData = {
id: number;
name: string;
image: string | null;
tutorialCount: number;
};
export type TechnologyListItemData = {
name: string;
url: string;
};
export type TransactionItemData = {
id: number;
price: number;
tax: number;
duration: number;
method: string;
refunded: boolean;
createdAt: string;
userId: number;
username: string;
email: string;
};
export type UserItemData = {
id: number;
username: string;
email: string;
createdAt: string;
isPremium: boolean;
isBanned: boolean;
lastLoginIp: string | null;
};
