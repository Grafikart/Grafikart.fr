import { FetchForm, FormField, FormPrimaryButton } from '/components/Form.jsx'
import { useClickOutside, useToggle } from '/functions/hooks.js'
import { useRef } from 'preact/hooks'
import { SlideIn } from '/components/Animation/SlideIn.jsx'
import { flash } from '/elements/Alert.js'

/**
 * Bouton de signalement avec formulaire en tooltip
 */
export function ForumReport ({ message, topic }) {
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
