import {FetchForm, FormField, FormPrimaryButton} from '@comp/Form'
import {Fragment} from 'preact'
import {Alert} from '@comp/Alert'

function ReportButton ({children, ...props}) {
  return <button className="forum-message__report js-report" {...props}>
    <span>!</span>
    {children}
  </button>
}

function ReportForm ({value, onSuccess}) {
  const placeholder = 'Indiquez en quoi ce sujet ne convient pas'
  const action = '/api/forum/reports'
  return <FetchForm value={value} className="stack" action={action} method="post"
                    onSuccess={onSuccess}>
    <FormField type="textarea" name="reason" required placeholder={placeholder} autofocus>Raison du
      signalement</FormField>
    <FormPrimaryButton>Envoyer</FormPrimaryButton>
  </FetchForm>
}

export function Report ({message}) {
  let instructions = 'Signaler le sujet'
  const initialData = {
    reason: ''
  }
  if (message) {
    initialData.message = `/api/forum/messages/${message}`
    instructions = 'Signaler le message'
  }

  return <Fragment>
    <div className="forum-message__buttons">
      <ReportButton style={{marginLeft: 'auto'}}>{instructions}</ReportButton>
    </div>
    <div className="forum-message__report">
      <Alert type="success" duration={2500}>Merci pour votre signalement</Alert>
      <ReportForm value={initialData}/>
    </div>
  </Fragment>
}
