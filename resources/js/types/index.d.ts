import { type UrlMethodPair } from "@inertiajs/core"

export * from "./generated.d.ts"

export interface Auth {
  user: User
}

export interface BreadcrumbItem {
  title: string
  href: string
}

export interface NavGroup {
  title: string
  items: NavItem[]
}

export type NavItem = {
  label: string
  href?: UrlMethodPair
  icon?: LucideIcon
  children?: NavItem[]
}

export type Flash = {
  type: "success" | "error" | "info"
  message: string
}

export interface SharedData {
  name: string
  auth: Auth
  sidebarOpen: boolean
  flash?: Flash
  errors: Record<string, string>
  [key: string]: unknown
}

export interface User {
  id: number
  name: string
  email: string
  avatar?: string
  email_verified_at: string | null
  two_factor_enabled?: boolean
  created_at: string
  updated_at: string
  [key: string]: unknown // This allows for additional properties...
}

export interface PaginatedData<T> {
  data: T[]
  current_page: number
  first_page_url: string | null
  from: number | null
  last_page: number
  last_page_url: string | null
  next_page_url: string | null
  path: string
  per_page: number
  prev_page_url: string | null
  to: number | null
  total: number
  links: Array<{
    url: string | null
    label: string
    active: boolean
  }>
}
