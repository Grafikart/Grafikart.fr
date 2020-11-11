/**
 * Icône basé sur la sprite SVG
 * @param {{name: string}} props
 */
export function Icon ({ name, size }) {
  const className = `icon icon-${name}`
  const href = `/sprite.svg#${name}`
  return (
    <svg className={className} width={size} height={size}>
      <use xlinkHref={href} />
    </svg>
  )
}
