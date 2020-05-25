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

export function RoundedButton ({children, loading = false, type = '', title = '', ...props}) {
  return <button className={"rounded-button " + type} aria-label={title} data-microtip-position="top" role="tooltip" {...props}>
    <span>{loading ? children : <spinning-dots className="icon"/>}</span>
  </button>
}
