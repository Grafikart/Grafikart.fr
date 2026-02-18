import { Moon, Sun } from "lucide-react"
import { useRef, useState } from "react"
import { cookie } from "@/lib/cookie.ts"
import { cn } from "@/lib/utils.ts"
import { withViewTransition } from "@/lib/dom.ts"

type Theme = "light" | "dark"

export function ThemeSwitcher() {
  const [theme, setTheme] = useState<Theme>(getInitialTheme)
  const ref = useRef<HTMLButtonElement>(null)

  const toggle = async () => {
    if (!ref.current) {
      return
    }
    const next = theme === "dark" ? "light" : "dark"
    setTheme(next)
    await withViewTransition(() => {
      applyTheme(next)
    })
    const { top, left, width, height } = ref.current.getBoundingClientRect()
    const x = left + width / 2
    const y = top + height / 2
    const right = window.innerWidth - left
    const bottom = window.innerHeight - top
    const maxRadius = Math.hypot(Math.max(left, right), Math.max(top, bottom))
    document.documentElement.animate(
      {
        clipPath: [
          `circle(0px at ${x}px ${y}px)`,
          `circle(${maxRadius}px at ${x}px ${y}px)`,
        ],
      },
      {
        duration: 500,
        easing: "ease-in-out",
        pseudoElement: "::view-transition-new(root)",
      },
    )
    cookie("appearance", next, { expires: 365 })
  }

  return (
    <button
      ref={ref}
      type="button"
      onClick={toggle}
      aria-label="Changer de thème"
      className="relative inline-flex h-7 w-14 items-center rounded-full border bg-border/50 transition-colors"
    >
      <span
        className={cn(
          "absolute left-0.5 flex size-6 items-center justify-center rounded-full transition-transform",
          theme === "dark" &&
            "translate-x-7 bg-primary text-primary-foreground",
          theme === "light" && "bg-background-light text-[#FF9A00]",
        )}
      >
        {theme === "dark" ? (
          <Moon className="size-3.5" />
        ) : (
          <Sun className="size-3.5" />
        )}
      </span>
    </button>
  )
}

function getSystemTheme(): Theme {
  return window.matchMedia("(prefers-color-scheme: dark)").matches
    ? "dark"
    : "light"
}

function getInitialTheme(): Theme {
  const saved = cookie("appearance")
  if (saved === "light" || saved === "dark") {
    return saved
  }
  return getSystemTheme()
}

function applyTheme(theme: Theme) {
  document.documentElement.classList.toggle("dark", theme === "dark")
  document.documentElement.classList.toggle("light", theme === "light")
}
