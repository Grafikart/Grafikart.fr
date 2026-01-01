import type { LucideIcon } from "lucide-react";

export * from "./dto";

export type PaginatedData<T> = {
  items: T[];
  page: number;
  last: number;
  links: {
    page: number;
    url: string;
  }[];
};

export type NavItem = {
  label: string;
  href?: string;
  icon?: LucideIcon;
  children?: NavItem[];
};

export type Violation = {
  propertyPath: string;
  title: string;
};
