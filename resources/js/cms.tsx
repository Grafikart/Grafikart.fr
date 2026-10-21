import "../css/cms.css"

import { createInertiaApp, type ResolvedComponent } from "@inertiajs/react"
import { QueryClient, QueryClientProvider } from "@tanstack/react-query"
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers"
import { StrictMode } from "react"
import { createRoot } from "react-dom/client"
import { Toaster } from "sonner"
import { Search } from "@/components/search.tsx"

const appName = import.meta.env.VITE_APP_NAME || "Laravel"

const queryClient = new QueryClient()

void createInertiaApp({
  title: (title) => (title ? `${title} - ${appName}` : appName),
  resolve: async (name) => {
    const page = await resolvePageComponent<{ default: ResolvedComponent }>(
      `./pages/${name}.tsx`,
      import.meta.glob<{ default: ResolvedComponent }>("./pages/**/*.tsx"),
    )
    return page.default
  },
  setup({ el, App, props }) {
    const root = createRoot(el)

    root.render(
      <StrictMode>
        <QueryClientProvider client={queryClient}>
          <Toaster position="top-center" richColors closeButton />
          <Search />
          <App {...props} />
        </QueryClientProvider>
      </StrictMode>,
    )
  },
  progress: {
    color: "#4B5563",
  },
})
