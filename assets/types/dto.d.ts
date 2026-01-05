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
export type FolderData = {
path: string;
count: number;
};
export type OptionResource = {
id: number;
name: string;
};
export type TechnologyListItemData = {
name: string;
url: string;
};
