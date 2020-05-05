import {useState} from 'preact/hooks'
import {FetchForm, FormField, PrimaryButton} from '../Form'
import {Fragment} from 'preact'
import {useToggle} from '@fn/hooks'
import SlideToggle from '../Animation/SlideToggle'
import {Alert} from '../Alert'

function ReportButton ({children, ...props}) {
  return <button className="forum-message__report js-report" {...props}>
    <span>!</span>
    {children}
  </button>
}

function ReportForm ({action, value, onChange, onSuccess}) {
  const placeholder = "Indiquez en quoi ce sujet ne convient pas"
  return <FetchForm value={value} onChange={onChange} className="stack" action={action} method="post" onSuccess={onSuccess}>
        <FormField name="reason" required placeholder={placeholder} autofocus>Raison du signalement</FormField>
        <PrimaryButton>Envoyer</PrimaryButton>
      </FetchForm>
}

export function Report ({endpoint, data}) {
  const [formVisible, toggleForm] = useToggle(false)
  const [success, setSuccess] = useState(false)
  const [value, onChange] = useState({...data, reason: ''})

  return <Fragment>
    {!success && <div className="forum-message__actions">
      <ReportButton style={{marginLeft: 'auto'}} onClick={toggleForm}>Signaler le sujet</ReportButton>
    </div>}
    {success && <Alert type="success" duration={2500}>Merci pour votre signalement</Alert>}
    <SlideToggle visible={formVisible && !success}>
      <ReportForm value={value} action={endpoint} onSuccess={setSuccess} onChange={onChange}></ReportForm>
    </SlideToggle>
  </Fragment>
}
