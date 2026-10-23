import { describe, expect, it } from "vite-plus/test"
import { withLayout, Layout } from "./layout"

describe("withLayout", () => {
  it("resolves breadcrumbs from the page props passed by Inertia", () => {
    const Page = withLayout(() => null, {
      breadcrumb: (props: { item: { title: string } }) => [
        { label: props.item.title },
      ],
      top: "Enregistrer",
    })

    expect(Page.layout({ item: { title: "Question de support" } })).toEqual({
      component: Layout,
      props: {
        breadcrumb: [{ label: "Question de support" }],
        top: "Enregistrer",
      },
    })
  })

  it("preserves static breadcrumbs", () => {
    const breadcrumb = [{ label: "Support" }]
    const Page = withLayout(() => null, { breadcrumb })

    expect(Page.layout({})).toEqual({
      component: Layout,
      props: { breadcrumb, top: undefined },
    })
  })
})
