export function PrimaryButton ({children, ...props}) {
  return <button className="btn btn-primary" {...props}>
    {children}
  </button>
}

export function SecondaryButton ({children, ...props}) {
  return <button className="btn btn-secondary" {...props}>
    {children}
  </button>
}
