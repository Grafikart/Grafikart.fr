export type APISearchItem = {
readonly title: string,
readonly url: string,
readonly type: string,
};
export type APISearchResponse = {
readonly items: APISearchItem[],
readonly hits: number,
};
export type AttachmentFileData = {
readonly id: number,
readonly createdAt: string,
readonly name: string,
readonly url: string,
readonly size: number,
readonly thumbnail: string,
};
export type AttachmentUrlData = {
readonly id: number,
readonly url: string,
};
export type BadgeFormData = {
id: number | null,
name: string,
description: string,
position: number,
action: string,
actionCount: number,
theme: string,
image: string | null,
unlockable: boolean,
};
export type BadgeRowData = {
id: number,
name: string,
action: string,
actionCount: number,
};
export type BlogCategoryData = {
readonly id: number | null,
readonly name: string,
readonly slug: string,
};
export type Chapter = {
title: string,
ids: number[],
};
export type ChapterData = {
readonly title: string,
readonly courses: OptionItemData[],
};
export type CommentRowData = {
readonly id: number,
readonly username: string,
readonly email: string,
readonly content: string,
readonly ip: string,
readonly createdAt: string,
};
export type ContactRequestData = {
readonly id: number,
readonly name: string,
readonly email: string,
readonly message: string,
readonly ip: string | null,
readonly createdAt: string,
};
export type ContactRequestRowData = {
readonly id: number,
readonly name: string,
readonly email: string,
readonly message: string,
readonly ip: string | null,
readonly createdAt: string,
};
export type CountryData = {
readonly name: string,
readonly code: string,
};
export type CouponFormData = {
readonly id: string,
readonly months: number,
};
export type CouponRowData = {
readonly id: string,
readonly email: string,
readonly months: number,
readonly claimedAt: string | null,
readonly createdAt: string,
};
export type CourseFilterItem = {
readonly label: string,
readonly value: string,
readonly courses_count: number | null,
readonly formations_count: number | null,
};
export type CourseFilterTypeCount = {
readonly course: number,
readonly formation: number,
};
export type CourseFiltersResponse = {
readonly technologies: CourseFilterItem[],
readonly levels: CourseFilterItem[],
readonly types: CourseFilterTypeCount,
};
export type CourseFormData = {
id: number | null,
title: string,
slug: string,
createdAt: string,
online: boolean,
premium: boolean,
forceRedirect: boolean,
videoPath: string,
demo: string,
youtubeId: string,
duration: number,
deprecatedById: number | null,
content: string,
level: DifficultyLevel,
source: boolean,
attachment: AttachmentUrlData | null,
youtubeThumbnail: AttachmentUrlData | null,
formationId: number | null,
questionsCount: number,
technologies: TechnologyUsageData[] | null,
};
export type CourseRowData = {
readonly id: number,
readonly title: string,
readonly online: boolean,
readonly createdAt: string,
readonly technologies: TechnologyUsageData[],
};
export type CourseViewData = {
type: string,
readonly id: number,
readonly title: string,
readonly video: string,
readonly content: string,
readonly poster: string,
};
export type CursorPaginatedDataCollection<TKey, TValue> = CursorPaginator<TKey, TValue>;
export type CursorPaginator<TKey, TValue> = {
data: TKey extends string ? Record<TKey, TValue> : TValue[],
links: {
url: string | null,
label: string,
active: boolean,
}[],
meta: {
path: string,
per_page: number,
next_cursor: string | null,
next_page_url: string | null,
prev_cursor: string | null,
prev_page_url: string | null,
},
};
export type CursorPaginatorInterface<TKey, TValue> = CursorPaginator<TKey, TValue>;
export type DailyData = {
readonly date: string,
readonly value: number,
};
export type DifficultyLevel = 0 | 1 | 2;
export type FolderData = {
readonly path: string,
readonly count: number,
};
export type FormationChapterData = {
readonly title: string,
readonly courses: FormationCourseData[],
};
export type FormationCourseData = {
readonly url: string,
readonly id: number,
readonly title: string,
readonly slug: string,
readonly duration: number,
};
export type FormationFormData = {
id: number | null,
title: string,
slug: string,
createdAt: string,
online: boolean,
forceRedirect: boolean,
deprecatedBy: number | null,
content: string,
short: string | null,
level: DifficultyLevel,
attachment: AttachmentUrlData | null,
youtubePlaylist: string | null,
links: string | null,
chapters: ChapterData[],
technologies: TechnologyUsageData[] | null,
};
export type FormationRowData = {
readonly id: number,
readonly title: string,
readonly online: boolean,
readonly createdAt: string,
readonly technologies: TechnologyUsageData[],
};
export type FormationViewData = {
type: string,
readonly content: string,
readonly links: string | null,
readonly chapters: FormationChapterData[],
readonly duration: number,
readonly url: string,
};
export type JobItemData = {
id: number,
type: string,
message: string,
exception: string | null,
date: string,
};
export type LengthAwarePaginator<TKey, TValue> = {
data: TKey extends string ? Record<TKey, TValue> : TValue[],
links: {
url: string | null,
label: string,
active: boolean,
}[],
meta: {
total: number,
current_page: number,
first_page_url: string,
from: number | null,
last_page: number,
last_page_url: string,
next_page_url: string | null,
path: string,
per_page: number,
prev_page_url: string | null,
to: number | null,
},
};
export type LengthAwarePaginatorInterface<TKey, TValue> = LengthAwarePaginator<TKey, TValue>;
export type MonthlyData = {
readonly month: number,
readonly year: number,
readonly value: number,
};
export type NotificationCreatedEvent = {
readonly notification: NotificationData,
};
export type NotificationData = {
message: string,
url: string,
date: string,
userId: number | null,
};
export type OptionItemData = {
readonly id: number,
readonly name: string,
};
export type PaginatedDataCollection<TKey, TValue> = LengthAwarePaginator<TKey, TValue>;
export type PathFormData = {
id: number | null,
title: string,
slug: string,
createdAt: string,
online: boolean,
description: string,
tags: string,
nodes: PathNodeData[],
};
export type PathNodeData = {
id: number,
icon: string | null,
title: string | null,
description: string | null,
contentType: string,
contentId: number | null,
x: number,
y: number,
meta: PathNodeMetaData | null,
parents: PathNodeEdgeData[],
};
export type PathNodeEdgeData = {
id: number,
primary: boolean,
};
export type PathNodeMetaData = {
video: string | null,
};
export type PathRowData = {
id: number,
title: string,
online: boolean,
createdAt: string,
description: string,
tags: string,
};
export type PathViewData = {
readonly id: number | null,
readonly title: string,
slug: string,
tags: string,
readonly description: string,
readonly nodes: PathNodeData[],
};
export type PlanData = {
readonly id: number | null,
readonly name: string,
readonly price: number,
readonly duration: number,
readonly stripeId: string,
readonly deletedAt: string | null,
};
export type PostFormData = {
id: number | null,
title: string,
slug: string,
createdAt: string,
categoryId: number | null,
online: boolean,
attachment: AttachmentUrlData | null,
content: string,
};
export type PostRowData = {
readonly id: number,
readonly title: string,
readonly online: boolean,
};
export type QuestionChoicesAnswer = {
choices: string[],
answer: number,
};
export type QuestionData = {
id: number,
question: string,
type: QuestionType,
answer: QuestionChoicesAnswer | QuestionTextAnswer,
};
export type QuestionTextAnswer = {
answer: string,
};
export type QuestionType = 'choice' | 'text';
export type RevisionRowData = {
readonly id: number,
readonly authorName: string,
readonly targetTitle: string,
readonly targetType: string,
readonly state: RevisionStatus,
readonly createdAt: string,
};
export type RevisionShowData = {
readonly id: number,
readonly content: string,
readonly currentContent: string,
readonly state: RevisionStatus,
readonly targetTitle: string,
readonly targetUrl: string,
};
export type RevisionStatus = -1 | 0 | 1;
export type SchoolFormData = {
readonly id: number | null,
readonly name: string,
readonly couponPrefix: string,
readonly credits: number,
readonly emailSubject: string,
readonly emailMessage: string,
readonly owner: OptionItemData | null,
readonly students: SchoolStudentData[],
};
export type SchoolRowData = {
readonly id: number,
readonly name: string,
};
export type SchoolStudentData = {
readonly id: number,
readonly username: string,
readonly email: string,
readonly createdAt: string,
readonly isPremium: boolean,
};
export type SearchResultData = {
readonly id: number,
readonly name: string,
readonly type: string,
readonly url: string,
};
export type SettingsFormData = {
liveAt: string,
};
export type SponsorFormData = {
createdAt: string,
id: number | null,
name: string,
url: string,
content: string,
type: SponsorType,
logo: string | null,
};
export type SponsorRowData = {
id: number,
name: string,
type: SponsorType,
url: string,
};
export type SponsorType = 'affiliation' | 'sponsor';
export type SupportQuestionData = {
readonly id: number,
readonly title: string,
readonly content: string,
readonly answer: string | null,
readonly timestamp: number,
readonly createdAt: string,
readonly me: boolean,
};
export type SupportQuestionFormData = {
id: number,
title: string,
content: string,
answer: string,
online: boolean,
courseId: number,
courseTitle: string,
courseUrl: string,
timestamp: number,
createdAt: string,
};
export type SupportQuestionRowData = {
readonly id: number,
readonly title: string,
readonly courseTitle: string,
readonly online: boolean,
readonly answered: boolean,
readonly createdAt: string,
};
export type TechnologyFormData = {
id: number | null,
name: string,
slug: string,
content: string,
deprecatedBy: OptionItemData | null,
image: string | null,
requirements: OptionItemData[] | null,
};
export type TechnologyRowData = {
id: number,
name: string,
image: string | null,
count: number,
};
export type TechnologyUsageData = {
id: number,
name: string,
version: string | null,
primary: boolean,
};
export type TransactionReportRowData = {
readonly method: string,
readonly month: number,
readonly price: number,
readonly tax: number,
readonly fee: number,
};
export type TransactionRowData = {
readonly id: number,
readonly duration: number,
readonly price: number,
readonly tax: number,
readonly fee: number,
readonly method: string,
readonly refunded: boolean,
readonly createdAt: string,
readonly userId: number,
readonly username: string,
readonly email: string,
};
export type UserRowData = {
readonly id: number,
readonly username: string,
readonly email: string,
readonly createdAt: string,
readonly isPremium: boolean,
readonly isBanned: boolean,
readonly lastLoginIp: string | null,
};
