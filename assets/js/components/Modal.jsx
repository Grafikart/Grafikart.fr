export function Modal ({children, onClose}) {
  return <modal-dialog overlay-close onClose={onClose}>
    <section className="modal-box">
      {children}
    </section>
  </modal-dialog>
}
