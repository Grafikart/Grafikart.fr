import { FetchForm, FormField, FormPrimaryButton } from '/components/Form.jsx'
import { useClickOutside, useJsonFetchAndFlash, useToggle } from '/functions/hooks.js'
import { useRef } from 'preact/hooks'
import { SlideIn } from '/components/Animation/SlideIn.jsx'
import { flash } from '/elements/Alert.js'
import { Icon } from '/components/Icon.jsx'
import { RoundedButton } from '/components/Button.jsx'
import { closest } from '/functions/dom.js'

export function ForumActions ({ message, topic }) {
  // On récupère le endpoint à appeler pour la suppresion (ou l'édition)
  let endpoint = null
  if (message) {
    endpoint = `/api/forum/messages/${message}`
  } else if (topic) {
    endpoint = `/api/forum/topics/${topic}`
  } else {
    throw new Error('Impossible de charger le formulaire de signalement')
  }

  // Rendu
  return (
    <>
      <DeleteButton endpoint={endpoint} />
      <ReportButton message={message} topic={topic} />
    </>
  )
}

function DeleteButton ({ endpoint }) {
  // On prépare les hooks
  const { loading: deleteLoading, fetch: deleteFetch } = useJsonFetchAndFlash(endpoint, { method: 'DELETE' })

  // Handler
  const handleDeleteClick = async e => {
    if (!confirm('Voulez vous vraiment supprimer ce message ?')) {
      return
    }
    const message = closest(e.target, '.forum-message')
    await deleteFetch()
    flash('Votre message a bien été supprimé')
    message.remove()
  }

  return (
    <RoundedButton type='danger' title='Supprimer ce message' loading={deleteLoading} onClick={handleDeleteClick}>
      <Icon name='trash' />
    </RoundedButton>
  )
}

/**
 * Bouton de signalement avec formulaire en tooltip
 */
function ReportButton ({ message, topic }) {
  // Hooks
  const ref = useRef(null)
  const [showForm, toggleForm] = useToggle(false)
  const [success, toggleSuccess] = useToggle(false)
  useClickOutside(ref, toggleForm)

  return (
    <div style={{ position: 'relative' }}>
      <button
        className='rounded-button'
        onClick={toggleForm}
        disabled={success}
        aria-label='Signaler le message'
        data-microtip-position='top'
        role='tooltip'
      >
        <span>!</span>
      </button>
      <SlideIn show={showForm && !success} className='forum-report__form' forwardedRef={ref}>
        <ReportForm message={message} topic={topic} onSuccess={toggleSuccess} />
      </SlideIn>
    </div>
  )
}

/**
 * Formulaire de signalement
 */
function ReportForm ({ onSuccess, message, topic }) {
  const data = { message: null, topic: null }
  if (message) {
    data.message = `/api/forum/messages/${message}`
  } else if (topic) {
    data.topic = `/api/forum/topics/${topic}`
  } else {
    throw new Error('Impossible de charger le formulaire de signalement')
  }
  const placeholder = 'Indiquez en quoi ce sujet ne convient pas'
  const action = '/api/forum/reports'
  const onReportSuccess = function () {
    flash('Merci pour votre signalement')
    onSuccess()
  }

  return (
    <FetchForm data={data} className='forum-report stack' action={action} onSuccess={onReportSuccess}>
      <FormField type='textarea' name='reason' required placeholder={placeholder} autofocus>
        Raison du signalement
      </FormField>
      <FormPrimaryButton>Envoyer</FormPrimaryButton>
    </FetchForm>
  )
}
