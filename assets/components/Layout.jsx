export function Stack ({ children, gap }) {
  const style = gap ? `--gap:${gap}` : null
  return (
    <div className='stack' style={style}>
      {children}
    </div>
  )
}

export function Flex ({ children, gap }) {
  const style = gap ? `--gap:${gap}` : null
  return (
    <div className='hstack' style={style}>
      {children}
    </div>
  )
}
