import confetti from "canvas-confetti";
import { windowHeight } from "/functions/window.js";

export class Confetti extends HTMLElement {
  connectedCallback() {
    const rect = this.getBoundingClientRect();
    const y = (rect.top + rect.height / 2) / windowHeight();
    console.log(y);
    confetti({
      particleCount: 100,
      zIndex: 3000,
      spread: 70,
      origin: { y: y },
    });
  }
}
