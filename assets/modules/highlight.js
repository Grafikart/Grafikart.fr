import scriptjs from "scriptjs";

const lazylangs = ["typescript", "elixir", "less", "stylus", "scss", "sass", "yaml", "twig", "dockerfile"];

/**
 * Ajoute highlightjs sur les éléments sélectionnés
 *
 * @param {NodeList<HTMLElement>} $codes
 */
function highlight($codes) {
  $codes.forEach((code) => {
    code.parentNode.classList.add("with-syntax");
    let lazy = false;
    let cls = code.getAttribute("class");
    if (cls === null) {
      cls = "bash";
    } else {
      cls = code.getAttribute("class").replace("markup", "bash");
    }
    lazylangs.forEach((lang) => {
      if (cls.endsWith(lang)) {
        lazy = true;
        scriptjs(`//cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/languages/${lang}.min.js`, () => {
          window.hljs.highlightBlock(code);
        });
      }
    });
    if (lazy === false) {
      window.hljs.highlightBlock(code);
    }
  });
}

/**
 * Détecte et ajoute la coloration syntaxique sur le site
 */
export function bindHighlight(root = document) {
  const $codes = Array.from(root.querySelectorAll("pre code")).filter((v) => !v.classList.contains("language-mermaid"));
  if ($codes.length > 0) {
    if (window.hljs) {
      highlight($codes);
    } else {
      const link = document.createElement("link");
      link.setAttribute("rel", "stylesheet");
      document.querySelector("head").appendChild(link);
      scriptjs("//cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js", () => {
        window.hljs.configure({ tabReplace: "    " });
        highlight($codes);
      });
    }
  }
}

document.addEventListener("turbolinks:load", () => {
  bindHighlight();
});
