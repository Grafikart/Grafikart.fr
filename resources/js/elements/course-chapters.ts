export class CourseChapters extends HTMLElement {
    connectedCallback() {
        const selectedChapter = this.querySelector('[aria-selected]');
        selectedChapter?.scrollIntoView({
            block: 'center',
            container: 'nearest',
        });
    }
}
