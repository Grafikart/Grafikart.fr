export class CourseChapters extends HTMLElement {
  connectedCallback() {
    const selectedChapter = this.querySelector("[aria-selected]")
    selectedChapter?.scrollIntoView({
      block: "center",
      // @ts-expect-error container is not recognized
      container: "nearest",
    })
  }
}
