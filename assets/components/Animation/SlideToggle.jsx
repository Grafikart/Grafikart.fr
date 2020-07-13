import { useRef, useState } from 'preact/hooks'
import { useAsyncEffect } from '/functions/hooks.js'

export default function SlideToggle ({ visible, className = null, children }) {
  const [height, setHeight] = useState(0)
  const [showChildren, setShowChildren] = useState(visible)
  const duration = 500
  const containerRef = useRef(null)
  const style = {
    height: height !== null ? `${height}px` : null,
    transition: `${duration}ms`,
    overflow: 'hidden'
  }

  useAsyncEffect(() => {
    if (visible) {
      if (showChildren === false) {
        setShowChildren(true)
        return
      }
      containerRef.current.style.height = null
      const height = containerRef.current.getBoundingClientRect().height
      containerRef.current.style.height = '0px'
      containerRef.current.getBoundingClientRect()
      setHeight(height)
      window.setTimeout(() => {
        setHeight(null)
      }, duration)
    } else if (showChildren === true) {
      const height = containerRef.current.getBoundingClientRect().height
      containerRef.current.style.height = `${height}px`
      containerRef.current.getBoundingClientRect()
      setHeight(0)
      window.setTimeout(() => {
        setShowChildren(false)
      }, duration)
    }
  }, [visible, showChildren])

  return (
    <>
      {showChildren && (
        <div className={className} ref={containerRef} style={style}>
          {children}
        </div>
      )}
    </>
  )
}
