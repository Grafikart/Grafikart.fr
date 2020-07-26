export function Spinner ({ width = 16, margin = 10 }) {
  const style = {
    display: 'block',
    width: `${width}px`,
    height: `${width}px`,
    margin: `${margin}px auto`
  }
  return <spinning-dots style={style} />
}
