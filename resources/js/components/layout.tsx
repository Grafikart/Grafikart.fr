import { Link, usePage } from "@inertiajs/react"
import {
  BadgeEuroIcon,
  BookOpenTextIcon,
  BotIcon,
  CogIcon,
  HouseIcon,
  ListVideoIcon,
  MessagesSquareIcon,
  MonitorPlayIcon,
  StarIcon,
  UserIcon,
  WaypointsIcon,
} from "lucide-react"
import {
  type FC,
  Fragment,
  type PropsWithChildren,
  type ReactNode,
  useEffect,
} from "react"
import { toast } from "sonner"
import CommentController from "@/actions/App/Http/Cms/CommentController.ts"
import CourseController from "@/actions/App/Http/Cms/CourseController.ts"
import DashboardController from "@/actions/App/Http/Cms/DashboardController.ts"
import FormationController from "@/actions/App/Http/Cms/FormationController.ts"
import PathController from "@/actions/App/Http/Cms/PathController.ts"
import PlanController from "@/actions/App/Http/Cms/PlanController.ts"
import PostController from "@/actions/App/Http/Cms/PostController.ts"
import SettingsController from "@/actions/App/Http/Cms/SettingsController.ts"
import TechnologyController from "@/actions/App/Http/Cms/TechnologyController.ts"
import TransactionController from "@/actions/App/Http/Cms/TransactionController.ts"
import UserController from "@/actions/App/Http/Cms/UserController.ts"
import { BreadcrumbNav } from "@/components/ui/breadcrumb.tsx"
import { Separator } from "@/components/ui/separator.tsx"
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
} from "@/components/ui/sidebar"
import type { NavItem, SharedData } from "@/types"

type Props = {
  breadcrumb: NavItem[]
  top?: ReactNode
}

export function Layout({ children, ...props }: PropsWithChildren<Props>) {
  const { flash, errors } = usePage<SharedData>().props

  useEffect(() => {
    if (flash) {
      toast[flash.type](flash.message)
    }
  }, [flash])

  useEffect(() => {
    if (Object.keys(errors).length > 0) {
      toast.error("Le formulaire contient des erreurs")
    }
  }, [errors])

  return (
    <SidebarProvider>
      <AppSidebar />
      <SidebarInset>
        <Header {...props} />
        <div className="p-4 lg:p-6">{children}</div>
      </SidebarInset>
    </SidebarProvider>
  )
}

const nav = [
  {
    label: "Dashboard",
    icon: HouseIcon,
    href: DashboardController.index(),
  },
  {
    label: "Contenus",
    children: [
      {
        label: "Tutoriels",
        icon: MonitorPlayIcon,
        href: CourseController.index(),
      },
      {
        label: "Formation",
        icon: ListVideoIcon,
        href: FormationController.index(),
      },
      {
        label: "Parcours",
        icon: WaypointsIcon,
        href: PathController.index(),
      },
      {
        label: "Technologies",
        icon: BotIcon,
        href: TechnologyController.index(),
      },
      {
        label: "Blog",
        icon: BookOpenTextIcon,
        href: PostController.index(),
      },
    ],
  },
  {
    label: "Premium",
    children: [
      {
        label: "Utilisateurs",
        icon: UserIcon,
        href: UserController.index(),
      },
      {
        label: "Transactions",
        icon: BadgeEuroIcon,
        href: TransactionController.index(),
      },
      { label: "Formules", icon: StarIcon, href: PlanController.index() },
    ],
  },
  {
    label: "Communauté",
    children: [
      {
        label: "Commentaires",
        icon: MessagesSquareIcon,
        href: CommentController.index(),
      },
    ],
  },
  {
    label: "Divers",
    children: [
      {
        label: "Paramètres",
        icon: CogIcon,
        href: SettingsController.index(),
      },
    ],
  },
] satisfies NavItem[]

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
  )
}

function AppSidebar() {
  return (
    <Sidebar collapsible="icon" variant="inset">
      <SidebarContent>
        {nav.map((item) => (
          <SidebarItem item={item} key={item.label} root />
        ))}
      </SidebarContent>
      <SidebarFooter />
    </Sidebar>
  )
}

function SidebarItem({ item, root }: { item: NavItem; root?: boolean }) {
  const { url } = usePage()
  if (item.href) {
    const Wrapper = root ? SidebarGroup : Fragment
    return (
      <Wrapper>
        <SidebarMenuItem key={item.label}>
          <SidebarMenuButton
            render={
              <Link
                href={item.href}
                aria-current={url.startsWith(item.href.url)}
              />
            }
          >
            {item.icon && <item.icon />}
            <span>{item.label}</span>
          </SidebarMenuButton>
        </SidebarMenuItem>
      </Wrapper>
    )
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
    )
  }
}

type Extra<Props> = {
  breadcrumb: NavItem[] | ((props: Props) => NavItem[])
  top?: ReactNode
}

export function withLayout<Props>(comp: FC<Props>, extra: Extra<Props>) {
  // @ts-expect-error Inertia specific code
  comp.layout = (page) => (
    <Layout
      top={extra.top}
      breadcrumb={
        Array.isArray(extra.breadcrumb)
          ? extra.breadcrumb
          : extra.breadcrumb(page.props)
      }
    >
      {page}
    </Layout>
  )
  return comp
}
