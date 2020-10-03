import { classNames } from '/functions/dom.js'

export function Slider ({ children, slide }) {
  const childrenCount = children.length
  const width = (childrenCount * 100).toString() + '%'
  const transform = `translateX(${((slide - 1) * -100) / childrenCount}%)`

  return (
    <div style={{ overflow: 'hidden' }}>
      <div class='slider' style={{ width, transform }}>
        {children.map((child, i) => (
          <div key={i} class={classNames(i === slide - 1 && 'slider-active')}>
            {child}
          </div>
        ))}
      </div>
    </div>
  )
}
