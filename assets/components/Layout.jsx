export function Stack ({ children, gap }) {
  const style = gap ? `--gap:${gap}` : null
  return (
    <div class='stack' style={style}>
      {children}
    </div>
  )
}

export function Flex ({ children, gap, center }) {
  const style = {}
  if (gap) {
    style['--gap'] = gap
  }
  if (center) {
    style['align-items'] = 'center'
  }
  return (
    <div class='hstack' style={style}>
      {children}
    </div>
  )
}
