export function Icon({name}) {
  const className = `icon icon-${name}`
  const href = `/sprite.svg#${name}`
  return <svg className={className}><use xlinkHref={href}></use></svg>
}
