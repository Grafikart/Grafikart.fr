import { Modal } from '/components/Modal.jsx'

export function BadgeUnlock ({ name, description, theme, image }) {
  return (
    <Modal class='badge-modal'>
      <div class={`badge-modal_sprite badge-modal_sprite-${theme}`}>
        <div class='badge-modal_sprite1' />
        <div class='badge-modal_sprite2' />
        <div class='badge-modal_sprite3' />
        <img alt='Gamer' class='badge-modal_icon' src={image} />
      </div>
      <div class='h2 text-center mt3 mb2' style={{ fontWeight: 'normal' }}>
        Vous venez de débloquer le badge <span class='bold'>{name}</span> !
      </div>
      <hr class='my4' />
      <div class='text-muted text-center text-big mb3'>"{description}"</div>
    </Modal>
  )
}
