import { offsetTop } from "/functions/dom.js";

/**
 * Crée un élément qui va scroller jusqu'à la cible indiquer dans l'attribute data-to
 *
 * ```html
 * <div is="auto-scroll" data-to="[checked]"></div>
 * ```
 */
export class AutoScroll extends HTMLDivElement {
  connectedCallback() {
    const target = document.querySelector(this.dataset.to);
    this.scrollTo(
      0,
      offsetTop(target, this) - target.getBoundingClientRect().height / 2 - this.getBoundingClientRect().height / 2
    );
    target.classList.add("is-selected");
  }
}
