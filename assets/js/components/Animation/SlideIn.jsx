import {useEffect, useState} from 'preact/hooks'

export function SlideIn ({show, children, style = {}, ...props}) {
  const [shouldRender, setRender] = useState(show)

  useEffect(() => {
    if (show) setRender(true)
  }, [show])

  const onAnimationEnd = () => {
    if (!show) setRender(false)
  }

  return (
    shouldRender && (
      <div
        style={{animation: `${show ? 'slideIn' : 'slideOut'} .3s both`, ...style}}
        onAnimationEnd={onAnimationEnd}
        {...props}
      >
        {children}
      </div>
    )
  )
}
