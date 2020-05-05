import {useState} from 'preact/hooks'
import {FetchForm, Field, PrimaryButton} from '../Form'
import {Fragment} from 'preact'

export function Report ({endpoint, data}) {
  const [success, setSuccess] = useState(false)
  const [value, onChange] = useState({...data, reason: ''})
  const placeholder = "Indiquez en quoi ce sujet ne convient pas"
  return <Fragment>
    {success ?
      <alert-message type="success" duration="2">Merci pour votre signalement</alert-message> :
      <FetchForm value={value} onChange={onChange} className="stack" action={endpoint} method="post" onSuccess={setSuccess}>
        <Field name="reason" required placeholder={placeholder}>Raison du signalement</Field>
        <PrimaryButton>Envoyer</PrimaryButton>
      </FetchForm>
    }
  </Fragment>
}
