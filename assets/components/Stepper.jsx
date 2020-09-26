import { cloneElement } from 'preact'
import { Icon } from '/components/Icon.jsx'
import { classNames } from '/functions/dom.js'

/**
 * @param {JSX.Element[]} step
 * @param {number} step
 */
export function Stepper ({ children, step = 0, className }) {
  const steps = children.map((child, index) => {
    const done = index + 1 < step
    const current = index + 1 === step
    return cloneElement(child, { done, current, step: index + 1 })
  })
  return <ul class={classNames('stepper', className)}>{steps}</ul>
}

export function Step ({ children, done, current, step }) {
  let icon = 'lock'
  if (current) {
    icon = 'pen'
  }
  if (done) {
    icon = 'check'
  }

  return (
    <li class={classNames(done && 'stepper_step-done', current && 'stepper_step-current')}>
      <div class='stepper__icon'>
        <Icon name={icon} />
      </div>
      <em>Etape {step}</em>
      <strong>{children}</strong>
    </li>
  )
}
