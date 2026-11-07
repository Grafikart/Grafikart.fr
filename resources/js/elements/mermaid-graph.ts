import { mount } from "pele"
import { LazyComponent } from "@/lib/custom-element.ts"

/**
 * Draw a mermaid graph in SVG
 */
export default class MermaidGraph extends LazyComponent {
  onMount() {
    const source = this.el.textContent ?? ""
    const graph = mount(this.el, source, {
      onError: (error) => {
        this.el.textContent = source
        console.error("Impossible de rendre le schéma Mermaid", error)
      },
    })
    this.graph = graph
  }

  private graph?: ReturnType<typeof mount>

  onUnmount() {
    if (this.graph) {
      this.graph.destroy()
      this.el.textContent = this.graph.source
    }
  }
}
