import "react"

declare module "react" {
  namespace JSX {
    interface IntrinsicElements {
      "con-fetti": React.DetailedHTMLProps<
        React.HTMLAttributes<HTMLElement>,
        HTMLElement
      >
    }
  }
}

declare module "https://esm.sh/shiki@3.0.0" {
  export function codeToHtml(
    code: string,
    options: {
      lang: string
      theme?: string
      themes?: Record<string, string>
    },
  ): Promise<string>
}
