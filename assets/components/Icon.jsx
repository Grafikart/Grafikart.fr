/**
 * Icône basé sur la sprite SVG
 * @param {{name: string}} props
 */
export function Icon ({ name }) {
  const className = `icon icon-${name}`
  const href = `/sprite.svg#${name}`
  return (
    <svg className={className}>
      <use xlinkHref={href} />
    </svg>
  )
}
