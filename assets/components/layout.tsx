import { type FC, Fragment, type PropsWithChildren, type ReactNode } from "react";
import {
  Sidebar,
  SidebarContent,
  SidebarFooter,
  SidebarGroup,
  SidebarGroupLabel,
  SidebarInset,
  SidebarMenu,
  SidebarMenuButton,
  SidebarMenuItem,
  SidebarProvider,
  SidebarTrigger,
} from "@/components/ui/sidebar";
import { HouseIcon, MonitorPlayIcon, StarIcon } from "lucide-react";
import { Link } from "@inertiajs/react";
import { adminPath } from "@/lib/url.ts";
import { Separator } from "@/components/ui/separator.tsx";
import { BreadcrumbNav } from "@/components/ui/breadcrumb.tsx";
import type { NavItem } from "@/types";

type Props = {
  breadcrumb: NavItem[];
  top?: ReactNode;
};

export function Layout({ children, ...props }: PropsWithChildren<Props>) {
  return (
    <SidebarProvider>
      <AppSidebar />
      <SidebarInset>
        <Header {...props} />
        <div className="p-4 lg:p-6">{children}</div>
      </SidebarInset>
    </SidebarProvider>
  );
}

const nav = [
  {
    label: "Dashboard",
    icon: HouseIcon,
    href: "/",
  },
  {
    label: "Contenus",
    children: [
      {
        label: "Tutoriels",
        icon: MonitorPlayIcon,
        href: "/courses",
      },
    ],
  },
  { label: "Premium", children: [{ label: "Formules", icon: StarIcon, href: "/plans" }] },
] satisfies NavItem[];

function Header(props: Props) {
  return (
    <header className="flex h-(--header-height) shrink-0 items-center gap-2 border-b transition-[width,height] ease-linear group-has-data-[collapsible=icon]/sidebar-wrapper:h-(--header-height)">
      <div className="flex w-full items-center gap-1 px-4 lg:gap-2 lg:px-6">
        <SidebarTrigger className="-ml-1" />
        <Separator orientation="vertical" className="mx-2 h-4" />
        {props.breadcrumb && <BreadcrumbNav items={props.breadcrumb} />}
        <div className="ml-auto" />
        {props.top}
      </div>
    </header>
  );
}

function AppSidebar() {
  return (
    <Sidebar variant="inset">
      <SidebarContent>
        {nav.map((item) => (
          <SidebarItem item={item} key={item.label} root />
        ))}
      </SidebarContent>
      <SidebarFooter />
    </Sidebar>
  );
}

function SidebarItem({ item, root }: { item: NavItem; root?: boolean }) {
  if (item.href) {
    const Wrapper = root ? SidebarGroup : Fragment;
    return (
      <Wrapper>
        <SidebarMenuItem key={item.label}>
          <SidebarMenuButton render={<Link href={adminPath(item.href)} />}>
            {item.icon && <item.icon />}
            <span>{item.label}</span>
          </SidebarMenuButton>
        </SidebarMenuItem>
      </Wrapper>
    );
  }

  if (item.children) {
    return (
      <SidebarGroup>
        <SidebarGroupLabel>{item.label}</SidebarGroupLabel>
        <SidebarMenu>
          {item.children.map((item) => (
            <SidebarItem item={item} key={item.label} />
          ))}
        </SidebarMenu>
      </SidebarGroup>
    );
  }
}

type Extra<Props> = {
  breadcrumb: NavItem[] | ((props: Props) => NavItem[]);
  top?: ReactNode;
};

export function withLayout<Props>(comp: FC<Props>, extra: Extra<Props>) {
  // @ts-expect-error Inertia specific code
  comp.layout = function (page) {
    return (
      <Layout
        top={extra.top}
        breadcrumb={Array.isArray(extra.breadcrumb) ? extra.breadcrumb : extra.breadcrumb(page.props)}
      >
        {page}
      </Layout>
    );
  };
  return comp;
}
