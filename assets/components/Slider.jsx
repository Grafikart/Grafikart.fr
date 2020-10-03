import { classNames } from '/functions/dom.js'
import { useLayoutEffect, useRef } from 'preact/hooks'

export function Slider ({ children, slide }) {
  const height = useRef(null)
  const container = useRef(null)
  const childrenCount = children.length
  const width = `${(childrenCount * 100).toString()}%`
  const transform = `translateX(${((slide - 1) * -100) / childrenCount}%)`

  useLayoutEffect(() => {
    const newHeight = container.current.offsetHeight
    if (height.current) {
      container.current.animate([{ height: `${height.current}px` }, { height: `${newHeight}px` }], {
        duration: 600,
        easing: 'cubic-bezier(.3,.85,.36,.99)'
      })
    }
    height.current = newHeight
  }, [slide])

  return (
    <div style={{ overflow: 'hidden' }} ref={container}>
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
