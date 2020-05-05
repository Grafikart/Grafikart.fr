import SlideToggle from './Animation/SlideToggle'
import {useToggle} from '@fn/hooks'

function AlertIcon ({type}) {
  let icon = 'warning'
  if (type === 'success') {
    icon = 'check'
  }
  const href = '/sprite.svg#' + icon
  return <svg className="icon icon-{$name}">
    <use xlinkHref={href}></use>
  </svg>
}

function AlertClose ({type, ...props}) {
  return <button className="alert-close" {...props}>
    <svg className="icon">
      <use xlinkHref="/sprite.svg#cross"></use>
    </svg>
  </button>
}

function AlertProgress ({duration}) {
  return <div className="alert__progress" style={{animationDuration: duration + 'ms'}}></div>
}

export function Alert ({type, children, duration}) {
  const className = 'alert alert-' + type
  const [visible, toggleVisible] = useToggle(true)
  if (duration && visible) {
    window.setTimeout(function () {
      toggleVisible()
    }, duration)
  }
  return <SlideToggle visible={visible}>
    <div className={className}>
      <AlertIcon type={type}/>
      {children}
      <AlertClose onClick={toggleVisible}/>
      {duration && <AlertProgress duration={duration}/>}
    </div>
  </SlideToggle>
}
