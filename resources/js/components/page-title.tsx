import { Head } from "@inertiajs/react"

export function PageTitle({ children }: { children: string }) {
  return <Head title={children} />
}
