import {Fragment} from 'preact'
import {FetchForm, FormField, FormPrimaryButton} from '@comp/Form'
import {useClickOutside, useToggle} from '@fn/hooks'
import {useRef, useState} from 'preact/hooks'
import {SlideIn} from '@comp/Animation/SlideIn'
import {flash} from '@el/Alert'

export function ForumActions ({message, topic}) {
  return <Fragment>
    <ReportButton message={message} topic={topic}/>
  </Fragment>
}

/**
 * Bouton de signalement avec formulaire en tooltip
 */
function ReportButton ({message, topic}) {
  // Hooks
  const ref = useRef(null)
  const [showForm, toggleForm] = useToggle(false)
  const [success, toggleSuccess] = useToggle(false)
  useClickOutside(ref, toggleForm)


  return <div style={{marginLeft: 'auto', position: 'relative'}}>
    <button className="forum-report" onClick={toggleForm} disabled={success}>
      <span>!</span>
    </button>
    <SlideIn show={showForm && !success} className="forum-report__form" forwardedRef={ref}>
      <ReportForm message={message} topic={topic} onSuccess={toggleSuccess}></ReportForm>
    </SlideIn>
  </div>
}

function ReportForm ({onSuccess, message, topic}) {
  const initialData = {reason: 'Ceci est un spam', message: null, topic: null}
  if (message) {
    initialData.message = `/api/forum/messages/${message}`
  } else if (topic) {
    initialData.topic = `/api/forum/topics/${topic}`
  } else {
    console.error('Impossible de charger le forumulaire de signalement')
    return
  }
  const placeholder = 'Indiquez en quoi ce sujet ne convient pas'
  const action = '/api/forum/reports'
  const [value, setValue] = useState(initialData)
  const onReportSuccess = function () {
    setValue(initialData)
    flash('Merci pour votre signalement')
    onSuccess()
  }

  return <FetchForm value={value} onChange={setValue} className="forum-report stack" action={action} onSuccess={onReportSuccess}>
    <FormField type="textarea" name="reason" required placeholder={placeholder} autofocus>Raison du
      signalement</FormField>
    <FormPrimaryButton>Envoyer</FormPrimaryButton>
  </FetchForm>
}
