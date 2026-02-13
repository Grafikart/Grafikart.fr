import type { ComponentProps } from "react"

export function Logo(props: ComponentProps<"svg">) {
  return (
    <svg
      fill="none"
      xmlns="http://www.w3.org/2000/svg"
      viewBox="0 0 14 14"
      {...props}
    >
      <path
        fill="currentColor"
        d="M12.2 2.8A95.7 95.7 0 0 0 7 5.6 100.7 100.7 0 0 0 0 2l.6 8.4C2.8 11.5 4.9 12.7 7 14a152.5 152.5 0 0 1 4.8-2.7l1.6-.9L14 2l-1.7.8Zm-.3 6.8-3.3 1.8V9.7L12 7.9v1.7Zm.1-3.4A73.8 73.8 0 0 0 7 9a57.8 57.8 0 0 0-3.3-1.8v1.7l1.7 1v1.6L2.1 9.6l-.2-5 5 2.6H7l5-2.7v1.7Z"
      />{" "}
    </svg>
  )
}
