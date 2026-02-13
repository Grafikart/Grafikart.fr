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
