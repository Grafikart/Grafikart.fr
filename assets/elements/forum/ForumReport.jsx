import { FetchForm, FormField, FormPrimaryButton } from '/components/Form.jsx'
import { useClickOutside, useToggle } from '/functions/hooks.js'
import { useRef } from 'preact/hooks'
import { SlideIn } from '/components/Animation/SlideIn.jsx'
import { flash } from '/elements/Alert.js'
import { isAuthenticated, getUserId } from '/functions/auth.js'

/**
 * Bouton de signalement avec formulaire en tooltip
 */
export function ForumReport ({ message, topic, owner }) {
  // Hooks
  const ref = useRef(null)
  const [showForm, toggleForm] = useToggle(false)
  const [success, toggleSuccess] = useToggle(false)
  useClickOutside(ref, toggleForm)

  if (!isAuthenticated() || getUserId() === parseInt(owner, 10)) {
    return null
  }

  return (
    <div style={{ position: 'relative' }}>
      <button
        className='rounded-button warning'
        onClick={toggleForm}
        disabled={success}
        title={message ? 'Signaler le message' : 'Signaler le sujet'}
      >
        !
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
    data.message = message
  } else if (topic) {
    data.topic = topic
  } else {
    throw new Error('Impossible de charger le formulaire de signalement')
  }
  const placeholder = `Indiquez en quoi ce ${message ? 'message' : 'sujet'} ne convient pas`
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
