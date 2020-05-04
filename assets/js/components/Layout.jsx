export function Stack ({children, gap}) {
  const style = gap ? `--gap:${gap}` : null
  return <div className="stack" style={style}>
    {children}
  </div>
}
