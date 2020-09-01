import { createPortal } from 'preact/compat'
import { classNames } from '/functions/dom.js'

export function Modal ({ children, onClose, padding }) {
  const bodyClassName = classNames('modal-box', padding && `p${padding}`)
  return createPortal(
    <modal-dialog overlay-close onClose={onClose}>
      <section className={bodyClassName}>{children}</section>
    </modal-dialog>,
    document.body
  )
}
