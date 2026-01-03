export type CourseFormData = {
url: string;
id: number;
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
technologies: Array<TechnologyData>;
image: string;
youtubeThumbnail: string;
};
export type CourseListItemData = {
id: number;
title: string;
url: string;
createdAt: string;
isOnline: boolean;
hasSource: boolean;
technologies: Array<TechnologyListItemData>;
};
export type OptionResource = {
id: number;
name: string;
};
export type TechnologyData = {
id: number;
secondary: boolean;
version: string | null;
name: string;
};
export type TechnologyListItemData = {
name: string;
url: string;
};
