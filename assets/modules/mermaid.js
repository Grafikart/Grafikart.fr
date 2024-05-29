
export function registerMermaid () {

  document.addEventListener('turbolinks:load', () => {
    const diagrams = document.querySelectorAll('.language-mermaid:not([data-processed="true"]')
    if (diagrams.length > 0) {
      import('mermaid').then(({default: Mermaid}) => {
        Mermaid.startOnLoad = false
        Mermaid.mermaidAPI.initialize({
          securityLevel: 'loose',
          theme: document.getElementById('theme-switcher').checked ? 'dark' : 'neutral',
        });
        Mermaid.run({
          nodes: diagrams
        })
      })
      diagrams.forEach(v => v.parentElement.style.setProperty('border', 'none'))
    }
  })
}
